<?php
namespace App\Futures\Auth\Profile;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;
use App\Models\DB\RefreshToken;

class ProfileController {
  public function getProfile(Request $request, Response $response) {
    /** @var User $user */
    $user = $request->getAttribute('user');

    if (!$user) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, "/auth/profile", "User not found");
    }

    $profile = [
      'id' => $user->id,
      'username' => $user->username,
      'email' => $user->email,
      'plan' => $user->plan,
      'created_at' => $user->created_at,
      'updated_at' => $user->updated_at,
    ];

    return ApiResponseHelper::successResponse($response, ['profile' => $profile], "Profile retrieved successfully");
  }
}