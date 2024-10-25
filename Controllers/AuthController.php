<?php

namespace App\Controllers;

// use App\Core\Application;

use App\Models\User;
use App\Core\Request;
use App\Core\Response;
use App\Core\Controller;
use App\Core\Application;
use App\Models\LoginForm;
use App\Core\Middlewares\AuthMiddleware;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }
    
    public function index()
    {
        return 'index';
    }

    public function login(Request $request, Response $response)
    {
        $loginForm = new LoginForm();
        if ($request->isPost()) {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login()) {
                $response->redirect('/');
                return;
            }
            return $this->render('auth/login', [
                'model' => $loginForm
            ]);
        }
        $this->setLayout('auth');
        return $this->render('auth/login', [
            'model' => $loginForm
        ]);
    }

    public function register($request)
    {
        $user = new User();

        if ($request->isPost()) {            
            $user->loadData($request->getBody());
            
            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash('success', 'Thank you for registering');
                Application::$app->response->redirect('/');
            }
            
            return $this->render('auth/register', [
                'model' => $user
            ]);
        }
        $this->setLayout('auth');
        return $this->render('auth/register', [
            'model' => $user
        ]);
    }

    public function logout($request, $response)
    {
        Application::$app->logout();
        Application::$app->response->redirect('/');
    }
    
    public function profile()
    {
        return $this->render('userpages/profile');
    }

}
