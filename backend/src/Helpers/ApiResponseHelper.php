<?php
namespace App\Helpers;

use Psr\Http\Message\ResponseInterface as Response;
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
  static function sendResponse(Response $response, int $statusCode, array $data) {
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
  }


  static function sendErrorResponse(Response $response, string $instance, string $message = "Something went wrong", ErrorStatus $status = ErrorStatus::BAD_REQUEST, ErrorCode $code = ErrorCode::SOMETHING_WENT_WRONG) {
    $errorDetails = new ErrorDetails($instance, null, $status->value);
    $errorResponse = new ErrorResponse($errorDetails, $message, $code);
    return self::sendResponse($response, $status->value, $errorResponse->toArray());
  }

  static function errorResponse(Response $response, ErrorStatus $errorStatus, ErrorCode $code, string $instance, ?string $message = null) {
    $status = $errorStatus->value;
    $config = RESPONSE_CONFIG[$status] ?? array('message' => 'Something went wrong');
    $defaultMessage = $config['message'];
    $errorMessage = $message ?? $defaultMessage;

    return self::sendErrorResponse($response, $instance, $errorMessage, $errorStatus, $code);
  }

  static function sendSuccessResponse(Response $response, mixed $data, string $message = "Success", int $status = 200) {
    $successResponse = new SuccessResponse($data, $message);
    return self::sendResponse($response, $status, $successResponse->toArray());
  }

  static function successResponse(Response $response, mixed $data, ?string $message = null, int $status = 200) {
    $defaultMessage = "Success";
    $successMessage = $message ?? $defaultMessage;

    return self::sendSuccessResponse($response, $data, $successMessage, $status);
  }

  static function redirect(Response $response, string $url, int $status = 302) {
    return $response->withHeader('Location', $url)->withStatus($status);
  }
}
