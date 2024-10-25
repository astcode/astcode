<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Application;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';
    public bool $rememberMe = false;

    public function rules(): array
    {
        return [
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'password' => [self::RULE_REQUIRED],
            'rememberMe' => []
        ];
    }

    public function labels(): array
    {
        return [
            'email' => 'Email',
            'password' => 'Password',
            'rememberMe' => 'Remember Me'
        ];
    }

    public function login()
    {
        $user = User::findOne(['email' => $this->email]);
        
        if (!$user) {
            $this->addError('email', 'User does not exist with this email');
            return false;
        }
        if (!password_verify($user->salt . $this->password, $user->password)) {
            $this->addError('password', 'Password is incorrect');
            return false;
        }
        
        return Application::$app->login($user);
    }

    // logout function
    public function logout()
    {
        Application::$app->logout();
        return true;
    }
    
}
