<?php

namespace App\Core\Form;

use App\Core\Model;

class RadioField extends BaseField
{
    public array $optionsList = []; // options as value => label
    public array $radioAttributes = [];

    /**
     * RadioField constructor.
     *
     * @param \App\Core\Model $model
     * @param string $attribute
     * @param array $options Additional options (optionsList, attributes, class, etc.)
     */
    public function __construct(Model $model, string $attribute, array $options = [])
    {
        parent::__construct($model, $attribute, $options);
        $this->optionsList = $options['optionsList'] ?? [];
        $this->radioAttributes = $options['attributes'] ?? [];

        // Handle additional classes if provided
        if (isset($options['class'])) {
            $this->inputClass .= ' ' . $options['class'];
        }
    }

    /**
     * Set the list of radio options.
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
     * Add a radio option.
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
     * Set additional radio attributes.
     *
     * @param array $attributes
     * @return self
     */
    public function setRadioAttributes(array $attributes): self
    {
        $this->radioAttributes = $attributes;
        return $this;
    }

    /**
     * Render the radio inputs.
     *
     * @return string
     */
    public function renderInput(): string
    {
        $html = '';
        foreach ($this->optionsList as $value => $label) {
            $classes = trim($this->inputClass);
            if ($this->model->hasError($this->attribute)) {
                $classes .= ' is-invalid';
            }

            // Determine if checked
            $checked = ($this->model->{$this->attribute} == $value) ? ' checked' : '';

            // Build additional attributes string
            $attr = '';
            foreach ($this->radioAttributes as $key => $valueAttr) {
                $attr .= sprintf(' %s="%s"', htmlspecialchars($key), htmlspecialchars($valueAttr));
            }

            $html .= sprintf(
                '<div class="form-check">
                    <input class="%s" type="radio" name="%s" id="%s_%s" value="%s"%s%s>
                    <label class="form-check-label" for="%s_%s">%s</label>
                </div>',
                htmlspecialchars(trim($classes)),
                htmlspecialchars($this->attribute),
                htmlspecialchars($this->attribute),
                htmlspecialchars($value),
                htmlspecialchars($value),
                $checked,
                $attr,
                htmlspecialchars($this->attribute),
                htmlspecialchars($value),
                htmlspecialchars($label)
            );
        }

        return $html;
    }
}


    /*
        // In your view file
        echo $form->radioField($model, 'gender', [
            'optionsList' => [
                'male' => 'Male',
                'female' => 'Female',
                'other' => 'Other',
            ],
            'class' => 'form-check-input',
            'attributes' => [
                'required' => 'required',
            ],
        ]);
    */

