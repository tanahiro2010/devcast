<?php
namespace App\Futures\Auth;

use App\Config\Config;
use App\Database\Database;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthService
{
    private const STATE_TTL_SECONDS = 600; // 10分

    public static function getOauthUrl(): string
    {
        $oauthConfig = Config::oauth();

        $state = bin2hex(random_bytes(16));
        $db = (new Database())->getInstance();

        // 期限切れの発行済みstateはここで掃除しておく(専用のバッチ処理は用意しない)。
        $db->table('oauth_states')->where('expires_at', '<', date('Y-m-d H:i:s'))->delete();

        $db->table('oauth_states')->insert([
            'state' => $state,
            'expires_at' => date('Y-m-d H:i:s', time() + self::STATE_TTL_SECONDS),
        ]);

        $queries = [
            "client_id" => $oauthConfig['github']['client_id'],
            "redirect_uri" => $oauthConfig['github']['redirect_uri'],
            "scope" => implode(" ", $oauthConfig['github']['scope']),
            "state" => $state
        ];

        return "https://github.com/login/oauth/authorize?" . http_build_query($queries);
    }

    /**
     * getOauthUrl() で発行したstateかどうかを1回限り検証する(検証後は必ず破棄する)。
     *
     * /auth はフロントエンド(app.devcast.work)からクロスオリジンのfetchで呼ばれるため、
     * PHPネイティブセッション(Cookie)には依存できない(credentials未設定のfetchでは
     * Set-Cookieがブラウザに保存されない)。そのためstateはDBに永続化して検証する。
     */
    public static function consumeIssuedState(string $state): bool
    {
        $db = (new Database())->getInstance();

        $row = $db->table('oauth_states')->where('state', $state)->first();
        if ($row === null) {
            return false;
        }

        $db->table('oauth_states')->where('state', $state)->delete();

        return strtotime($row->expires_at) >= time();
    }
}
