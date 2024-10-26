<?php

namespace App\Core;

use App\Models\User;

use App\Core\DB\Database;
use App\Core\DB\DbModel;
use App\Core\Middlewares\BaseMiddleware;
use App\Core\Middlewares\CsrfMiddleware;

// namespace App\Application;
/**
 * @author Aaron Thomas <aaron@aaronsthomas.com>
 * @package App\Core
 */
class Application
{
    public static string $ROOT_DIR;

    public string $layout = 'main';
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?UserModel $user = null; // Ensure this is nullable
    public View $view;

    public static Application $app;
    public ?Controller $controller = null;

    public $isUserLoggedIn = false;

    public array $middlewares = [];

    public function __construct($rootPath, $config)
    {
        $this->userClass = Config::getUserClass();
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->db = new Database(Config::getDbConfig());

        // Attempt to find the logged-in user
        $primaryKey = $this->userClass::primaryKey();
        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]) ?: null;
        }

        $this->registerMiddleware(new CsrfMiddleware());
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\PDOException $e) {
            $this->response->setStatusCode(500);
            echo $this->view->renderView('_error', [
                'exception' => new \Exception('Database error occurred', 500, $e)
            ]);
        } catch (\Exception $e) {
            $code = is_int($e->getCode()) && $e->getCode() !== 0 ? $e->getCode() : 500;
            $this->response->setStatusCode($code);
            echo $this->view->renderView('_error', [
                'exception' => $e
            ]);
        }
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function login(UserModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryKeyValue = $user->{$primaryKey};
        $this->session->set('user', $primaryKeyValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

}
