<?php
namespace App\Features\Version1\Providers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ValidateHelper;
use App\Helpers\ApiResponseHelper;
use App\Helpers\MessageHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;

class ProvidersController
{
    public function getProviders(Request $request, Response $response): Response
    {
        /** @var User $user */
        $user = $request->getAttribute('user');
        $providers = $user->providers();

        return ApiResponseHelper::successResponse($response, $request, [
            'providers' => $providers
        ]);
    }

    public function registerProvider(Request $request, Response $response): Response
    {
        $validationResult = ValidateHelper::validateRequestBody($request->getParsedBody(), [
            'provider', 'token', 'expires_at'
        ]);
        if (
            !$validationResult
            || !is_string($validationResult['provider'])
            || !is_string($validationResult['token'])
            || !is_string($validationResult['expires_at'])
        ) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::INVALID_REQUEST_BODY, MessageHelper::invalid('request body'));
        }

        /** @var User $user */
        $user = $request->getAttribute('user');

        $provider = $validationResult['provider'];
        $token = $validationResult['token'];

        try {
            $expiresAt = new \DateTime($validationResult['expires_at']);
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::INVALID_FIELD_FORMAT, MessageHelper::invalid('expires_at'));
        }

        if ($user->provider($provider) !== null) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::DUPLICATE_RECORD, "Provider already registered; use PUT to update it");
        }

        try {
            $user->registerProvider($provider, $token, $expiresAt);
            return ApiResponseHelper::successResponse($response, $request, null, MessageHelper::succeeded('provider', 'registered'));
        } catch (\Exception $e) {
            error_log('[providers.registerProvider] ' . $e->getMessage());
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, MessageHelper::failed('register', 'provider'));
        }
    }

    public function updateProvider(Request $request, Response $response): Response
    {
        $validationResult = ValidateHelper::validateRequestBody($request->getParsedBody(), [
            'provider', 'token', 'expires_at'
        ]);
        if (
            !$validationResult
            || !is_string($validationResult['provider'])
            || !is_string($validationResult['token'])
            || !is_string($validationResult['expires_at'])
        ) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::INVALID_REQUEST_BODY, MessageHelper::invalid('request body'));
        }

        /** @var User $user */
        $user = $request->getAttribute('user');

        $provider = $validationResult['provider'];
        $token = $validationResult['token'];

        try {
            $expiresAt = new \DateTime($validationResult['expires_at']);
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::INVALID_FIELD_FORMAT, MessageHelper::invalid('expires_at'));
        }

        $providerToken = $user->provider($provider);
        if (!$providerToken) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::NOT_FOUND, ErrorCode::PROVIDER_NOT_FOUND, MessageHelper::notFound('provider'));
        }

        try {
            $providerToken->updateToken($token, $expiresAt);
            return ApiResponseHelper::successResponse($response, $request, null, MessageHelper::succeeded('provider', 'updated'));
        } catch (\Exception $e) {
            error_log('[providers.updateProvider] ' . $e->getMessage());
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, MessageHelper::failed('update', 'provider'));
        }
    }

    public function deleteProvider(Request $request, Response $response): Response
    {
        $validationResult = ValidateHelper::validateRequestBody($request->getParsedBody(), [
            'provider'
        ]);
        if (!$validationResult || !is_string($validationResult['provider'])) {
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::INVALID_REQUEST_BODY, MessageHelper::invalid('request body'));
        }

        /** @var User $user */
        $user = $request->getAttribute('user');

        $provider = $validationResult['provider'];

        try {
            $providerToken = $user->provider($provider);
            if (!$providerToken) {
                return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::NOT_FOUND, ErrorCode::PROVIDER_NOT_FOUND, MessageHelper::notFound('provider'));
            }
            $providerToken->destroy();
            return ApiResponseHelper::successResponse($response, $request, null, MessageHelper::succeeded('provider', 'deleted'));
        } catch (\Exception $e) {
            error_log('[providers.deleteProvider] ' . $e->getMessage());
            return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, MessageHelper::failed('delete', 'provider'));
        }
    }
}