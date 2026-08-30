<?php

namespace App\Futures\Auth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;

class AuthController
{
  public function oauthUrl(Request $_, Response $response)
  {
    $oauthUrl = AuthService::getOauthUrl();
    if (!$oauthUrl) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, "/auth", "Failed to generate OAuth URL");
    }

    // state はリクエストごとに一意である必要があるため、プロキシ/CDN等にキャッシュされてはならない。
    return ApiResponseHelper::successResponse($response, ['url' => $oauthUrl], "OAuth URL generated successfully")
      ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
      ->withHeader('Pragma', 'no-cache');
  }
}
