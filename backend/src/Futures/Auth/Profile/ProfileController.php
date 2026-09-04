<?php

namespace App\Futures\Auth\Profile;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;

class ProfileController
{
  public function getProfile(Request $request, Response $response)
  {
    /** @var User $user */
    $user = $request->getAttribute('user');

    if (!$user) {
      return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, "User not found");
    }

    try {
      $profile = ProfileService::getProfile($user);

      return ApiResponseHelper::successResponse($response, ['profile' => $profile], "Profile retrieved successfully");
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, "Failed to retrieve profile: " . $e->getMessage());
    }
  }
}
