<?php
namespace App\Models\Response;
use App\Models\Response\Base\Details;

class Error {
  public Details $details;
  public string | null $message = "Something went wrong";
  public Code $code = Code::SOMETHING_WENT_WRONG;

  public function __construct(Details $details, string $message = "Something went wrong", Code $code = Code::SOMETHING_WENT_WRONG) {
    $this->details = $details;
    $this->message = $message;
    $this->code = $code;
  }

  public function toArray() {
    return array(
      'details' => $this->details->toArray(),
      'message' => $this->message,
      'code' => $this->code
    );
  }
}
