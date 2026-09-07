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
 * Body for registering or updating a linked OAuth provider credential.
 * backend/src/Futures/Version1/Providers/ProvidersController.php:25-62 (register)
 * backend/src/Futures/Version1/Providers/ProvidersController.php:64-102 (update)
 */
model RegisterProviderBody {
  provider: string;
  token: string;

  /** ISO-8601 date-time string, parsed with PHP's `new \DateTime(...)`. */
  expires_at: string;
}

model DeleteProviderBody {
  provider: string;
}

model ArticlesData {
  articles: Article[];

  /** Only present when the `metadata` query parameter is `"true"`. */
  metadata?: ArticlesMetadata;
}

/**
 * backend/src/Futures/Version1/Articles/ArticlesController.php:49-76
 * `tags` must have fewer than 5 entries (`count($tags) >= 5` is rejected).
 */
model CreateArticleBody {
  title: string;
  body: string;
  tags: string[];
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
   * backend/src/Futures/Version1/Providers/ProvidersController.php:14
   */
  @get
  @route("/providers")
  getProviders(): ApiSuccess<ProvidersData>;

  /**
   * Registers a linked OAuth provider credential for the authenticated
   * user. Fails if a credential for that provider already exists — use
   * `updateProvider` to rotate an existing one.
   * backend/src/Futures/Version1/Providers/ProvidersController.php:25
   */
  @post
  @route("/providers")
  registerProvider(@body body: RegisterProviderBody):
    | ApiSuccess<null>
    | ApiBadRequestResponse // INVALID_REQUEST_BODY | INVALID_FIELD_FORMAT | DUPLICATE_RECORD
    | ApiInternalServerErrorResponse;

  /**
   * Rotates the token/expiry on an existing linked provider credential.
   * backend/src/Futures/Version1/Providers/ProvidersController.php:64
   */
  @put
  @route("/providers")
  updateProvider(@body body: RegisterProviderBody):
    | ApiSuccess<null>
    | ApiBadRequestResponse // INVALID_REQUEST_BODY | INVALID_FIELD_FORMAT
    | ApiNotFoundResponse // PROVIDER_NOT_FOUND
    | ApiInternalServerErrorResponse;

  /**
   * Unlinks a provider credential from the authenticated user.
   * backend/src/Futures/Version1/Providers/ProvidersController.php:104
   */
  @delete
  @route("/providers")
  deleteProvider(@body body: DeleteProviderBody):
    | ApiSuccess<null>
    | ApiBadRequestResponse // INVALID_REQUEST_BODY
    | ApiNotFoundResponse // PROVIDER_NOT_FOUND
    | ApiInternalServerErrorResponse;

  /**
   * Lists the authenticated user's articles. Pass `?metadata=true` to
   * also compute publish-state counters across all providers.
   * backend/src/Futures/Version1/Articles/ArticlesController.php:22
   */
  @get
  @route("/articles")
  getArticles(@query metadata?: "true" | "false"):
    | ApiSuccess<ArticlesData>
    | ApiUnauthorizedResponse
    | ApiInternalServerErrorResponse;

  /**
   * Creates an article owned by the authenticated user and seeds an
   * `article_status` row (unpublished) for each of their linked
   * providers. Returns the created article directly as `data` (not
   * nested under an `article` key).
   * backend/src/Futures/Version1/Articles/ArticlesController.php:49
   */
  @post
  @route("/articles")
  createArticle(@body body: CreateArticleBody):
    | ApiSuccess<Article>
    | ApiUnauthorizedResponse
    | ApiBadRequestResponse; // VALIDATION_FAILED
}
