<?php
namespace App\Helpers;

class ValidateHelper
{
    static function isValidEmail(string $email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    static function isValidBase64(string $base64)
    {
        return base64_decode($base64, true) !== false;
    }

    static function isValidUrl(string $url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    static function isValidDate(string $date)
    {
        return strtotime($date) !== false;
    }

    static function isValidJson(string $json)
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    static function isValidUuid(string $uuid)
    {
        return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid) === 1;
    }

    static function validateStringLength(string $string, int $minLength, int $maxLength)
    {
        $length = strlen($string);
        return $length >= $minLength && $length <= $maxLength;
    }

    static function validateIntegerRange(int $number, int $min, int $max)
    {
        return $number >= $min && $number <= $max;
    }

    static function validateRequestBody(array $body, array $requiredFields)
    {
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $body)) {
                return false;
            }
        }

        return $body;
    }
}