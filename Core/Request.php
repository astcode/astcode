<?php

namespace App\Core;

/**
 * Class Request
 * @package App\Core
 * @author Aaron Thomas <aaron@aaronsthomas.com>
 */
class Request
{
    private array $routeParams = [];

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if ($position === false) {
            return $path;
        }
        
        $path = substr($path, 0, $position);
        return $path;
    }
    
    public function method()
    {
        $request = strtolower($_SERVER['REQUEST_METHOD']);
        return $request;
    }

    public function getBody()
    {
        $body = [];

        if ($this->isJson()) {
            $json = file_get_contents('php://input');
            $body = json_decode($json, true) ?? [];
            return $body;
        }

        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

    public function isPost()
    {
        return $this->method() === 'post';
    }

    public function isJson()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($contentType, 'application/json') !== false;
    }

    public function setRouteParams($params)
    {
        $this->routeParams = $params;
        return $this;
    }

    public function getRouteParams()
    {
        return $this->routeParams;
    }

    public function getRouteParam($param, $default = null)
    {
        return $this->routeParams[$param] ?? $default;
    }
}
