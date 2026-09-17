<?php
namespace App\Infrastructure;

class InfrastructureException extends \RuntimeException
{
    private ?int $statusCode;
    private ?string $responseBody;

    public function __construct(
        string $message,
        ?int $statusCode = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }
}
