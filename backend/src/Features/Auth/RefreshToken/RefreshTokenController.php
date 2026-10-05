<?php

namespace App\Features\Auth\RefreshToken;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Helpers\MessageHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;

class RefreshTokenController
{
    public function refresh(Request $request, Response $response): Response
    {
        /** @var User $user */
        $user = $request->getAttribute('user');

        if (!$user) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, MessageHelper::unauthenticated());
        }

        try {
            $user->deleteAllSessions();
            $user->deleteAllRefreshTokens();
        } catch (\Exception $e) {
            error_log('[auth/token/refresh_token] ' . $e->getMessage());
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, MessageHelper::failed('delete', 'existing sessions'));
        }

        $newAccessToken = $user->refresh($request->getServerParams()['REMOTE_ADDR'], $request->getHeaderLine('User-Agent'));
        $newRefreshToken = $user->refreshToken();
        return ApiResponseHelper::successResponse($response, $request, ['access_token' => $newAccessToken, 'refresh_token' => $newRefreshToken], MessageHelper::succeeded('access token', 'refreshed'));
    }
}
