<?php
use Psr\Http\Message\ResponseInterface as Response;

use App\Models\Response\Status as ErrorStatus;
use App\Models\Response\Error as ErrorResponse;
use App\Models\Response\Details as ErrorDetails;
use App\Models\Response\Success as SuccessResponse;

const RESPONSE_CONFIG = array(
  ErrorStatus::BAD_REQUEST => array(
    'message' => 'Bad request'
  ),
  ErrorStatus::UNAUTHORIZED => array(
    'message' => 'Unauthorized'
  ),
  ErrorStatus::FORBIDDEN => array(
    'message' => 'Forbidden'
  ),
  ErrorStatus::NOT_FOUND => array(
    'message' => 'Resource not found'
  ),
  ErrorStatus::UNPROCESSABLE_ENTITY => array(
    'message' => 'Unprocessable entity'
  ),
  ErrorStatus::INTERNAL_SERVER_ERROR => array(
    'message' => 'Internal server error'
  )
);

class ApiResponseHelper {
  static function sendResponse(Response $response, int $statusCode, array $data) {
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
  }


  static function sendErrorResponse(Response $response, string $instance, int $status = 400, string $message = "Something went wrong") {
    $errorDetails = new ErrorDetails($instance, null, $status);
    $errorResponse = new ErrorResponse($errorDetails, $message, $status);
    return self::sendResponse($response, $status, $errorResponse->toArray());
  }

  static function sendSuccessResponse(Response $response, mixed $data, string $message = "Success", int $status = 200) {
    $successResponse = new SuccessResponse($data, $message);
    return self::sendResponse($response, $status, $successResponse->toArray());
  }
  
  static function errorResponse(Response $response, ErrorStatus $errorStatus, string $instance, string $message = null) {
    $status = $errorStatus->value;
    $config = RESPONSE_CONFIG[$status] ?? array('message' => 'Something went wrong');
    $defaultMessage = $config['message'];
    $errorMessage = $message ?? $defaultMessage;

    return self::sendErrorResponse($response, $instance, $status, $errorMessage);
  }
  
}