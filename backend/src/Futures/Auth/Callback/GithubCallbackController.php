<?php
namespace App\Futures\Auth\Callback;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Config\Config;
use App\Models\Response\Status as ErrorStatus;
use App\Models\Response\Code as ErrorCode;
use App\Helpers\ApiResponseHelper;

class GithubCallbackController {
  public function callback(Request $request, Response $response) {
    // token交換処理やら
    $params = $request->getQueryParams();
    $code = $params['code'] ?? null;
    $state = $params['state'] ?? null;

    if (!$code || !$state) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::BAD_REQUEST, ErrorCode::MISSING_CODE_OR_STATE, '/auth/callback');
    }

    $credentials = CallbackService::exchangeToken($code, $state);

    $accessToken = $credentials['access_token'] ?? null;
    $refreshToken = $credentials['refresh_token'] ?? null;
    $expiresIn = $credentials['expires_in'] ?? null;
    $refreshTokenExpiresIn = $credentials['refresh_token_expires_in'] ?? null;
    $tokenType = $credentials['token_type'] ?? null;
    $scope = $credentials['scope'] ?? null;




    return ApiResponseHelper::successResponse($response, ['credentials' => $credentials], "Callback successful");


    // return ApiResponseHelper::successResponse($response, $request->getQueryParams(), "Callback successful");

    $config = Config::server();

    $frontendUrl = $config['frontend']['base_url'];
    $redirectUrl = $frontendUrl . '/_auth/callback?token=' . urlencode('dummy_token');

    return ApiResponseHelper::redirect($response, $redirectUrl);
  }
}
