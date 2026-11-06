<?php
class Validator
{
    // Clean input coming IN (trim + remove HTML tags)
    public static function clean(?string $value): string
    {
        return trim(strip_tags($value ?? ''));
    }

    // Escape text going OUT to HTML: Validator::e($product['name'])
    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
