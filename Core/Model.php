<?php

namespace App\Core;

abstract class Model
{
    public array $errors = [];

    // Define validation rule constants
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    /**
     * Check if a value is unique in the database.
     *
     * @param string $attribute The attribute to check
     * @param string $value The value to check
     * @param string $class The model class
     * @return bool
     */
    protected function isUnique(string $attribute, $value, string $class): bool
    {
        $tableName = $class::tableName();
        $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $attribute = :attr");
        $statement->bindValue(":attr", $value);
        $statement->execute();

        return $statement->fetchObject() === false;
    }
    // Add more constants as needed

    /**
     * Define the validation rules.
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Validate the model based on the defined rules.
     *
     * @return bool
     */
    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (is_array($rule)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$this->isValuePresent($value)) {
                    $this->addError($attribute, 'This field is required');
                }
                if ($ruleName === self::RULE_EMAIL && !$this->isValidEmail($value)) {
                    $this->addError($attribute, 'This field must be a valid email address');
                }
                if ($ruleName === self::RULE_MIN && is_array($rule) && isset($rule['min'])) {
                    $min = $rule['min'];
                    if (!$this->hasMinLength($value, $min)) {
                        $this->addError($attribute, "Minimum length of this field must be {$min}");
                    }
                }
                if ($ruleName === self::RULE_MAX && is_array($rule) && isset($rule['max'])) {
                    $max = $rule['max'];
                    if (!$this->hasMaxLength($value, $max)) {
                        $this->addError($attribute, "Maximum length of this field must be {$max}");
                    }
                }
                if ($ruleName === self::RULE_MATCH && is_array($rule) && isset($rule['match'])) {
                    $match = $rule['match'];
                    if ($value !== $this->{$match}) {
                        $this->addError($attribute, "This field must be the same as {$match}");
                    }
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addError($attribute, 'Record with this ' . $attribute . ' already exists');
                    }
                }
            }
        }
        return empty($this->errors);
    }

    /**
     * Load data into the model.
     *
     * @param array $data
     */
    public function loadData(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                // Assign the value directly; ensure that it matches the expected type
                if (is_array($this->{$key})) {
                    // If the model expects an array, ensure that the input is an array
                    $this->{$key} = is_array($value) ? $value : [];
                } else {
                    // For non-array properties, assign the value as a string
                    $this->{$key} = is_array($value) ? '' : $value;
                }
            }
        }
    }

    /**
     * Add an error message for a specific attribute.
     *
     * @param string $attribute
     * @param string $message
     */
    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * Get the first error message for a specific attribute.
     *
     * @param string $attribute
     * @return string|null
     */
    public function getFirstError(string $attribute): ?string
    {
        return $this->errors[$attribute][0] ?? null;
    }

    /**
     * Check if an attribute has an error.
     *
     * @param string $attribute
     * @return bool
     */
    public function hasError(string $attribute): bool
    {
        return isset($this->errors[$attribute]);
    }

    /**
     * Define custom labels for form fields.
     *
     * @param string $attribute
     * @return string
     */
    public function getLabel(string $attribute): string
    {
        return ucfirst($attribute);
    }

    /**
     * Helper method to check if a value is present.
     *
     * @param mixed $value
     * @return bool
     */
    protected function isValuePresent($value): bool
    {
        if (is_array($value)) {
            return !empty($value);
        }

        return trim($value) !== '';
    }

    /**
     * Helper method to validate email.
     *
     * @param mixed $value
     * @return bool
     */
    protected function isValidEmail($value): bool
    {
        if (is_array($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Helper method to check minimum length.
     *
     * @param mixed $value
     * @param int $min
     * @return bool
     */
    protected function hasMinLength($value, int $min): bool
    {
        if (is_array($value)) {
            return count($value) >= $min;
        }

        return mb_strlen($value) >= $min;
    }

    /**
     * Helper method to check maximum length.
     *
     * @param mixed $value
     * @param int $max
     * @return bool
     */
    protected function hasMaxLength($value, int $max): bool
    {
        if (is_array($value)) {
            return count($value) <= $max;
        }

        return mb_strlen($value) <= $max;
    }

    /**
     * Helper method to check if two values match.
     *
     * @param mixed $value
     * @param string $matchAttribute
     * @return bool
     */
    protected function isMatch($value, string $matchAttribute): bool
    {
        return $value === $this->{$matchAttribute};
    }
}
