<?php

namespace App\Core\Form;

use App\Core\Model;

class CheckboxField extends BaseField
{
    public string $label;
    public string $value;
    public bool $isArray;

    /**
     * CheckboxField constructor.
     *
     * @param \App\Core\Model $model
     * @param string $attribute
     * @param string $label
     * @param string $value
     * @param bool $isArray
     */
    public function __construct(Model $model, string $attribute, string $label, string $value, bool $isArray = false)
    {
        parent::__construct($model, $attribute);
        $this->label = $label;
        $this->value = $value;
        $this->isArray = $isArray;
    }

    /**
     * Render the checkbox input.
     *
     * @return string
     */
    public function renderInput(): string
    {
        $attribute = $this->attribute;
        $name = $this->isArray ? "{$attribute}[]" : $attribute;
        $checked = '';

        $isChecked = false;
        if ($this->isArray) {
            $isChecked = in_array($this->value, $this->model->{$attribute} ?? []);
        } else {
            $isChecked = $this->model->{$attribute} === $this->value;
        }

        if ($isChecked) {
            $checked = 'checked';
        }

        // Generate a unique ID for each checkbox
        $uniqueId = htmlspecialchars("{$attribute}_{$this->value}", ENT_QUOTES, 'UTF-8');

        return sprintf(
            '<input type="checkbox" name="%s" id="%s" value="%s" class="form-check-input" %s>',
            htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            $uniqueId,
            htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8'),
            $checked
        );
    }

    /**
     * Render the label for the checkbox.
     *
     * @return string
     */
    public function renderLabel(): string
    {
        // Generate the same unique ID as the checkbox
        $uniqueId = htmlspecialchars("{$this->attribute}_{$this->value}", ENT_QUOTES, 'UTF-8');

        return sprintf(
            '<label class="form-check-label" for="%s">%s</label>',
            $uniqueId,
            htmlspecialchars($this->label, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Render the entire checkbox field with input and label.
     *
     * @return string
     */
    public function __toString(): string
    {
        $input = $this->renderInput();
        $label = $this->renderLabel();
        $error = $this->renderError();

        return sprintf(
            '<div class="form-check">
                %s
                %s
                %s
            </div>',
            $input,
            $label,
            $error
        );
    }
}
