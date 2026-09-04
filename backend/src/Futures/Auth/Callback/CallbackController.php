<?php

namespace App\Futures\Auth\Callback;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Response\Status as ErrorStatus;
use App\Models\Response\Code as ErrorCode;
use App\Config\Config;
use App\Helpers\ApiResponseHelper;
use App\Libraries\Crypto;
use App\Libraries\Algorithm;
use App\Futures\Auth\AuthService;



class CallbackController
{
    private CallbackService $callbackService;

    public function __construct()
    {
        $this->callbackService = new CallbackService();
    }

    public function callback(Request $request, Response $response)
    {
        // token交換処理やら
        $params = $request->getQueryParams();
        $code = $params['code'] ?? null;
        $state = $params['state'] ?? null;

        if (!is_string($code) || $code === '' || !is_string($state) || $state === '') {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::MISSING_CODE_OR_STATE);
        }

        if (!AuthService::consumeIssuedState($state)) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::MISSING_CODE_OR_STATE, "Invalid or expired state");
        }

        try {
            $credentials = $this->callbackService->exchangeToken($code, $state);

            if (!isset($credentials['access_token'])) {
                return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::TOKEN_EXCHANGE_FAILED);
            }

            $profile = $this->callbackService->getProfile($credentials['access_token']);
            $user = $this->callbackService->findOrCreateUser('github', $profile);
            // credentials.token_expires_at はGitHub側のアクセストークンの有効期限であり、
            // アプリのセッション有効期限とは独立して管理する(GitHubがexpires_inを返さない場合でも
            // セッションが無期限にならないようにするため)。
            $credentialExpiredAt = isset($credentials['expires_in']) ? date('Y-m-d H:i:s', time() + $credentials['expires_in']) : null;

            $credential =$user->createCredential(
                'github',
                $credentials['access_token'],
                $credentials['refresh_token'] ?? null,
                $credentialExpiredAt,
                $credentials['scope'] ?? null,
                $credentials['token_type'] ?? 'Bearer'
            );

            $sessionExpiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
            $session = $user->createSession(
                Crypto::generateRandomString(16), // session_id
                $request->getServerParams()['REMOTE_ADDR'] ?? null, // ip_address
                $request->getHeaderLine('User-Agent') ?? null, // user_agent
                $sessionExpiresAt // expires_at
            );

            $jwtToken = Crypto::jwtEncode([
                'sub' => $user->get('id'),
                'iss' => $session->get('session_id'),
                'iat' => time(),
                'exp' => time() + 3600, // 1 hour expiration
            ], Config::env('JWT_SECRET'), Algorithm::HS256);

            $config = Config::server();
            $frontendUrl = $config['frontend']['base_url'];
            $redirectUrl = $frontendUrl . '/_auth/callback?token=' . urlencode($jwtToken);

            return ApiResponseHelper::redirect($response, $redirectUrl);
        } catch (\Exception $e) {
            error_log('[auth/callback] token exchange failed: ' . $e->getMessage());
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::TOKEN_EXCHANGE_FAILED);
        }
    }
}
