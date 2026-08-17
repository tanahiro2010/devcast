<?php
namespace App\Futures\Auth\Callback;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Config\Config;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Status as ErrorStatus;
use App\Models\Response\Code as ErrorCode;
use App\Models\DB\Credential;
use App\Models\DB\User;

class CallbackController {
  private CallbackService $callbackService;

  public function __construct()
  {
    $this->callbackService = new CallbackService();
  }

  public function callback(Request $request, Response $response) {
    // token交換処理やら
    $params = $request->getQueryParams();
    $code = $params['code'] ?? null;
    $state = $params['state'] ?? null;

    if (!$code || !$state) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::BAD_REQUEST, ErrorCode::MISSING_CODE_OR_STATE, '/auth/callback');
    }

    $credentials = $this->callbackService->exchangeToken($code, $state);

    $accessToken = $credentials['access_token'] ?? null;
    $refreshToken = $credentials['refresh_token'] ?? null;
    $expiresIn = $credentials['expires_in'] ?? null;
    $refreshTokenExpiresIn = $credentials['refresh_token_expires_in'] ?? null;
    $tokenType = $credentials['token_type'] ?? null;
    $scope = $credentials['scope'] ?? null;

    $profile = $this->callbackService->getProfile($accessToken);

    return ApiResponseHelper::successResponse($response, ['credentials' => $credentials, 'profile' => $profile], "Callback successful");

    

    User::create([
      'username' => 'dummy_user',
      'email' => 'dummy_user@example.com'
    ]);

    Credential::create([
      'access_token' => $accessToken,
      'refresh_token' => $refreshToken,
      'token_expires_at' => date('Y-m-d H:i:s', time() + $expiresIn),
      'scope' => $scope,
      'token_type' => $tokenType,
    ]);



    return ApiResponseHelper::successResponse($response, ['credentials' => $credentials], "Callback successful");


    // return ApiResponseHelper::successResponse($response, $request->getQueryParams(), "Callback successful");

    $config = Config::server();

    $frontendUrl = $config['frontend']['base_url'];
    $redirectUrl = $frontendUrl . '/_auth/callback?token=' . urlencode('dummy_token');

    return ApiResponseHelper::redirect($response, $redirectUrl);
  }
}
