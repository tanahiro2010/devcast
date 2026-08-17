<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Config\Config;
use App\Models\Response\Status as ErrorStatus;
use App\Models\Response\Code as ErrorCode;

define('__ROOT__', __DIR__ . '/../../..');

require_once __DIR__  . '/service.php';
require_once __ROOT__ . '/helpers/response.php';
require_once __ROOT__ . '/config/env.php';

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

    return ApiResponseHelper::successResponse($response, ['credentials' => $credentials], "Callback successful");


    // return ApiResponseHelper::successResponse($response, $request->getQueryParams(), "Callback successful");

    $config = Config::server();

    $frontendUrl = $config['frontend']['base_url'];
    $redirectUrl = $frontendUrl . '/_auth/callback?token=' . urlencode('dummy_token');

    return ApiResponseHelper::redirect($response, $redirectUrl);
  }
}