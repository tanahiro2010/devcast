<?php

namespace App\Futures\Auth\AccessToken;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;
use App\Models\DB\RefreshToken;

class AccessTokenController
{
  public function accessToken(Request $request, Response $response)
  {
    $refreshTokenValue = $request->getQueryParams()['refresh_token'] ?? null;

    if (!$refreshTokenValue) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::BAD_REQUEST, ErrorCode::MISSING_REQUIRED_FIELDS, "/auth/token/access_token", "Missing refresh_token");
    }

    $refreshToken = RefreshToken::findByToken($refreshTokenValue);

    if (!$refreshToken || $refreshToken->isTokenExpired()) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::UNAUTHORIZED, ErrorCode::TOKEN_EXPIRED, "/auth/token/access_token", "Invalid or expired refresh token");
    }

    /** @var User|null $user */
    $user = $refreshToken->user();

    if (!$user) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, "/auth/token/access_token", "User not found for this refresh token");
    }

    $newAccessToken = $user->refresh($request->getServerParams()['REMOTE_ADDR'] ?? '', $request->getHeaderLine('User-Agent'));

    return ApiResponseHelper::successResponse($response, ['access_token' => $newAccessToken], "Access token refreshed successfully");
  }
}
