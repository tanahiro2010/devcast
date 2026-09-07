<?php

namespace App\Futures\Auth\AccessToken;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\RefreshToken;


class AccessTokenController
{
    public function accessToken(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $refreshTokenValue = is_array($body) ? ($body['refresh_token'] ?? null) : null;

        if (!is_string($refreshTokenValue) || $refreshTokenValue === '') {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::MISSING_REQUIRED_FIELDS, "Missing refresh_token");
        }

        $refreshToken = RefreshToken::findByToken($refreshTokenValue);

        if (!$refreshToken || $refreshToken->isTokenExpired()) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::TOKEN_EXPIRED, "Invalid or expired refresh token");
        }

        try {
            [$newAccessToken, $newRefreshTokenValue] = $refreshToken->rotate(
                $request->getServerParams()['REMOTE_ADDR'] ?? '',
                $request->getHeaderLine('User-Agent')
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, "User not found for this refresh token");
        }

        return ApiResponseHelper::successResponse($response, $request, [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshTokenValue,
        ], "Access token refreshed successfully");
    }
}
