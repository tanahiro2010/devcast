<?php

namespace App\Futures\Auth\Profile;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;
use App\Models\DB\RefreshToken;

class ProfileController
{
  public function getProfile(Request $request, Response $response)
  {
    /** @var User $user */
    $user = $request->getAttribute('user');

    if (!$user) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, "/auth/profile", "User not found");
    }

    try {
      $profile = ProfileService::getProfile($user);

      return ApiResponseHelper::successResponse($response, ['profile' => $profile], "Profile retrieved successfully");
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, "/auth/profile", "Failed to retrieve profile: " . $e->getMessage());
    }
  }
}
