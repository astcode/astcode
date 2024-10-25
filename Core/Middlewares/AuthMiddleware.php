<?php

namespace App\Core\Middlewares;

use App\Core\Application;
use App\Core\Exception\ForbiddenExeption;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * Execute the middleware
     * @return void
     * 
     * if the user is not logged in and the action is not in the actions array, redirect to the login page
     */
    public function execute() {
        if (Application::$app->isGuest()) {
            // echo 'isGuest';
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                // throw new ForbiddenExeption();
                Application::$app->response->redirect('login');
            }
        }
    }

}
