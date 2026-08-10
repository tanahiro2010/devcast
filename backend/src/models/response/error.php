<?
namespace App\Models\Response;

class Details {
  public $instance;
  public $date;
  public $status;

  public function __construct(string $instance, string | null $date = null, int $status = 400) {
    $this->instance = $instance;
    $this->date = $date ?? date('Y-m-d H:i:s');
    $this->status = $status;
  }

  public function toArray() {
    return array(
      'instance' => $this->instance,
      'date' => $this->date,
      'status' => $this->status
    );
  }
}

class Error {
  public $details;
  public $message;
  public $code;

  public function __construct(Details $details, string $message = "Something went wrong", $code = null) {
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

