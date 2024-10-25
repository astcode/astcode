<?php

namespace App\Models;

use App\Core\Application;
use App\Core\Model;
use App\Core\UserModel;

class User extends UserModel
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public int $status = self::STATUS_INACTIVE;
    public string $password = '';
    public string $confirmPassword = '';
    public ?string $salt = null; // Make salt nullable
    public ?string $username = null; // Make username nullable
    public bool $agreeTerms = false; // Add this line
    public ?string $passwordResetToken = null;
    public ?string $passwordResetExpires = null;

    public function tableName(): string
    {
        return 'users';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function save()
    {
        $this->status = self::STATUS_INACTIVE;
        $this->salt = bin2hex(random_bytes(16));
        $this->password = password_hash($this->salt . $this->password, PASSWORD_DEFAULT);
        return parent::save();
    }

    public function rules(): array
    {
        return [
            'firstname' => [self::RULE_REQUIRED],
            'lastname' => [self::RULE_REQUIRED],
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_UNIQUE, 'class' => self::class]],
            'username' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'class' => self::class]], // Add this line
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
            'confirmPassword' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
            'agreeTerms' => [self::RULE_REQUIRED],
        ];
    }

    public function attributes(): array
    {
        return ['firstname', 'lastname', 'email', 'username', 'password', 'status', 'salt'];
    }

    public function labels(): array
    {
        return [
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'email' => 'Email',
            'username' => 'Username', // Add this line
            'password' => 'Password',
            'confirmPassword' => 'Confirm Password',
            'agreeTerms' => 'I agree to the Terms and Conditions'
        ];
    }

    public function getDisplayName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getStatusText(): string
    {
        return match ($this->status) {
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_DELETED => 'Deleted',
            default => 'Unknown'
        };
    }

    public function getRole(): string
    {
        return 'User';
    }

    public function getRoleText(): string
    {
        return 'User';
    }

    public function getRoleColor(): string
    {
        return 'primary';
    }

    public function getRoleIcon(): string
    {
        return 'fas fa-user';
    }

    public static function nameOrGuest()
    {
        switch (!Application::isGuest()) {
            case true:
                // $name = makeName(Application::$app->user);
                $name = Application::$app->user->getDisplayName();
                break;
            default:
                $name = 'Guest';
                break;
        }
        return $name;
    }

    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = bin2hex(random_bytes(32));
        $this->passwordResetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->save();
    }

    public static function findByPasswordResetToken(string $token): ?User
    {
        return static::findOne([
            'passwordResetToken' => $token,
            'passwordResetExpires' => ['>', date('Y-m-d H:i:s')]
        ]);
    }

    public function resetPassword(string $password)
    {
        $this->password = $password;
        $this->passwordResetToken = null;
        $this->passwordResetExpires = null;
        return $this->save();
    }
}
