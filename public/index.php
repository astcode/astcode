<?php

use Dotenv\Dotenv;
use App\Core\Config;
use App\Models\User;
use App\Core\Application;
use App\Controllers\AuthController;
use App\Controllers\SiteController;
use App\Controllers\UserController;
use App\Controllers\PasswordResetController;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/functions.php';

// Remove this line:
// session_start();

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = Config::getConfig();

$app = new Application(dirname(__DIR__), $config);

// Include the routes file
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';  // Add this line

$app->run();
