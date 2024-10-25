<?php

namespace App\Core\Exception;

class ForbiddenExeption extends \Exception
{
    protected $message = 'You don\'t have permission to access this page';
    protected $code = 403;
}
