<?php

namespace App\Core\Middlewares;

use App\Core\Application;
use App\Core\Exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    protected array $actions = [];
    protected array $permissions = [];

    public function __construct(array $permissions = [], array $actions = [])
    {
        $this->permissions = $permissions;
        $this->actions = $actions;
    }

    public function execute()
    {
        // First check if user is logged in
        if (Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                Application::$app->response->redirect('/login');
                exit;
            }
        }

        // Then check permissions if specified
        if (!empty($this->permissions) && !Application::isGuest()) {
            $user = Application::$app->user;
            $hasAnyPermission = false;
            
            foreach ($this->permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasAnyPermission = true;
                    break;
                }
            }

            if (!$hasAnyPermission) {
                throw new ForbiddenException('You do not have the required permissions to access this page');
            }
        }
    }
}
