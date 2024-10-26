<?php

namespace App\Controllers\Api\Base;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Application;
use App\Models\User;
use App\Models\ApiKey;
use Firebase\JWT\JWT;
use App\Models\ApiRequest;

class ApiController extends Controller
{
    public function __construct()
    {
        // Only authenticate for API routes, not web routes
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            $this->authenticate(new Request());
        }
    }

    protected function authenticate(Request $request)
    {
        $headers = getallheaders();
        
        // Skip authentication for web form submissions to these endpoints
        $skipAuthPaths = [
            '/api/v1/keys',
            '/api/v1/keys/delete'
        ];
        
        if (!$request->isJson() && in_array($request->getPath(), $skipAuthPaths)) {
            return;
        }

        // Skip authentication for API key creation
        if ($request->getPath() === '/api/v1/keys' && $request->method() === 'post') {
            error_log('Skipping authentication for API key creation');
            return;
        }

        // Check for API Key in headers
        if (!isset($headers['X-Api-Key'])) {
            error_log('No API key found in headers');
            $this->sendJson(['error' => 'API key is required'], 401);
            exit;
        }

        // Get API key from headers
        $apiKey = $headers['X-Api-Key'];
        error_log('Looking up API key: ' . $apiKey);

        // Get user_id from api_keys table
        $sql = "SELECT user_id FROM api_keys WHERE `key` = :key AND is_active = 1";
        $stmt = ApiKey::prepare($sql);
        $stmt->bindValue(':key', $apiKey);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        error_log('API key lookup result: ' . json_encode($result));

        if (!$result) {
            error_log('API key not found or not active');
            $this->sendJson(['error' => 'Invalid API key'], 401);
            exit;
        }

        // Get user by ID
        $userId = $result['user_id'];
        error_log('Found user_id: ' . $userId);
        
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            error_log('User not found for ID: ' . $userId);
            $this->sendJson(['error' => 'Invalid user'], 401);
            exit;
        }

        error_log('Authentication successful for user ID: ' . $userId);
        Application::$app->user = $user;
    }

    protected function logApiRequest(string $apiKey, int $statusCode)
    {
        try {
            // Direct SQL insert for debugging
            $sql = "INSERT INTO api_requests (api_key, endpoint, method, response_code, created_at) 
                    VALUES (:api_key, :endpoint, :method, :response_code, NOW())";
            
            $stmt = ApiRequest::prepare($sql);
            $stmt->bindValue(':api_key', $apiKey);
            $stmt->bindValue(':endpoint', $_SERVER['REQUEST_URI']);
            $stmt->bindValue(':method', $_SERVER['REQUEST_METHOD']);
            $stmt->bindValue(':response_code', $statusCode);
            
            error_log('Logging API request:');
            error_log('API Key: ' . $apiKey);
            error_log('Endpoint: ' . $_SERVER['REQUEST_URI']);
            error_log('Method: ' . $_SERVER['REQUEST_METHOD']);
            error_log('Status: ' . $statusCode);
            
            if ($stmt->execute()) {
                error_log('API request logged successfully');
            } else {
                error_log('Failed to log API request: ' . json_encode($stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            error_log('Failed to log API request: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
        }
    }

    protected function sendJson($data, $statusCode = 200)
    {
        // Get API key from headers if available
        $headers = getallheaders();
        $apiKey = $headers['X-Api-Key'] ?? null;

        // Log the request if we have an API key
        if ($apiKey) {
            $this->logApiRequest($apiKey, $statusCode);
        }

        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    public function generateToken(User $user)
    {
        $payload = [
            'iss' => $_ENV['APP_URL'] ?? 'http://astcode.test',
            'aud' => $_ENV['APP_URL'] ?? 'http://astcode.test',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + ($_ENV['JWT_EXPIRATION'] ?? 3600),
            'user_id' => $user->getId(),
            'user_email' => $user->getEmail(),
            'user_name' => $user->getFullName()
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET'] ?? 'your-default-secret-key', 'HS256');
    }

    protected function checkRateLimit($apiKey)
    {
        $hourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $sql = "SELECT COUNT(*) FROM api_requests 
                WHERE api_key = :key 
                AND created_at > :time";
        
        $stmt = ApiRequest::prepare($sql);
        $stmt->bindValue(':key', $apiKey);
        $stmt->bindValue(':time', $hourAgo);
        $count = $stmt->fetchColumn();
        
        return $count < 100; // 100 requests per hour
    }
}
