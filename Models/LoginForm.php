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
        // Debug web login attempt
        error_log('Web Login Attempt:');
        error_log('Email: ' . $this->email);
        
        $user = User::findOne(['email' => $this->email]);
        
        if (!$user) {
            error_log('Web Login: User not found');
            $this->addError('email', 'User does not exist with this email');
            return false;
        }

        error_log('Web Login: User found with ID: ' . $user->getId());
        
        if (!$user->validatePassword($this->password)) {
            error_log('Web Login: Password validation failed');
            $this->addError('password', 'Password is incorrect');
            return false;
        }
        
        return Application::$app->login($user);
    }
}
