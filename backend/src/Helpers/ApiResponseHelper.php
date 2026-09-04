<?php
namespace App\Helpers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Response\Base\Details as ErrorDetails;
use App\Models\Response\Status as ErrorStatus;
use App\Models\Response\Code as ErrorCode;
use App\Models\Response\Error as ErrorResponse;
use App\Models\Response\Success as SuccessResponse;

const RESPONSE_CONFIG = array(
  ErrorStatus::BAD_REQUEST->value => array(
    'message' => 'Bad request'
  ),
  ErrorStatus::UNAUTHORIZED->value => array(
    'message' => 'Unauthorized'
  ),
  ErrorStatus::FORBIDDEN->value => array(
    'message' => 'Forbidden'
  ),
  ErrorStatus::NOT_FOUND->value => array(
    'message' => 'Resource not found'
  ),
  ErrorStatus::UNPROCESSABLE_ENTITY->value => array(
    'message' => 'Unprocessable entity'
  ),
  ErrorStatus::INTERNAL_SERVER_ERROR->value => array(
    'message' => 'Internal server error'
  )
);

class ApiResponseHelper {
  static function sendResponse(Response $response, int $statusCode, array $data): Response {
    $response->getBody()->write(json_encode(
      $data,
      JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
    ));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
  }

  /**
   * @param Request|string $instance The request the error occurred on, or an explicit path/identifier.
   */
  static function errorResponse(
    Response $response,
    Request|string $instance,
    ErrorStatus $status = ErrorStatus::BAD_REQUEST,
    ErrorCode $code = ErrorCode::SOMETHING_WENT_WRONG,
    ?string $message = null
  ): Response {
    $path = $instance instanceof Request ? $instance->getUri()->getPath() : $instance;
    $config = RESPONSE_CONFIG[$status->value] ?? array('message' => 'Something went wrong');
    $errorMessage = $message ?? $config['message'];

    $errorDetails = new ErrorDetails($path, null, $status->value);
    $errorResponse = new ErrorResponse($errorDetails, $errorMessage, $code);
    return self::sendResponse($response, $status->value, $errorResponse->toArray());
  }

  static function successResponse(Response $response, mixed $data = null, ?string $message = null, int $status = 200): Response {
    $successResponse = new SuccessResponse($data, $message ?? "Success");
    return self::sendResponse($response, $status, $successResponse->toArray());
  }

  static function redirect(Response $response, string $url, int $status = 302): Response {
    return $response->withHeader('Location', $url)->withStatus($status);
  }
}
