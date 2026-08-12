<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Response\Status as ErrorStatus;


require __DIR__ . '/../../helpers/response.php';

class errorsController {
  public function notFound(Request $request, Response $response) {
    $instance = $request->getUri()->getPath();
    
    return ApiResponseHelper::errorResponse($response, ErrorStatus::NOT_FOUND, $instance, "The requested resource was not found.");
  }
}