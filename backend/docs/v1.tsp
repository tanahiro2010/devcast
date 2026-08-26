import "@typespec/http";
import "./common.tsp";
import "./models.tsp";

using Http;
using DevCast.Common;
using DevCast.Models;

namespace DevCast.V1;

model VersionData {
  version: string;
}

model ProvidersData {
  providers: Provider[];
}

/**
 * Body for registering a linked OAuth provider credential.
 * backend/src/Futures/Version1/Providers/ProvidersController.php:23-44
 */
model RegisterProviderBody {
  provider: string;
  token: string;

  /** ISO-8601 date-time string, parsed with PHP's `new \DateTime(...)`. */
  expires_at: string;
}

@route("/v1")
@tag("V1")
@useAuth(BearerAuth)
interface V1 {
  /**
   * backend/src/Futures/Version1/Version1Controller.php:10
   */
  @get
  version(): ApiSuccess<VersionData>;

  /**
   * backend/src/Futures/Version1/Providers/ProvidersController.php:13
   */
  @get
  @route("/providers")
  getProviders(): ApiSuccess<ProvidersData>;

  /**
   * Registers a linked OAuth provider credential for the authenticated
   * user.
   *
   * NOTE: as of 2026-08-26 this action is implemented in
   * `ProvidersController::registerProvider` but is NOT registered in
   * `backend/src/Config/routes.php` — calling `POST /v1/providers`
   * currently falls through to the catch-all 404 handler. Documented
   * here ahead of wiring so the contract is agreed before it ships.
   */
  @post
  @route("/providers")
  registerProvider(@body body: RegisterProviderBody):
    | ApiSuccess<null>
    | ApiBadRequestResponse // INVALID_REQUEST_BODY
    | ApiInternalServerErrorResponse;
}
