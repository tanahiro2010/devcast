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
      'created_at' => $user->created_at,
      'updated_at' => $user->updated_at,
    ];

    $subscription = $user->activeSubscription();
    if ($subscription) {
      $profile['subscription'] = [
        'stripe_subscription_id' => $subscription->stripe_subscription_id,
        'stripe_price_id' => $subscription->stripe_price_id,
        'status' => $subscription->status,
        'current_period_start' => $subscription->current_period_start,
        'current_period_end' => $subscription->current_period_end,
      ];
    } else {
      $profile['subscription'] = null;
    }
    

    return ApiResponseHelper::successResponse($response, ['profile' => $profile], "Profile retrieved successfully");
  }
}