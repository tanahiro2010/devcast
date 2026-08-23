<?php

namespace App\Futures\Auth\RefreshToken;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;

class RefreshTokenController
{
  public function refresh(Request $request, Response $response)
  {
    /** @var User $user */
    $user = $request->getAttribute('user');

    if (!$user) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, "/auth/token/refresh_token", "User not authenticated");
    }

    try {
      $user->deleteAllSessions();
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, "/auth/token/refresh_token", "Failed to delete existing sessions: " . $e->getMessage());
    }

    $newAccessToken = $user->refresh($request->getServerParams()['REMOTE_ADDR'], $request->getHeaderLine('User-Agent'));
    $newRefreshToken = $user->refreshToken();
    return ApiResponseHelper::successResponse($response, ['access_token' => $newAccessToken, 'refresh_token' => $newRefreshToken], "Access token refreshed successfully");
  }
}
