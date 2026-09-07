import "@typespec/http";

using Http;

namespace DevCast.Common;

/**
 * Application-level error code, mirrors `App\Models\Response\Code`
 * (backend/src/Models/Response/Code.php).
 */
enum ErrorCode {
  // OAuth codes
  INVALID_CODE: "INVALID_CODE",
  INVALID_STATE: "INVALID_STATE",
  INVALID_REDIRECT_URI: "INVALID_REDIRECT_URI",
  INVALID_CLIENT_ID: "INVALID_CLIENT_ID",
  INVALID_CLIENT_SECRET: "INVALID_CLIENT_SECRET",
  INVALID_ACCESS_TOKEN: "INVALID_ACCESS_TOKEN",
  EXPIRED_ACCESS_TOKEN: "EXPIRED_ACCESS_TOKEN",
  REVOKED_ACCESS_TOKEN: "REVOKED_ACCESS_TOKEN",
  INSUFFICIENT_SCOPE: "INSUFFICIENT_SCOPE",
  MISSING_CODE_OR_STATE: "MISSING_CODE_OR_STATE",
  TOKEN_EXCHANGE_FAILED: "TOKEN_EXCHANGE_FAILED",

  // Auth codes
  INVALID_CREDENTIALS: "INVALID_CREDENTIALS",
  ACCOUNT_LOCKED: "ACCOUNT_LOCKED",
  ACCOUNT_DISABLED: "ACCOUNT_DISABLED",
  UNAUTHORIZED: "UNAUTHORIZED",
  SESSION_NOT_FOUND: "SESSION_NOT_FOUND",

  // Permission codes
  PERMISSION_DENIED: "PERMISSION_DENIED",
  UNAUTHORIZED_ACCESS: "UNAUTHORIZED_ACCESS",

  // General codes
  INVALID_INPUT: "INVALID_INPUT",
  RESOURCE_NOT_FOUND: "RESOURCE_NOT_FOUND",
  SERVER_ERROR: "SERVER_ERROR",
  SOMETHING_WENT_WRONG: "SOMETHING_WENT_WRONG",

  // Validation codes
  INVALID_REQUEST_BODY: "INVALID_REQUEST_BODY",
  INVALID_QUERY_PARAMS: "INVALID_QUERY_PARAMS",
  VALIDATION_FAILED: "VALIDATION_FAILED",
  MISSING_REQUIRED_FIELDS: "MISSING_REQUIRED_FIELDS",
  INVALID_FIELD_FORMAT: "INVALID_FIELD_FORMAT",
  FIELD_TOO_LONG: "FIELD_TOO_LONG",
  FIELD_TOO_SHORT: "FIELD_TOO_SHORT",
  FIELD_NOT_UNIQUE: "FIELD_NOT_UNIQUE",

  // Database codes
  DATABASE_ERROR: "DATABASE_ERROR",
  RECORD_NOT_FOUND: "RECORD_NOT_FOUND",
  DUPLICATE_RECORD: "DUPLICATE_RECORD",
  FOREIGN_KEY_VIOLATION: "FOREIGN_KEY_VIOLATION",

  // API codes
  RATE_LIMIT_EXCEEDED: "RATE_LIMIT_EXCEEDED",
  API_VERSION_NOT_SUPPORTED: "API_VERSION_NOT_SUPPORTED",
  ENDPOINT_NOT_FOUND: "ENDPOINT_NOT_FOUND",
  METHOD_NOT_ALLOWED: "METHOD_NOT_ALLOWED",
  AUTHENTICATION_FAILED: "AUTHENTICATION_FAILED",
  TOKEN_EXPIRED: "TOKEN_EXPIRED",
  TOKEN_INVALID: "TOKEN_INVALID",
  TOKEN_MISSING: "TOKEN_MISSING",
  TOKEN_REVOKED: "TOKEN_REVOKED",

  // Provider codes
  PROVIDER_NOT_FOUND: "PROVIDER_NOT_FOUND",
}

/**
 * Mirrors `App\Models\Response\Base\Details`
 * (backend/src/Models/Response/Base/Details.php).
 */
model ErrorDetails {
  /** The request path that produced the error, e.g. `/auth/profile`. */
  instance: string;

  /** Server-local timestamp, `Y-m-d H:i:s` format. */
  date: string;

  /** HTTP status code, duplicated from the response status line. */
  status: int32;
}

/**
 * Common error envelope, mirrors `App\Models\Response\Error`
 * (backend/src/Models/Response/Error.php).
 */
model ApiError {
  details: ErrorDetails;
  message: string | null;
  code: ErrorCode;
}

/**
 * Common success envelope, mirrors `App\Models\Response\Success`
 * (backend/src/Models/Response/Success.php).
 */
model ApiSuccess<T> {
  data: T;
  message: string;
}

@doc("400 Bad Request")
@error
model ApiBadRequestResponse {
  @statusCode statusCode: 400;
  @body body: ApiError;
}

@doc("401 Unauthorized")
@error
model ApiUnauthorizedResponse {
  @statusCode statusCode: 401;
  @body body: ApiError;
}

@doc("403 Forbidden")
@error
model ApiForbiddenResponse {
  @statusCode statusCode: 403;
  @body body: ApiError;
}

@doc("404 Not Found")
@error
model ApiNotFoundResponse {
  @statusCode statusCode: 404;
  @body body: ApiError;
}

@doc("422 Unprocessable Entity")
@error
model ApiUnprocessableEntityResponse {
  @statusCode statusCode: 422;
  @body body: ApiError;
}

@doc("500 Internal Server Error")
@error
model ApiInternalServerErrorResponse {
  @statusCode statusCode: 500;
  @body body: ApiError;
}
