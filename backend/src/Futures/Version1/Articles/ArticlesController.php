<?php

namespace App\Futures\Version1\Articles;


use App\Helpers\ApiResponseHelper;
use App\Helpers\ValidateHelper;
use App\Models\DB\User;
use App\Models\Response\Status as ErrorStatus;
use App\Models\Response\Code as ErrorCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ArticlesController
{
    private ArticlesService $articlesService;
    function __construct()
    {
        $this->articlesService = new ArticlesService();
    }

    function getArticles(Request $request, Response $response): Response
    {
        /** @var User $user */
        $user = $request->getAttribute("user");
        if ($user === null) return ApiResponseHelper::errorResponse(
            $response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED,
            "Failed to fetch user"
        );

        $params = $request->getQueryParams();
        $includeMetadata = $params['metadata'] === "true";
        try {
            $articles = $this->articlesService->getArticles($user);

            $result = ["articles" => $articles];

            if ($includeMetadata) {
                $metadata = $this->articlesService->getMetadata($articles);
                $result["metadata"] = $metadata;
            }

            return ApiResponseHelper::successResponse($response, $request, $result, "Success to get articles");
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse($response, $request, message: $e->getMessage());
        }
    }

    function createArticle(Request $request, Response $response): Response
    {
        /** @var User $user */
        $user = $request->getAttribute("user");
        if ($user === null) return ApiResponseHelper::errorResponse(
            $response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED,
            "Failed to fetch user"
        );

        $body = $request->getParsedBody();
        $hasValidationError = (
            !ValidateHelper::validateRequestBody($body, ['title', 'body', 'tags']) ||
            !is_string($body['title']) ||
            !is_string($body['body']) ||
            !is_array($body['tags']) ||
            count($body['tags']) >= 5
        );
        if ($hasValidationError) {
            return ApiResponseHelper::errorResponse(
                $response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::VALIDATION_FAILED,
                "title must be a string and body must be a string and tags must be an array"
            );
        }

        $article = $user->createArticle($body);

        return ApiResponseHelper::successResponse($response, $request, $article, "Article created successfully");
    }
}