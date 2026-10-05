<?php

namespace App\Features\Version1\Articles;


use App\Helpers\ApiResponseHelper;
use App\Helpers\MessageHelper;
use App\Helpers\ValidateHelper;
use App\Models\DB\Article;
use App\Models\DB\User;
use App\Models\Response\Code;
use App\Models\Response\Status;
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
            MessageHelper::unauthenticated()
        );

        $params = $request->getQueryParams();
        $includeMetadata = $params['metadata'] === "true";
        $include = !empty($params['include']) ? explode(',', $params['include']) : [];
        try {
            $articles = $this->articlesService->getArticles($user, $include);

            $result = ["articles" => $articles];

            if ($includeMetadata) {
                $metadata = $this->articlesService->getMetadata($articles);
                $result["metadata"] = $metadata;
            }

            return ApiResponseHelper::successResponse($response, $request, $result, MessageHelper::succeeded('articles', 'retrieved'));
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse($response, $request, message: $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array{article_id: string} $args
     * @return Response
     */
    function getArticle(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute("user");
        if ($user === null) return ApiResponseHelper::errorResponse(
            $response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED,
            MessageHelper::unauthenticated()
        );

        $articleId = $args["article_id"];
        if ($articleId === null) return ApiResponseHelper::errorResponse(
            $response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::VALIDATION_FAILED,
            MessageHelper::invalid('article id')
        );

        try {
            $article = $this->articlesService->getArticle($articleId);
            $result = ["article" => $article];

            return ApiResponseHelper::successResponse($response, $request, $result, MessageHelper::succeeded('article', 'retrieved'));
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse(
                $response, $request, ErrorStatus::NOT_FOUND, ErrorCode::RESOURCE_NOT_FOUND,
                MessageHelper::notFound('article')
            );
        }
    }


    function createArticle(Request $request, Response $response): Response
    {
        /** @var User $user */
        $user = $request->getAttribute("user");
        if ($user === null) return ApiResponseHelper::errorResponse(
            $response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED,
            MessageHelper::unauthenticated()
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
                MessageHelper::invalid('request body', 'title and body must be strings and tags must be an array')
            );
        }

        $article = $user->createArticle($body);

        return ApiResponseHelper::successResponse($response, $request, $article, MessageHelper::succeeded('article', 'created'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array{article_id: string} $args
     * @return Response
     */
    function updateArticle(Request $request, Response $response, array $args): Response
    {
        /**
         * @var User $user
         */
        $user = $request->getAttribute("user");
        if ($user === null) return ApiResponseHelper::errorResponse(
            $response, $request, ErrorStatus::UNAUTHORIZED, ErrorCode::UNAUTHORIZED,
            MessageHelper::unauthenticated()
        );

        $articleId = $args["article_id"];
        if ($articleId === null) return ApiResponseHelper::errorResponse(
            $response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::VALIDATION_FAILED,
            MessageHelper::missing('article_id')
        );

        try {
            $article = $this->articlesService->getArticle($articleId);
            if ($article === null) {
                throw new \Exception("Article not found");
            }
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse(
                $response, $request, ErrorStatus::BAD_REQUEST, ErrorCode::VALIDATION_FAILED,
                MessageHelper::notFound('article')
            );
        }

        $body = $request->getParsedBody();
        $hasValidationError = (
            $body === null ||
            !ValidateHelper::validateRequestBody($body, ['title', 'body', 'tags']) ||
            !is_string($body['title']) ||
            !is_string($body['body']) ||
            !is_array($body['tags']) ||
            count($body['tags']) >= 5
        );

        if ($hasValidationError) {
            return ApiResponseHelper::errorResponse(
                $response, $request, ErrorStatus::BAD_REQUEST, Code::VALIDATION_FAILED,
                MessageHelper::invalid('request body', 'title and body must be strings and tags must be an array')
            );
        }

        try {
            $article->updateArticle($user, $body);
        } catch (\Exception $e) {
            return ApiResponseHelper::errorResponse(
                $response, $request, ErrorStatus::BAD_REQUEST, Code::VALIDATION_FAILED,
                MessageHelper::failed('update', 'article')
            );
        }

        return ApiResponseHelper::successResponse($response, $request, $article, MessageHelper::succeeded('article', 'updated'));
    }
}