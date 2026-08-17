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

enum Status: int {
  case BAD_REQUEST = 400;
  case UNAUTHORIZED = 401;
  case FORBIDDEN = 403;
  case NOT_FOUND = 404;
  case UNPROCESSABLE_ENTITY = 422;
  case INTERNAL_SERVER_ERROR = 500;
}

enum Code: string {
  // Auth Codes
  case INVALID_CREDENTIALS = "INVALID_CREDENTIALS";
  case ACCOUNT_LOCKED = "ACCOUNT_LOCKED";
  case ACCOUNT_DISABLED = "ACCOUNT_DISABLED";
  case MISSING_CODE_OR_STATE = "MISSING_CODE_OR_STATE";

  // Permission Codes
  case PERMISSION_DENIED = "PERMISSION_DENIED";
  case UNAUTHORIZED_ACCESS = "UNAUTHORIZED_ACCESS";

  // General Codes
  case INVALID_INPUT = "INVALID_INPUT";
  case RESOURCE_NOT_FOUND = "RESOURCE_NOT_FOUND";
  case SERVER_ERROR = "SERVER_ERROR";
  case SOMETHING_WENT_WRONG = "SOMETHING_WENT_WRONG";

  // Validation Codes
  case VALIDATION_FAILED = "VALIDATION_FAILED";
  case MISSING_REQUIRED_FIELDS = "MISSING_REQUIRED_FIELDS";
  case INVALID_FIELD_FORMAT = "INVALID_FIELD_FORMAT";
  case FIELD_TOO_LONG = "FIELD_TOO_LONG";
  case FIELD_TOO_SHORT = "FIELD_TOO_SHORT";
  case FIELD_NOT_UNIQUE = "FIELD_NOT_UNIQUE";

  // Database Codes
  case DATABASE_ERROR = "DATABASE_ERROR";
  case RECORD_NOT_FOUND = "RECORD_NOT_FOUND";
  case DUPLICATE_RECORD = "DUPLICATE_RECORD";
  case FOREIGN_KEY_VIOLATION = "FOREIGN_KEY_VIOLATION";

  // API Codes
  case RATE_LIMIT_EXCEEDED = "RATE_LIMIT_EXCEEDED";
  case API_VERSION_NOT_SUPPORTED = "API_VERSION_NOT_SUPPORTED";
  case ENDPOINT_NOT_FOUND = "ENDPOINT_NOT_FOUND";
  case METHOD_NOT_ALLOWED = "METHOD_NOT_ALLOWED";
  case AUTHENTICATION_FAILED = "AUTHENTICATION_FAILED";
  case TOKEN_EXPIRED = "TOKEN_EXPIRED";
  case TOKEN_INVALID = "TOKEN_INVALID";
  case TOKEN_MISSING = "TOKEN_MISSING";
  case TOKEN_REVOKED = "TOKEN_REVOKED";
}