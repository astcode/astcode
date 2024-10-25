<?php

namespace App\Core\Form;

use App\Core\Model;

class SelectField extends BaseField
{
    public array $optionsList = []; // options as value => label
    public array $selectAttributes = [];

    /**
     * SelectField constructor.
     *
     * @param \App\Core\Model $model
     * @param string $attribute
     * @param array $options Additional options (optionsList, attributes, class, etc.)
     */
    public function __construct(Model $model, string $attribute, array $options = [])
    {
        parent::__construct($model, $attribute, $options);
        $this->optionsList = $options['optionsList'] ?? [];
        $this->selectAttributes = $options['attributes'] ?? [];

        // Handle additional classes if provided
        if (isset($options['class'])) {
            $this->inputClass .= ' ' . $options['class'];
        }
    }

    /**
     * Set the list of options.
     *
     * @param array $optionsList
     * @return self
     */
    public function setOptionsList(array $optionsList): self
    {
        $this->optionsList = $optionsList;
        return $this;
    }

    /**
     * Add an option to the options list.
     *
     * @param string $value
     * @param string $label
     * @return self
     */
    public function addOption(string $value, string $label): self
    {
        $this->optionsList[$value] = $label;
        return $this;
    }

    /**
     * Set additional select attributes.
     *
     * @param array $attributes
     * @return self
     */
    public function setSelectAttributes(array $attributes): self
    {
        $this->selectAttributes = $attributes;
        return $this;
    }

    /**
     * Render the select input.
     *
     * @return string
     */
    public function renderInput(): string
    {
        $classes = trim($this->inputClass);
        if ($this->model->hasError($this->attribute)) {
            $classes .= ' is-invalid';
        }

        // Build additional attributes string
        $attr = '';
        foreach ($this->selectAttributes as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $attr .= sprintf(' %s="%s"', htmlspecialchars($key, ENT_QUOTES, 'UTF-8'), htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            }
        }

        // Start select tag
        $select = sprintf('<select name="%s" id="%s" class="%s"%s>', 
            htmlspecialchars((string)$this->attribute, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars((string)$this->attribute, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars(trim($classes), ENT_QUOTES, 'UTF-8'),
            $attr
        );

        // Safeguard: Ensure the selected value is a string
        $selectedValue = $this->model->{$this->attribute};
        if (is_array($selectedValue)) {
            // If it's an array, take the first value or handle accordingly
            $selectedValue = reset($selectedValue);
            // Optionally, you can log a warning or handle multi-selects differently
        }
        $selectedValue = (string)$selectedValue;

        // Add options
        foreach ($this->optionsList as $value => $label) {
            $value = (string)$value;
            $label = (string)$label;
            $selected = ($selectedValue === $value) ? ' selected' : '';
            $select .= sprintf('<option value="%s"%s>%s</option>',
                htmlspecialchars($value, ENT_QUOTES, 'UTF-8'),
                $selected,
                htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            );
        }

        // Close select tag
        $select .= '</select>';

        return $select;
    }
}
