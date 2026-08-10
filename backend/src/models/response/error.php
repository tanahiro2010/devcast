<?
namespace App\Models\Response;

class Details {
  public $instance;
  public $date;
  public $status;

  public function __construct(string $instance, string | null $date = null, int $status = 400) {
    if ($date === null) {
      $date = date('Y-m-d H:i:s');
    }
    $this->instance = $instance;
    $this->date = $date;
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
  public Details $details;
  public string | null $message = "Something went wrong";
  public int $code = 400;

  public function __construct(Details $details, string $message = "Something went wrong", int $code = 400) {
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

enum ErrorCode: int {
  case BAD_REQUEST = 400;
  case UNAUTHORIZED = 401;
  case FORBIDDEN = 403;
  case NOT_FOUND = 404;
  case UNPROCESSABLE_ENTITY = 422;
  case INTERNAL_SERVER_ERROR = 500;
}