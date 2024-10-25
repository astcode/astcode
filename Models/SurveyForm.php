<?php

namespace App\Models;

use App\Core\Model;

class SurveyForm extends Model
{
    public string $name = '';
    public string $email = '';
    public string $genre = '';
    public string $streaming = '';
    public array $actors = [];
    public string $comments = '';

    /**
     * Define the validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                self::RULE_REQUIRED,
                [self::RULE_MIN, 'min' => 3],
                [self::RULE_MAX, 'max' => 50]
            ],
            'email' => [
                self::RULE_REQUIRED,
                self::RULE_EMAIL
            ],
            'genre' => [
                self::RULE_REQUIRED
            ],
            'streaming' => [
                self::RULE_REQUIRED
            ],
            'actors' => [
                // self::RULE_REQUIRED
            ],
            'comments' => [
                [self::RULE_MAX, 'max' => 500]
            ],
        ];
    }

    /**
     * Define custom labels for form fields.
     *
     * @param string $attribute
     * @return string
     */
    public function getLabel($attribute): string
    {
        $labels = [
            'name' => 'Full Name',
            'email' => 'Email Address',
            'genre' => 'Favorite Genre',
            'streaming' => 'Preferred Streaming Service',
            'actors' => 'Favorite Actors',
            'comments' => 'Additional Comments',
        ];

        return $labels[$attribute] ?? ucfirst($attribute);
    }
}
