<?php
namespace App\Features\Health;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HealthController
{
    public function health(Request $_, Response $response): Response
    {
        $response->getBody()->write('Hello, World!');
        return $response;
    }
}
