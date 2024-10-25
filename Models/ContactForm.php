<?php

namespace App\Models;

use App\Core\Model;

class ContactForm extends Model
{
    public string $name = '';
    public string $subject = '';
    public string $email = '';
    public string $body = '';

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED],
            'subject' => [self::RULE_REQUIRED],
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'body' => [self::RULE_REQUIRED],
        ];
    }

    public function labels(): array
    {
        return [
            'name' => 'Your name',
            'subject' => 'Subject',
            'email' => 'Your email',
            'body' => 'Body',
        ];
    }

    public function validate(): bool
    {
        $result = parent::validate();
        echo "Validation result: " . ($result ? "true" : "false") . "\n";
        echo "Errors: " . print_r($this->errors, true) . "\n";
        return $result;
    }

    public function send(): bool
    {
        // Your sending logic here
        $result = true; // or false, depending on your logic
        echo "Send result: " . ($result ? "true" : "false"); // Debug line
        return $result;
    }
}
