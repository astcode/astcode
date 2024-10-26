<?php

namespace App\Controllers\Api;

use App\Controllers\Api\Base\ApiController;
use App\Models\User;
use App\Core\Request;
use App\Core\Response;
use App\Core\Application;
use App\Models\ApiKey;
use App\Models\ApiRequest;

class UserController extends ApiController
{
    public function profile(Request $request, Response $response)
    {
        try {
            $this->authenticate($request);
            $user = Application::$app->user;

            if (!$user) {
                return $this->sendJson(['error' => 'User not found'], 404);
            }

            return $this->sendJson([
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail()
            ]);
        } catch (\Exception $e) {
            return $this->sendJson(['error' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request, Response $response)
    {
        try {
            $this->authenticate($request);
            $user = Application::$app->user;
            $body = $request->getBody();

            if (!$user) {
                return $this->sendJson(['error' => 'User not found'], 404);
            }

            $user->loadData($body);

            if (!$user->validate()) {
                return $this->sendJson([
                    'error' => 'Validation failed',
                    'errors' => $user->errors
                ], 422);
            }

            if (!$user->save()) {
                return $this->sendJson(['error' => 'Failed to update profile'], 500);
            }

            return $this->sendJson([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->getId(),
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname(),
                    'email' => $user->getEmail()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->sendJson(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllUsers(Request $request, Response $response)
    {
        try {
            // Optional: Add authentication if you want to protect this endpoint
            // $this->authenticate($request);

            $statement = User::prepare("SELECT id, firstname, lastname, email, username, status FROM users");
            $statement->execute();
            $users = $statement->fetchAll(\PDO::FETCH_ASSOC);

            return $this->sendJson([
                'success' => true,
                'count' => count($users),
                'users' => $users
            ]);
        } catch (\Exception $e) {
            error_log('Error fetching users: ' . $e->getMessage());
            return $this->sendJson([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function listApiKeys(Request $request, Response $response)
    {
        try {
            $this->authenticate($request);
            $user = Application::$app->user;

            $statement = ApiKey::prepare("SELECT * FROM api_keys WHERE user_id = :user_id");
            $statement->bindValue(':user_id', $user->getId());
            $statement->execute();
            $keys = $statement->fetchAll(\PDO::FETCH_ASSOC);

            return $this->sendJson([
                'success' => true,
                'keys' => array_map(function($key) {
                    return [
                        'id' => $key['id'],
                        'name' => $key['name'],
                        'key' => $key['key'],
                        'is_active' => (bool)$key['is_active'],
                        'expires_at' => $key['expires_at'],
                        'last_used_at' => $key['last_used_at'],
                        'created_at' => $key['created_at']
                    ];
                }, $keys)
            ]);
        } catch (\Exception $e) {
            return $this->sendJson(['error' => $e->getMessage()], 500);
        }
    }

    public function createApiKey(Request $request, Response $response)
    {
        try {
            $this->authenticate($request);
            $user = Application::$app->user;
            $body = $request->getBody();

            // Create new API key
            $apiKey = new ApiKey();
            $apiKey->user_id = $user->id;
            $apiKey->name = $body['name'] ?? 'API Key ' . date('Y-m-d H:i:s');
            $apiKey->key = bin2hex(random_bytes(32));
            $apiKey->is_active = true;
            
            // Simple SQL insert
            $sql = "INSERT INTO api_keys (user_id, `key`, name, is_active) VALUES (:user_id, :key, :name, :is_active)";
            $stmt = ApiKey::prepare($sql);
            $stmt->bindValue(':user_id', $apiKey->user_id);
            $stmt->bindValue(':key', $apiKey->key);
            $stmt->bindValue(':name', $apiKey->name);
            $stmt->bindValue(':is_active', 1);
            
            if ($stmt->execute()) {
                // Check if this is a web form submission
                if (!$request->isJson()) {
                    Application::$app->session->setFlash('success', 'API key created successfully');
                    $response->redirect('/api-keys');
                    return;
                }

                // API response
                return $this->sendJson([
                    'success' => true,
                    'message' => 'API key created successfully',
                    'key' => [
                        'id' => ApiKey::prepare('SELECT LAST_INSERT_ID()')->fetchColumn(),
                        'name' => $apiKey->name,
                        'key' => $apiKey->key
                    ]
                ], 201);
            } else {
                if (!$request->isJson()) {
                    Application::$app->session->setFlash('error', 'Failed to create API key');
                    $response->redirect('/api-keys');
                    return;
                }

                return $this->sendJson([
                    'error' => 'Failed to create API key'
                ], 500);
            }
        } catch (\Exception $e) {
            error_log('Error creating API key: ' . $e->getMessage());
            if (!$request->isJson()) {
                Application::$app->session->setFlash('error', 'Error creating API key');
                $response->redirect('/api-keys');
                return;
            }
            return $this->sendJson([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteApiKey(Request $request, Response $response)
    {
        try {
            $body = $request->getBody();
            
            // Skip API key check for web form submissions
            if (!$request->isJson()) {
                // Verify CSRF token
                $token = $body['csrf_token'] ?? '';
                if (!$token || $token !== Application::$app->session->get('csrf_token')) {
                    $response->redirect('/api-keys');
                    return;
                }
                
                $user = Application::$app->user;
                
                // Delete the key if it belongs to the user
                $sql = "DELETE FROM api_keys WHERE id = :id AND user_id = :user_id";
                $stmt = ApiKey::prepare($sql);
                $stmt->bindValue(':id', $body['id']);
                $stmt->bindValue(':user_id', $user->id);
                $stmt->execute();
                
                // Set success flash message
                Application::$app->session->setFlash('success', 'API key deleted successfully');
                
                // Redirect back to the API keys page
                $response->redirect('/api-keys');
                return;
            }

            // Handle API requests (if needed)
            $this->authenticate($request);
            // ... rest of API handling code ...
            
        } catch (\Exception $e) {
            error_log('Error deleting API key: ' . $e->getMessage());
            if ($request->isJson()) {
                return $this->sendJson(['error' => $e->getMessage()], 500);
            }
            $response->redirect('/api-keys');
        }
    }

    public function apiUsage(Request $request, Response $response)
    {
        try {
            $this->authenticate($request);
            $user = Application::$app->user;

            $sql = "SELECT 
                        ar.endpoint,
                        COUNT(*) as total_requests,
                        AVG(ar.response_code) as avg_response_code,
                        MAX(ar.created_at) as last_used
                    FROM api_keys ak
                    JOIN api_requests ar ON ar.api_key_id = ak.id
                    WHERE ak.user_id = :user_id
                    GROUP BY ar.endpoint
                    ORDER BY total_requests DESC";

            $stmt = ApiKey::prepare($sql);
            $stmt->bindValue(':user_id', $user->getId());
            $stmt->execute();
            
            return $this->sendJson([
                'success' => true,
                'usage' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
            ]);
        } catch (\Exception $e) {
            return $this->sendJson(['error' => $e->getMessage()], 500);
        }
    }

    public function getUsageStats(Request $request, Response $response)
    {
        try {
            $this->authenticate($request);
            $user = Application::$app->user;

            // Get all API keys for this user
            $stmt = ApiKey::prepare("SELECT `key` FROM api_keys WHERE user_id = :user_id");
            $stmt->bindValue(':user_id', $user->id);
            $stmt->execute();
            $keys = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (empty($keys)) {
                return $this->sendJson(['message' => 'No API keys found'], 404);
            }

            // Create placeholders for SQL IN clause
            $placeholders = str_repeat('?,', count($keys) - 1) . '?';

            // Get usage statistics
            $sql = "SELECT 
                        endpoint,
                        method,
                        COUNT(*) as total_requests,
                        AVG(response_code) as avg_response_code,
                        MAX(created_at) as last_request,
                        MIN(created_at) as first_request
                    FROM api_requests 
                    WHERE api_key IN ($placeholders)
                    GROUP BY endpoint, method
                    ORDER BY total_requests DESC";

            $stmt = ApiRequest::prepare($sql);
            $stmt->execute($keys);
            $stats = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get hourly request counts for the last 24 hours
            $hourly = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour,
                        COUNT(*) as requests
                    FROM api_requests 
                    WHERE api_key IN ($placeholders)
                        AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    GROUP BY hour
                    ORDER BY hour";

            $stmt = ApiRequest::prepare($hourly);
            $stmt->execute($keys);
            $hourlyStats = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $this->sendJson([
                'success' => true,
                'total_requests' => array_sum(array_column($stats, 'total_requests')),
                'endpoints' => $stats,
                'hourly_requests' => $hourlyStats
            ]);

        } catch (\Exception $e) {
            error_log('Error getting usage stats: ' . $e->getMessage());
            return $this->sendJson(['error' => $e->getMessage()], 500);
        }
    }
}
