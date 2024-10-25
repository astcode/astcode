<?php

namespace App\Core;

/**
 * Class Request
 * @package App\Core
 * @author Aaron Thomas <aaron@aaronsthomas.com>
 */
class Request
{

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

    public function isGet()
    {
        return $this->method() === 'get';
    }
}
