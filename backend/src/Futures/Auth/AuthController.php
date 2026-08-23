<?php

namespace App\Futures\Auth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;
use App\Models\DB\RefreshToken;
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

  public function refresh(Request $request, Response $response)
  {
    /** @var User $users */
    $user = $request->getAttribute('user');

    if (!$user) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, "/auth/refresh", "User not authenticated");
    }

    try {
      $user->deleteAllSessions();
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, "/auth/refresh", "Failed to delete existing sessions: " . $e->getMessage());
    }

    $newAccessToken = $user->refresh($request->getServerParams()['REMOTE_ADDR'], $request->getHeaderLine('User-Agent'));
    $newRefreshToken = $user->refreshToken();
    return ApiResponseHelper::successResponse($response, ['access_token' => $newAccessToken, 'refresh_token' => $newRefreshToken], "Access token refreshed successfully");
  }
}
