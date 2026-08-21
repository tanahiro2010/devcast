<?php

namespace App\Middleware;

use App\Config\Config;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Helpers\ApiResponseHelper;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Status as ErrorStatus;
use App\Models\DB\Session;
use App\Libraries\Crypto;

class AuthMiddleware implements MiddlewareInterface
{
  public function process(Request $request, RequestHandler $handler): Response
  {
    $bearerToken = $request->getHeaderLine('Authorization');
    if (!$bearerToken || !str_starts_with($bearerToken, 'Bearer ')) {
      return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Missing or invalid Authorization header");
    }

    try {
      $token = str_replace('Bearer ', '', $bearerToken);
      $data = Crypto::jwtDecode($token, Config::env('JWT_SECRET'));

      if (!isset($data['iss']) || !isset($data['sub'])) {
        return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Invalid token: user_id not found");
      }
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Invalid or expired token: " . $e->getMessage());
    }

    try {
      $session = Session::findBySessionId($data['iss']);
      if (!$session) {
        return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Session not found");
      }
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, $request->getUri()->getPath(), "Failed to retrieve session: " . $e->getMessage());
    }


    if ($session->isExpired()) {
      return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::BAD_REQUEST, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Session expired");
    }

    try {
      $user = $session->user();
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::INTERNAL_SERVER_ERROR, ErrorCode::SOMETHING_WENT_WRONG, $request->getUri()->getPath(), "Failed to retrieve user: " . $e->getMessage());
    }

    $request = $request->withAttribute('user', $user);
    $request = $request->withAttribute('session', $session);

    return $handler->handle($request);
  }
}
