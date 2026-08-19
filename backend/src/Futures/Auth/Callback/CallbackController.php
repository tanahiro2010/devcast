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

    if (!$code || !$state) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::BAD_REQUEST, ErrorCode::MISSING_CODE_OR_STATE, '/auth/callback');
    }

    try {
      $credentials = $this->callbackService->exchangeToken($code, $state);
      
      if (!isset($credentials['access_token'])) {
        return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::TOKEN_EXCHANGE_FAILED, '/auth/callback');
      }
      
      $profile = $this->callbackService->getProfile($credentials['access_token']);
      $user = $this->callbackService->findOrCreateUser('github', $profile);
      
      $credential =$user->createCredential(
        'github',
        $credentials['access_token'], 
        $credentials['refresh_token'] ?? null, 
        $credentials['expires_in'] ?? null, 
        $credentials['scope'] ?? null, 
        $credentials['token_type'] ?? 'Bearer'
      );

      $session = $user->createSession(
        Crypto::generateRandomString(16), // session_id
        $request->getServerParams()['REMOTE_ADDR'] ?? null, // ip_address
        $request->getHeaderLine('User-Agent') ?? null, // user_agent
        date('Y-m-d H:i:s', strtotime('+7 days')) // expires_at
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
      return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::TOKEN_EXCHANGE_FAILED, '/auth/callback', $e->getMessage());
    }
  }
}
