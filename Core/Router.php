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
    protected string $prefix = '';

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $path = $this->prefix . $path;
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $path = $this->prefix . $path;
        $this->routes['post'][$path] = $callback;
    }

    public function put($path, $callback)
    {
        $path = $this->prefix . $path;
        $this->routes['put'][$path] = $callback;
    }

    public function delete($path, $callback)
    {
        $path = $this->prefix . $path;
        $this->routes['delete'][$path] = $callback;
    }

    public function group(array $attributes, callable $callback)
    {
        $previousPrefix = $this->prefix;
        if (isset($attributes['prefix'])) {
            $this->prefix = $previousPrefix . $attributes['prefix'];
        }
        call_user_func($callback, $this);
        $this->prefix = $previousPrefix;
    }

    protected function findRoute($method, $path)
    {
        // error_log("=== Router Debug ===");
        // error_log("Looking for route: $method $path");
        
        foreach ($this->routes[$method] ?? [] as $routePath => $callback) {
            // error_log("Checking route pattern: $routePath");
            
            // Convert route parameters to regex pattern
            $pattern = preg_replace('/\:([^\/]+)/', '(?P<$1>[^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';
            // error_log("Converted to regex: $pattern");

            if (preg_match($pattern, $path, $matches)) {
                // error_log("Route matched! Matches: " . json_encode($matches));
                // Remove numeric keys from matches
                $params = array_filter($matches, function($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);
                error_log("Extracted params: " . json_encode($params));

                return ['callback' => $callback, 'params' => $params];
            }
        }
        error_log("No matching route found");
        return null;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = strtolower($this->request->method());

        $route = $this->findRoute($method, $path);
        if (!$route) {
            if (str_starts_with($path, '/api/')) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'error' => 'Route not found',
                    'path' => $path,
                    'method' => $method
                ]);
                exit;
            }
            throw new NotFoundException();
        }

        $callback = $route['callback'];
        $params = $route['params'] ?? [];

        if (is_array($callback)) {
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }

        // Create array of arguments in the correct order
        $args = [$this->request, $this->response];
        if (!empty($params)) {
            $args = array_merge($args, array_values($params));
        }

        error_log("Calling controller with args: " . json_encode($args));
        return call_user_func_array($callback, $args);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
