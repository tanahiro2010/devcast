<?php

namespace App\Features\Auth\Profile;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Helpers\MessageHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;

class ProfileController
{
    public function getProfile(Request $request, Response $response): Response
    {
        /** @var User $user */
        $user = $request->getAttribute('user');

        if (!$user) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, MessageHelper::unauthenticated());
        }

        try {
            $profile = ProfileService::getProfile($user);

            return ApiResponseHelper::successResponse($response, $request, ['profile' => $profile], MessageHelper::succeeded('profile', 'retrieved'));
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, MessageHelper::failed('retrieve', 'profile') . ": " . $e->getMessage());
        }
    }
}
