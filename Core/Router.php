<?php

namespace App\Core;

use App\Core\Exception\NotFoundException;
use App\Core\Request;
use App\Core\Response;

/**
 * @author Aaron Thomas <aaron@aaronsthomas.com>
 * @package App\Core
 */
class Router
{
    
    protected Request $request;
    public Response $response;
    protected array $routes = [];

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        // dd($callback);

        if ($callback === false) {
            // $this->response->setStatusCode(404);
            throw new NotFoundException();
            // return $this->renderView('exceptions/_404');
        }
        if (is_string($callback)) {
            return Application::$app->view->renderView($callback);
        }

        if (is_array($callback)) {
            /** @var \App\Core\Controller $controller */
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;
            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }

        return call_user_func($callback, $this->request, $this->response);
    }
}
