<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Controller;
use App\Models\SurveyForm;
use App\Models\ApiKey;
use App\Core\Application;
use App\Controllers\Api\Base\ApiController;
use App\Models\ApiRequest;

class UserController extends Controller
{
    private $apiController;

    public function __construct()
    {
        $this->apiController = new ApiController();
    }

    /**
     * Display the survey form and handle submissions.
     *
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function survey(Request $request, Response $response)
    {
        $model = new SurveyForm();

        if ($request->isPost()) {
            $model->loadData($request->getBody());

            if ($model->validate()) {
                // TODO: Process the survey data (e.g., save to database)
                // For demonstration, we'll assume it's successful

                // Redirect to a thank-you page
                $response->redirect('/thank-you');
                return;
            }
        }

        // Render the form (either first load or with validation errors)
        return $this->render('user/survey', [
            'model' => $model,
        ]);
    }

    /**
     * Display the thank-you page after successful submission.
     *
     * @return string
     */
    public function thankYou()
    {
        return $this->render('user/thank_you');
    }

    public function apiKeys(Request $request, Response $response)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        $user = Application::$app->user;
        
        // Generate a JWT token for API requests
        $token = $this->apiController->generateToken($user);
        
        // Get user's API keys
        $statement = ApiKey::prepare("SELECT * FROM api_keys WHERE user_id = :user_id ORDER BY created_at DESC");
        $statement->bindValue(':user_id', $user->getId());
        $statement->execute();
        $apiKeys = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('userpages/api_keys', [
            'apiKeys' => $apiKeys,
            'token' => $token
        ]);
    }

    public function apiStats(Request $request, Response $response)
    {
        if (Application::isGuest()) {
            $response->redirect('/login');
            return;
        }

        $user = Application::$app->user;
        
        // Get API usage statistics
        $stats = $this->getApiStats($user);
        
        return $this->render('userpages/api_stats', [
            'stats' => $stats
        ]);
    }

    private function getApiStats($user)
    {
        // Get all API keys for this user
        $stmt = ApiKey::prepare("SELECT `key` FROM api_keys WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $user->id);
        $stmt->execute();
        $keys = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($keys)) {
            return [
                'total_requests' => 0,
                'success_rate' => 0,
                'last_24h_requests' => 0,
                'endpoints' => [],
                'hourly_requests' => []
            ];
        }

        $placeholders = str_repeat('?,', count($keys) - 1) . '?';

        // Get endpoint statistics
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
        $endpoints = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Modified hourly stats query to be more explicit
        $hourly = "SELECT 
                DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour,
                COUNT(*) as requests,
                GROUP_CONCAT(id) as request_ids  -- Add this for debugging
            FROM api_requests 
            WHERE api_key IN ($placeholders)
                AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')
            ORDER BY hour";

        $stmt = ApiRequest::prepare($hourly);
        $stmt->execute($keys);
        $hourlyData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Debug logging
        error_log('API Keys: ' . json_encode($keys));
        error_log('Hourly Query: ' . $hourly);
        error_log('Hourly Data: ' . json_encode($hourlyData));

        // Fill in missing hours with zeros
        $hourlyStats = [];
        $now = new \DateTime();
        for ($i = 23; $i >= 0; $i--) {
            $hour = clone $now;
            $hour->modify("-{$i} hours");
            $hourKey = $hour->format('Y-m-d H:00:00');
            
            $found = false;
            foreach ($hourlyData as $data) {
                if ($data['hour'] === $hourKey) {
                    $hourlyStats[] = [
                        'hour' => $hourKey,
                        'requests' => (int)$data['requests']
                    ];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $hourlyStats[] = [
                    'hour' => $hourKey,
                    'requests' => 0
                ];
            }
        }

        error_log('Final Hourly Stats: ' . json_encode($hourlyStats));

        // Calculate additional statistics
        $success_rate = 0;
        $last_24h_requests = 0;
        if (!empty($endpoints)) {
            $total_success = array_sum(array_map(function($endpoint) {
                return $endpoint['avg_response_code'] < 400 ? $endpoint['total_requests'] : 0;
            }, $endpoints));
            $total_requests = array_sum(array_column($endpoints, 'total_requests'));
            $success_rate = $total_requests > 0 ? ($total_success / $total_requests) * 100 : 0;
            
            // Calculate last 24h requests
            $last_24h_requests = array_sum(array_column($hourlyStats, 'requests'));
        }

        error_log('Hourly stats: ' . json_encode($hourlyStats));

        return [
            'total_requests' => array_sum(array_column($endpoints, 'total_requests')),
            'success_rate' => $success_rate,
            'last_24h_requests' => $last_24h_requests,
            'endpoints' => $endpoints,
            'hourly_requests' => $hourlyStats
        ];
    }
}
