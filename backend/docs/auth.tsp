import "@typespec/http";
import "./common.tsp";
import "./models.tsp";

using Http;
using DevCast.Common;
using DevCast.Models;

namespace DevCast.Auth;

@route("/health")
@tag("Health")
interface Health {
  /**
   * Liveness check. Returns a plain-text body, not JSON.
   * backend/src/Futures/Health/HealthController.php:8
   */
  @get
  @useAuth(NoAuth)
  health(): {
    @statusCode statusCode: 200;
    @header contentType: "text/plain";
    @body body: string;
  };
}

model OAuthUrlData {
  /** GitHub OAuth authorization URL to redirect the user to. */
  url: string;
}

model AccessTokenData {
  access_token: string;
}

model RefreshTokenData {
  access_token: string;
  refresh_token: string;
}

model ProfileData {
  profile: Profile;
}

@route("/auth")
@tag("Auth")
interface Auth {
  /**
   * Generates the GitHub OAuth authorization URL.
   * backend/src/Futures/Auth/AuthController.php:13
   */
  @get
  @useAuth(NoAuth)
  oauthUrl(): ApiSuccess<OAuthUrlData> | ApiInternalServerErrorResponse;

  /**
   * Exchanges a refresh token (as a query parameter) for a new access
   * token. Distinct from `refreshToken`, which requires a bearer JWT and
   * also rotates the refresh token.
   * backend/src/Futures/Auth/AccessToken/AccessTokenController.php:16
   */
  @get
  @route("/token/access_token")
  @useAuth(NoAuth)
  accessToken(@query("refresh_token") refreshToken: string):
    | ApiSuccess<AccessTokenData>
    | ApiBadRequestResponse // MISSING_REQUIRED_FIELDS
    | ApiUnauthorizedResponse; // TOKEN_EXPIRED | UNAUTHORIZED

  /**
   * Requires a bearer JWT. Invalidates all of the user's existing
   * sessions and issues a brand new access + refresh token pair.
   * backend/src/Futures/Auth/RefreshToken/RefreshTokenController.php:14
   */
  @get
  @route("/token/refresh_token")
  @useAuth(BearerAuth)
  refreshToken(): ApiSuccess<RefreshTokenData> | ApiUnauthorizedResponse | ApiInternalServerErrorResponse;

  /**
   * Requires a bearer JWT. Returns the authenticated user's profile.
   * backend/src/Futures/Auth/Profile/ProfileController.php:14
   */
  @get
  @route("/profile")
  @useAuth(BearerAuth)
  getProfile(): ApiSuccess<ProfileData> | ApiUnauthorizedResponse | ApiInternalServerErrorResponse;

  /**
   * GitHub OAuth callback. On success this issues a 302 redirect to
   * `{frontend_base_url}/_auth/callback?token={jwt}` rather than a JSON
   * body.
   * backend/src/Futures/Auth/Callback/CallbackController.php:25
   */
  @get
  @route("/callback")
  @useAuth(NoAuth)
  callback(@query code: string, @query state: string):
    | {
        @statusCode statusCode: 302;
        @header location: string;
      }
    | ApiBadRequestResponse // MISSING_CODE_OR_STATE
    | ApiInternalServerErrorResponse; // TOKEN_EXCHANGE_FAILED
}
