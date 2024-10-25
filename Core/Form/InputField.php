<?php

namespace App\Core\Form;

use App\Core\Model;

class InputField extends BaseField
{
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';
    public const TYPE_EMAIL = 'email';

    public string $type;
    public array $options = [];
    public string $placeholder = '';

    /**
     * InputField constructor.
     *
     * @param \App\Core\Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    /**
     * Set the input type to password.
     *
     * @return self
     */
    public function passwordField(): self
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    /**
     * Add an option to the input.
     *
     * @param string $option
     * @return self
     */
    public function addOption(string $option): self
    {
        $this->options[] = $option;
        return $this;
    }

    /**
     * Set the placeholder for the input.
     *
     * @param string $placeholder
     * @return self
     */
    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Render the input element.
     *
     * @return string
     */
    public function renderInput(): string
    {
        return sprintf('<input type="%s" name="%s" value="%s" class="form-control%s" placeholder="%s" %s>',
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            !empty($this->options) ? ' ' . implode(' ', $this->options) : '',
            $this->placeholder,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
        );
    }

    public function fieldType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
