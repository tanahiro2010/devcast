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
  private function resolveAuthorizationHeader(Request $request): string
  {
    $header = $request->getHeaderLine('Authorization');
    if ($header !== '') {
      return $header;
    }

    // 一部のリバースプロキシ/PHP実行環境(Apache+PHP-FPM、nginx+fastcgiの設定漏れなど)は
    // Authorizationヘッダーをアプリケーションまで転送しないため、$_SERVER側もフォールバックとして見る
    foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION'] as $key) {
      if (!empty($_SERVER[$key])) {
        return $_SERVER[$key];
      }
    }

    if (function_exists('apache_request_headers')) {
      $headers = apache_request_headers();
      foreach ($headers as $name => $value) {
        if (strcasecmp($name, 'Authorization') === 0) {
          return $value;
        }
      }
    }

    return '';
  }

  public function process(Request $request, RequestHandler $handler): Response
  {
    $bearerToken = $this->resolveAuthorizationHeader($request);
    if (!$bearerToken || !str_starts_with($bearerToken, 'Bearer ')) {
      return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED, $request->getUri()->getPath(), "Missing or invalid Authorization header");
    }
    
    $token = str_replace('Bearer ', '', $bearerToken);

    try {
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
        return ApiResponseHelper::errorResponse(new \Slim\Psr7\Response(), ErrorStatus::UNAUTHORIZED, ErrorCode::SESSION_NOT_FOUND, $request->getUri()->getPath(), "Session not found");
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
