<?php
namespace App\Futures\Version1\Providers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ValidateHelper;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\User;

class ProvidersController {
  public function getProviders(Request $request, Response $response) {
    $user = $request->getAttribute('user');
    $providers = $user->providers();

    return ApiResponseHelper::successResponse($response, [
      'providers' => $providers
    ]);
  }

  public function registerProvider(Request $request, Response $response) {
    $validationResult = ValidateHelper::validateRequestBody($request->getParsedBody(), [
      'provider', 'token', 'expires_at'
    ]);
    if (!$validationResult) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::BAD_REQUEST, ErrorCode::INVALID_REQUEST_BODY, "/providers", "Invalid request body");
    }

    /** @var User $user */
    $user = $request->getAttribute('user');

    $provider = $validationResult['provider'];
    $token = $validationResult['token'];
    $expiresAt = new \DateTime($validationResult['expires_at']);

    try {
      $user->registerProvider($provider, $token, $expiresAt);
      return ApiResponseHelper::successResponse($response, null, "Provider registered successfully");
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse($response, ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, "/providers", "Failed to register provider: " . $e->getMessage());
    }
  }
}