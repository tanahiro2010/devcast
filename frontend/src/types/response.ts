// Backend response base types
// Mirrors backend/src/models/response/{base,success,error}.php

export interface ErrorDetails {
  instance: string;
  date: string;
  status: number;
}

export interface SuccessResponse<T = unknown> {
  data: T;
  message: string;
}

export interface ErrorResponse {
  details: ErrorDetails;
  message: string;
  code: ErrorCode;
}

export type ApiResponse<T = unknown> = SuccessResponse<T> | ErrorResponse;

export function isErrorResponse(response: ApiResponse): response is ErrorResponse {
  return (response as ErrorResponse).details !== undefined;
}

export const ErrorStatus = {
  BAD_REQUEST: 400,
  UNAUTHORIZED: 401,
  FORBIDDEN: 403,
  NOT_FOUND: 404,
  UNPROCESSABLE_ENTITY: 422,
  INTERNAL_SERVER_ERROR: 500,
} as const;

export type ErrorStatus = (typeof ErrorStatus)[keyof typeof ErrorStatus];

export const ErrorCode = {
  // Auth Codes
  INVALID_CREDENTIALS: "INVALID_CREDENTIALS",
  ACCOUNT_LOCKED: "ACCOUNT_LOCKED",
  ACCOUNT_DISABLED: "ACCOUNT_DISABLED",

  // Permission Codes
  PERMISSION_DENIED: "PERMISSION_DENIED",
  UNAUTHORIZED_ACCESS: "UNAUTHORIZED_ACCESS",

  // General Codes
  INVALID_INPUT: "INVALID_INPUT",
  RESOURCE_NOT_FOUND: "RESOURCE_NOT_FOUND",
  SERVER_ERROR: "SERVER_ERROR",
  SOMETHING_WENT_WRONG: "SOMETHING_WENT_WRONG",

  // Validation Codes
  VALIDATION_FAILED: "VALIDATION_FAILED",
  MISSING_REQUIRED_FIELDS: "MISSING_REQUIRED_FIELDS",
  INVALID_FIELD_FORMAT: "INVALID_FIELD_FORMAT",
  FIELD_TOO_LONG: "FIELD_TOO_LONG",
  FIELD_TOO_SHORT: "FIELD_TOO_SHORT",
  FIELD_NOT_UNIQUE: "FIELD_NOT_UNIQUE",

  // Database Codes
  DATABASE_ERROR: "DATABASE_ERROR",
  RECORD_NOT_FOUND: "RECORD_NOT_FOUND",
  DUPLICATE_RECORD: "DUPLICATE_RECORD",
  FOREIGN_KEY_VIOLATION: "FOREIGN_KEY_VIOLATION",

  // API Codes
  RATE_LIMIT_EXCEEDED: "RATE_LIMIT_EXCEEDED",
  API_VERSION_NOT_SUPPORTED: "API_VERSION_NOT_SUPPORTED",
  ENDPOINT_NOT_FOUND: "ENDPOINT_NOT_FOUND",
  METHOD_NOT_ALLOWED: "METHOD_NOT_ALLOWED",
  AUTHENTICATION_FAILED: "AUTHENTICATION_FAILED",
  TOKEN_EXPIRED: "TOKEN_EXPIRED",
  TOKEN_INVALID: "TOKEN_INVALID",
  TOKEN_MISSING: "TOKEN_MISSING",
  TOKEN_REVOKED: "TOKEN_REVOKED",
} as const;

export type ErrorCode = (typeof ErrorCode)[keyof typeof ErrorCode];
