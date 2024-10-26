<?php

namespace App\Controllers\Api;

use App\Controllers\Api\Base\ApiController;
use App\Models\User;
use App\Core\Request;
use App\Core\Response;

class AuthController extends ApiController
{
    public function login(Request $request, Response $response)
    {
        try {
            $body = $request->getBody();
            
            error_log("\n=== API Login Debug ===");
            error_log('Raw Request Body: ' . file_get_contents('php://input'));
            error_log('Parsed Body: ' . json_encode($body));
            error_log('Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
            
            if (!isset($body['email']) || !isset($body['password'])) {
                return $this->sendJson([
                    'error' => 'Email and password are required',
                    'received' => $body,
                    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set'
                ], 400);
            }

            // Debug the exact email being searched
            error_log('Searching for email (API): "' . $body['email'] . '"');
            
            // Get all users for debugging
            $statement = User::prepare("SELECT id, email FROM users LIMIT 5");
            $statement->execute();
            $allUsers = $statement->fetchAll(\PDO::FETCH_ASSOC);
            error_log('Available users in database: ' . json_encode($allUsers));
            
            $user = User::findOne(['email' => $body['email']]);
            
            if (!$user) {
                return $this->sendJson([
                    'error' => 'Invalid credentials',
                    'detail' => 'User not found',
                    'debug' => [
                        'email_searched' => $body['email'],
                        'available_users' => $allUsers
                    ]
                ], 401);
            }

            // Debug password validation
            error_log('Password validation for user:');
            error_log('User ID: ' . $user->getId());
            error_log('Stored Password Hash: ' . $user->password);
            error_log('Provided Password: ' . $body['password']);
            
            if (!$user->validatePassword($body['password'])) {
                error_log('Password validation failed');
                return $this->sendJson([
                    'error' => 'Invalid credentials',
                    'detail' => 'Password validation failed',
                    'debug' => [
                        'user_found' => true,
                        'password_verified' => false
                    ]
                ], 401);
            }

            $token = $this->generateToken($user);

            return $this->sendJson([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname()
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log('API Login Error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return $this->sendJson([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request, Response $response)
    {
        try {
            $body = $request->getBody();
            
            // Debug request
            error_log('Register Request Body: ' . json_encode($body));

            // Set default values for required fields if not provided
            $body['confirmPassword'] = $body['confirmPassword'] ?? $body['password'] ?? '';
            $body['agreeTerms'] = $body['agreeTerms'] ?? true;

            $user = new User();
            $user->loadData($body);

            if (!$user->validate()) {
                return $this->sendJson([
                    'error' => 'Validation failed',
                    'errors' => $user->errors
                ], 422);
            }

            if (!$user->save()) {
                return $this->sendJson([
                    'error' => 'Failed to create user',
                    'errors' => $user->errors
                ], 500);
            }

            $token = $this->generateToken($user);

            return $this->sendJson([
                'success' => true,
                'message' => 'Registration successful',
                'token' => $token,
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname()
                ]
            ], 201);

        } catch (\Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return $this->sendJson([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request, Response $response)
    {
        return $this->sendJson(['message' => 'Logged out successfully']);
    }
}
