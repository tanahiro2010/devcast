<?php
namespace App\Features\Errors;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Response\Status as ErrorStatus;
use App\Models\Response\Code as ErrorCode;
use App\Helpers\ApiResponseHelper;
use App\Helpers\MessageHelper;

class ErrorsController
{
    public function notFound(Request $request, Response $response): Response
    {
        return ApiResponseHelper::errorResponse($response, $request, ErrorStatus::NOT_FOUND, ErrorCode::RESOURCE_NOT_FOUND, MessageHelper::notFound('resource'));
    }
}
