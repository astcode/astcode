<?php

namespace App\Models;

use App\Core\DB\DbModel;

class ApiRequest extends DbModel
{
    public ?int $id = null;
    public string $api_key = '';
    public string $endpoint = '';
    public string $method = '';
    public int $response_code = 200;
    public ?string $created_at = null;

    public function tableName(): string
    {
        return 'api_requests';
    }

    public function attributes(): array
    {
        return ['api_key', 'endpoint', 'method', 'response_code'];
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'api_key' => [self::RULE_REQUIRED],
            'endpoint' => [self::RULE_REQUIRED],
            'method' => [self::RULE_REQUIRED],
            'response_code' => [self::RULE_REQUIRED],
        ];
    }
}
