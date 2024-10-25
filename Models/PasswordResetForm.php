<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Application;

class PasswordResetForm extends Model
{
    public string $email = '';
    public string $password = '';
    public string $confirmPassword = '';

    public function rules(): array
    {
        return [
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
            'confirmPassword' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']]
        ];
    }

    public function sendResetLink()
    {
        $user = User::findOne(['email' => $this->email]);
        if ($user) {
            $user->generatePasswordResetToken();
            // Here you would typically send an email with the reset link
            // For now, we'll just log it
            error_log("Password reset link: http://yourwebsite.com/reset-password?token=" . $user->passwordResetToken);
        }
        return true;
    }

    public function resetPassword(User $user)
    {
        return $user->resetPassword($this->password);
    }
}
