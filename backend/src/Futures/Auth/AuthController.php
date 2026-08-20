<?php

namespace App\Futures\Auth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Libraries\Crypto;
use App\Config\Config;

class AuthController
{
  public function oauthUrl(Request $_, Response $response)
  {
    $oauthUrl = AuthService::getOauthUrl();
    if (!$oauthUrl) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, "/auth", "Failed to generate OAuth URL");
    }

    return ApiResponseHelper::successResponse($response, ['url' => $oauthUrl], "OAuth URL generated successfully");
  }

  public function refresh(Request $request, Response $response)
  {
    $user = $request->getAttribute('user');
    
    if (!$user) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, "/auth/refresh", "User not authenticated");
    }

    try {
      $newAccessToken = $user->refresh($request->getServerParams()['REMOTE_ADDR'], $request->getHeaderLine('User-Agent'));
      return ApiResponseHelper::successResponse($response, ['access_token' => $newAccessToken], "Access token refreshed successfully");
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, "/auth/refresh", "Failed to refresh access token");
    }
  }
}
