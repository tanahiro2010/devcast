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
use App\Libraries\Crypto;

class AuthMiddleware implements MiddlewareInterface {
  public function process(Request $request, RequestHandler $handler): Response {
    $bearerToken = $request->getHeaderLine('Authorization');
    if (!$bearerToken || !str_starts_with($bearerToken, 'Bearer ')) {
      return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Missing or invalid Authorization header");
    }
    
    try {
      $token = str_replace('Bearer ', '', $bearerToken);
      $data = Crypto::jwtDecode(Config::env('JWT_SECRET'), $token);
      
      if (!isset($data['iss']) || !isset($data['sub'])) {
        return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Invalid token: user_id not found");
      }
    } catch (\Exception $e) {
      return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Invalid or expired token");
    }

    return $handler->handle($request);
  }
}