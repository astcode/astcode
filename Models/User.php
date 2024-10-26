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

    public ?int $id = null;  // Add this line
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
        
        // Only generate new salt and hash password if it's a new user or password has changed
        if ($this->id === null || isset($this->password)) {
            $this->salt = bin2hex(random_bytes(16));
            $this->password = password_hash($this->salt . $this->password, PASSWORD_DEFAULT);
        }
        
        $isNewUser = $this->id === null;
        $result = parent::save();
        
        // Assign default role to new users
        if ($isNewUser && $result) {
            $defaultRole = Role::findOne(['name' => 'user']);
            if ($defaultRole) {
                $this->assignRole($defaultRole->id);
            }
        }
        
        return $result;
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

    public function getId(): ?int
    {
        // If using a property
        if (isset($this->id)) {
            return (int)$this->id;
        }
        
        // If using a database record
        if (isset($this->{self::primaryKey()})) {
            return (int)$this->{self::primaryKey()};
        }
        
        return null;
    }

    public function getFirstname(): string
    {
        return $this->firstname ?? '';
    }

    public function getLastname(): string
    {
        return $this->lastname ?? '';
    }

    public function validatePassword($password): bool
    {
        try {
            error_log('Starting password validation');
            
            if (empty($this->password)) {
                error_log('Missing password hash');
                return false;
            }

            // Debug logging
            error_log('Password validation details:');
            error_log('- Salt: ' . $this->salt);
            error_log('- Input password: ' . $password);
            error_log('- Stored hash: ' . $this->password);
            
            // Try both methods of password verification
            $saltedPassword = $this->salt . $password;
            $directVerify = password_verify($password, $this->password);
            $saltedVerify = password_verify($saltedPassword, $this->password);
            
            error_log('Validation attempts:');
            error_log('- Direct verify: ' . ($directVerify ? 'true' : 'false'));
            error_log('- Salted verify: ' . ($saltedVerify ? 'true' : 'false'));
            
            // Return true if either method works
            $isValid = $directVerify || $saltedVerify;
            error_log('Final validation result: ' . ($isValid ? 'true' : 'false'));
            
            return $isValid;
            
        } catch (\Exception $e) {
            error_log('Password validation error: ' . $e->getMessage());
            return false;
        }
    }

    public static function findOne($where)
    {
        try {
            $tableName = (new static)->tableName();
            $attributes = array_keys($where);
            $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
            $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
            
            // Debug SQL and parameters
            error_log("=== Debug findOne ===");
            error_log("Table name: " . $tableName);
            error_log("Full SQL: SELECT * FROM $tableName WHERE $sql");
            error_log("Parameters: " . json_encode($where));
            
            foreach ($where as $key => $item) {
                $statement->bindValue(":$key", $item);
                error_log("Binding $key = " . $item);
            }
            
            $statement->execute();
            
            // Debug the actual SQL that was executed
            ob_start();
            $statement->debugDumpParams();
            $debugSql = ob_get_clean();
            error_log("Executed SQL with params: " . $debugSql);
            
            $result = $statement->fetchObject(static::class);
            
            // Debug the result
            if ($result) {
                error_log("Record found: " . json_encode([
                    'id' => $result->id ?? 'null',
                    'email' => $result->email ?? 'null',
                    'firstname' => $result->firstname ?? 'null'
                ]));
            } else {
                error_log("No record found");
                // Debug the actual database content
                $checkStatement = self::prepare("SELECT * FROM $tableName LIMIT 5");
                $checkStatement->execute();
                $sampleRecords = $checkStatement->fetchAll(\PDO::FETCH_ASSOC);
                error_log("Sample records from table: " . json_encode($sampleRecords));
            }
            
            return $result;
        } catch (\Exception $e) {
            error_log("Database error in findOne: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getRoles(): array
    {
        $sql = "SELECT r.* FROM roles r 
                JOIN user_roles ur ON r.id = ur.role_id 
                WHERE ur.user_id = :user_id";
        $stmt = self::prepare($sql);
        $stmt->bindValue(':user_id', $this->id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        $roles = $this->getRoles();
        return in_array($roleName, array_column($roles, 'name'));
    }

    public function hasPermission(string $permission): bool
    {
        error_log('Checking permission: ' . $permission);
        error_log('User ID: ' . $this->id);
        
        $roles = $this->getRoles();
        error_log('User roles: ' . json_encode(array_map(function($role) {
            return $role->name;
        }, $roles)));
        
        foreach ($roles as $role) {
            error_log('Checking role: ' . $role->name);
            if ($role->hasPermission($permission)) {
                error_log('Permission granted by role: ' . $role->name);
                return true;
            }
        }
        
        error_log('Permission denied');
        return false;
    }

    public function assignRole(int $roleId): bool
    {
        $sql = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
        $stmt = self::prepare($sql);
        $stmt->bindValue(':user_id', $this->id);
        $stmt->bindValue(':role_id', $roleId);
        return $stmt->execute();
    }

    public function removeRole(int $roleId): bool
    {
        $sql = "DELETE FROM user_roles WHERE user_id = :user_id AND role_id = :role_id";
        $stmt = self::prepare($sql);
        $stmt->bindValue(':user_id', $this->id);
        $stmt->bindValue(':role_id', $roleId);
        return $stmt->execute();
    }
}
