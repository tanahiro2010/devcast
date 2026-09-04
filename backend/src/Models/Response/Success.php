<?php
namespace App\Models\Response;
use App\Models\Response\Base\Details;

class Success
{
    public mixed $data;
    public string $message;
    public Details $details;

    public function __construct(mixed $data, string $message = "Success", Details $details = new Details("unknown"))
    {
        $this->data = $data;
        $this->message = $message;
        $this->details = $details;
    }

    /**
     * @return array{data: mixed, message: string, details: array{instance: string, date: string, status: int}}
     */
    public function toArray(): array
    {
        return array(
            'data' => $this->data,
            'message' => $this->message,
            'details' => $this->details->toArray()
        );
    }
}