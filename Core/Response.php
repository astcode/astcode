<?php

namespace App\Core;

class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url)
    {
        echo "Redirecting to: " . $url; // Debug line
        header('Location: ' . $url);
        exit;
    }
}
