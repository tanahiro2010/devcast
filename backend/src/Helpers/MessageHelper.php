<?php
namespace App\Helpers;

/**
 * APIレスポンスのmessage文言を統一するためのヘルパー。
 * 末尾にピリオドは付けない。
 */
class MessageHelper
{
    /** e.g. "Failed to update article" */
    static function failed(string $action, string $resource): string
    {
        return "Failed to {$action} {$resource}";
    }

    /** e.g. "Article not found" */
    static function notFound(string $resource): string
    {
        return ucfirst($resource) . " not found";
    }

    /** e.g. "Missing article_id" */
    static function missing(string $field): string
    {
        return "Missing {$field}";
    }

    /** e.g. "Invalid request body" / "Invalid request body: title must be a string" */
    static function invalid(string $subject, ?string $reason = null): string
    {
        return "Invalid {$subject}" . ($reason !== null ? ": {$reason}" : "");
    }

    static function unauthenticated(): string
    {
        return "User not authenticated";
    }

    /** e.g. "Article created successfully" */
    static function succeeded(string $resource, string $pastAction): string
    {
        return ucfirst($resource) . " {$pastAction} successfully";
    }
}
