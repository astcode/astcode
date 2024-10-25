<?php

namespace App\Core\Middlewares;

use App\Core\Application;
use App\Core\Exception\ForbiddenExeption;

class CsrfMiddleware extends BaseMiddleware
{
    public function __construct(){   }

    public function execute()
    {
        // dd($_SERVER['REQUEST_METHOD']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!$this->isValidCsrfToken($token)) {
                throw new ForbiddenExeption('Invalid CSRF token');
            }
        }
    }

    public static function generateCsrfToken()
    {
        $token = bin2hex(random_bytes(32));
        Application::$app->session->set('csrf_token', $token);
        return $token;
    }

    private function isValidCsrfToken($token)
    {
        $sessionToken = Application::$app->session->get('csrf_token');
        return hash_equals($sessionToken, $token);
    }
}
