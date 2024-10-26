<?php

namespace App\Controllers;

// use App\Core\Application;

use App\Models\User;
use App\Core\Request;
use App\Core\Response;
use App\Core\Controller;
use App\Core\Application;
use App\Models\LoginForm;

class AuthController extends Controller
{
    public function login(Request $request, Response $response)
    {
        // Redirect if already logged in
        if (!Application::isGuest()) {
            $response->redirect('/');
            return;
        }

        $loginForm = new LoginForm();
        if ($request->isPost()) {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login()) {
                $response->redirect('/');
                return;
            }
        }
        $this->setLayout('auth');
        return $this->render('auth/login', [
            'model' => $loginForm
        ]);
    }

    public function register(Request $request)
    {
        // Redirect if already logged in
        if (!Application::isGuest()) {
            Application::$app->response->redirect('/');
            return;
        }

        $user = new User();
        if ($request->isPost()) {
            $user->loadData($request->getBody());

            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash('success', 'Thanks for registering');
                Application::$app->response->redirect('/');
                return 'Show success page';
            }
        }
        $this->setLayout('auth');
        return $this->render('auth/register', [
            'model' => $user
        ]);
    }

    public function logout(Request $request, Response $response)
    {
        // Only allow logout if logged in
        if (!Application::isGuest()) {
            Application::$app->logout();
            Application::$app->session->setFlash('success', 'You have been logged out');
        }
        $response->redirect('/');
    }
    
    public function profile()
    {
        // Only allow profile access if logged in
        if (Application::isGuest()) {
            Application::$app->response->redirect('/login');
            return;
        }
        return $this->render('profile');
    }
}
