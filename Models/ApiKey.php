<?php

namespace App\Models;

use App\Core\DB\DbModel;

class ApiKey extends DbModel
{
    public ?int $id = null;
    public ?int $user_id = null;
    public string $key = '';
    public string $name = '';
    public bool $is_active = true;
    public ?string $expires_at = null;
    public ?string $last_used_at = null;
    public array $permissions = [];

    public function tableName(): string
    {
        return 'api_keys';
    }

    public function attributes(): array
    {
        return ['user_id', 'key', 'name', 'is_active', 'expires_at', 'last_used_at', 'permissions'];
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED],
            'user_id' => [self::RULE_REQUIRED],
            'key' => [self::RULE_REQUIRED]
        ];
    }

    public function generateKey(): void
    {
        $this->key = bin2hex(random_bytes(32)); // 64 character hex string
        error_log('Generated API key: ' . $this->key);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            error_log('API key is not active');
            return false;
        }

        if ($this->expires_at && strtotime($this->expires_at) < time()) {
            error_log('API key has expired');
            return false;
        }

        return true;
    }

    public function save()
    {
        try {
            error_log('Saving API key...');
            error_log('Data to save: ' . json_encode([
                'user_id' => $this->user_id,
                'key' => $this->key,
                'name' => $this->name,
                'is_active' => $this->is_active,
                'expires_at' => $this->expires_at
            ]));

            $result = parent::save();
            
            if ($result) {
                error_log('API key saved successfully');
            } else {
                error_log('Failed to save API key');
                error_log('Errors: ' . json_encode($this->errors));
            }

            return $result;
        } catch (\Exception $e) {
            error_log('Exception saving API key: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateLastUsed(): void
    {
        $this->last_used_at = date('Y-m-d H:i:s');
        $this->save();
    }
}
