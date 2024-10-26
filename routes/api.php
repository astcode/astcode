<?php

use App\Controllers\Api\AuthController as ApiAuthController;
use App\Controllers\Api\UserController as ApiUserController;
use App\Controllers\Api\SurveyController as ApiSurveyController;

// Debug log to verify route registration
error_log("Registering API routes...");

$app->router->group(['prefix' => '/api/v1'], function($router) {
    // API Key Routes
    $router->get('/keys', [ApiUserController::class, 'listApiKeys']);
    $router->post('/keys', [ApiUserController::class, 'createApiKey']);
    $router->post('/keys/delete', [ApiUserController::class, 'deleteApiKey']);

    // User Routes
    $router->get('/users', [ApiUserController::class, 'getAllUsers']);
    $router->get('/user/profile', [ApiUserController::class, 'profile']);
    $router->put('/user/profile', [ApiUserController::class, 'updateProfile']);
    $router->delete('/user/:id', [ApiUserController::class, 'deleteUser']);
    $router->get('/usage/stats', [ApiUserController::class, 'getUsageStats']);

    // Authentication Routes
    $router->post('/auth/login', [ApiAuthController::class, 'login']);
    $router->post('/auth/register', [ApiAuthController::class, 'register']);
    $router->post('/auth/logout', [ApiAuthController::class, 'logout']);
    $router->post('/auth/forgot-password', [ApiAuthController::class, 'forgotPassword']);
    $router->post('/auth/reset-password', [ApiAuthController::class, 'resetPassword']);

    // Survey Routes
    $router->get('/survey', [ApiSurveyController::class, 'getSurvey']);
    $router->post('/survey', [ApiSurveyController::class, 'submitSurvey']);
});

// Add debug logging
// error_log("Available routes after registration:");
// error_log(print_r($app->router->getRoutes(), true));
