<?php
namespace App\Models\Response;
use App\Models\Response\Base\Details;

class Success {
    public mixed $data;
    public string $message;
    public int $code = 200;
    public Details $details;

    public function __construct(mixed $data, string $message = "Success", Details $details = new Details("unknown")) {
        $this->data = $data;
        $this->message = $message;
    }

    public function toArray() {
        return array(
            'data' => $this->data,
            'message' => $this->message
        );
    }
}