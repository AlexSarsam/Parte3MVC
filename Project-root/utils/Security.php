<?php

class Security {
    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken(?string $token): bool {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function normalizeText(string $value, int $maxLen = 5000): string {
        $value = trim($value);
        if (strlen($value) > $maxLen) {
            $value = substr($value, 0, $maxLen);
        }
        return $value;
    }
}
