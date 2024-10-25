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

$app->router->get('/', [SiteController::class, 'home']);
$app->router->get('/home', [SiteController::class, 'home']);

$app->router->get('/contact', [SiteController::class, 'contact']);
$app->router->post('/contact', [SiteController::class, 'contact']);

$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login']);

$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'register']);

$app->router->get('/logout', [AuthController::class, 'logout']);
// $app->router->post('/logout', [AuthController::class, 'logout']);

$app->router->get('/profile', [AuthController::class, 'profile']);

// **New Routes for Survey Form**
$app->router->get('/survey', [UserController::class, 'survey']);
$app->router->post('/survey', [UserController::class, 'survey']);
$app->router->get('/thank-you', [UserController::class, 'thankYou']);

$app->router->get('/thankyou', [SiteController::class, 'thankyou']);

// $app->router->get('/users', function() {
//     return 'users';
// });

$app->router->get('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
$app->router->post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
$app->router->get('/reset-password', [PasswordResetController::class, 'resetPassword']);
$app->router->post('/reset-password', [PasswordResetController::class, 'resetPassword']);

$app->run();
