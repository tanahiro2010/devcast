<?php
namespace App\Futures\Auth;

use App\Config\Config;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthService {
  public static function getOauthUrl() {
    $oauthConfig = Config::oauth();

    $queries = [
      "client_id" => $oauthConfig['github']['client_id'],
      "redirect_uri" => $oauthConfig['github']['redirect_uri'],
      "scope" => implode(" ", $oauthConfig['github']['scope']),
      "state" => bin2hex(random_bytes(16))
    ];

    return "https://github.com/login/oauth/authorize?" . http_build_query($queries);
  }
}
