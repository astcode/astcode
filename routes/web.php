<?php

use App\Controllers\AuthController;
use App\Controllers\SiteController;
use App\Controllers\UserController;
use App\Controllers\PasswordResetController;
use App\Controllers\RoleController;
use App\Controllers\PermissionController;

// Define routes
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

$app->router->get('/survey', [UserController::class, 'survey']);
$app->router->post('/survey', [UserController::class, 'survey']);
$app->router->get('/thank-you', [UserController::class, 'thankYou']);

$app->router->get('/thankyou', [SiteController::class, 'thankyou']);

$app->router->get('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
$app->router->post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
$app->router->get('/reset-password', [PasswordResetController::class, 'resetPassword']);
$app->router->post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Add this route
$app->router->get('/api-keys', [UserController::class, 'apiKeys']);

// Add this route
$app->router->get('/api-stats', [UserController::class, 'apiStats']);

// Role management routes - using :id format
$app->router->get('/roles', [RoleController::class, 'index']);
$app->router->get('/roles/create', [RoleController::class, 'create']);
$app->router->post('/roles/create', [RoleController::class, 'create']);
$app->router->get('/roles/edit/:id', [RoleController::class, 'edit']); // Changed to :id
$app->router->post('/roles/edit/:id', [RoleController::class, 'edit']); // Changed to :id
$app->router->post('/roles/delete/:id', [RoleController::class, 'delete']); // Changed to :id

// Permission management routes
$app->router->get('/permissions', [PermissionController::class, 'index']);
$app->router->get('/permissions/create', [PermissionController::class, 'create']);
$app->router->post('/permissions/create', [PermissionController::class, 'create']);
$app->router->get('/permissions/edit/:id', [PermissionController::class, 'edit']);
$app->router->post('/permissions/edit/:id', [PermissionController::class, 'edit']);
$app->router->post('/permissions/delete/:id', [PermissionController::class, 'delete']);
