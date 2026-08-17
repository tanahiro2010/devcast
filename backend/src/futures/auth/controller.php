<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../config/env.php';
require __DIR__ . '/service.php';

class AuthController
{
  public function oauthUrl(Request $request, Response $response)
  {
    $oauthUrl = AuthService::getOauthUrl();

    return ApiResponseHelper::successResponse($response, ['url' => $oauthUrl], "OAuth URL generated successfully");
  }
}
