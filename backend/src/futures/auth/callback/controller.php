<?php
use App\Config\Config;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../config/env.php';

class GithubCallbackController {
  public function callback(Request $request, Response $response) {
    // token交換処理やら

    return ApiResponseHelper::successResponse($response, $request->getQueryParams(), "Callback successful");

    $config = Config::server();

    $frontendUrl = $config['frontend']['base_url'];
    $redirectUrl = $frontendUrl . '/_auth/callback?token=' . urlencode('dummy_token');

    return ApiResponseHelper::redirect($response, $redirectUrl);

  }
}