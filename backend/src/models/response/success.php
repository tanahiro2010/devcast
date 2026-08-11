<?php
namespace App\Models\Response;

class Success {
  public $data;
  public $message;

  public function __construct(mixed $data, string $message = "Success") {
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