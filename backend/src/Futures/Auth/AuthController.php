<?php
namespace App\Futures\Auth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;

class AuthController
{
  public function oauthUrl(Request $request, Response $response)
  {
    $oauthUrl = AuthService::getOauthUrl();

    return ApiResponseHelper::successResponse($response, ['url' => $oauthUrl], "OAuth URL generated successfully");
  }
}
