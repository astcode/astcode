<?php

namespace App\Core\Exception;

class NotFoundException extends \Exception
{
    protected $message = 'Page not found';
    protected $code = 404;

    // public function __construct($message = '', $code = 0, \Throwable $previous = null)
    // {
    //     parent::__construct($message, $code, $previous);
    // }
}
