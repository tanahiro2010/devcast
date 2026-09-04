<?php
namespace App\Futures\Version1;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ApiResponseHelper;


class Version1Controller
{
    public function version1(Request $request, Response $response): Response
    {
        return ApiResponseHelper::successResponse($response, [
            'version' => '1.0.0',
        ]);
    }
}