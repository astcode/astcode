<?php

namespace App\Core\Form;

use App\Core\Model;

class TextAreaField extends BaseField
{
    public string $placeholder = '';
    protected array $additionalAttributes = [];

    /**
     * TextAreaField constructor.
     *
     * @param \App\Core\Model $model
     * @param string $attribute
     * @param array $options Additional options (attributes, class, placeholder, etc.)
     */
    public function __construct(Model $model, string $attribute, array $options = [])
    {
        parent::__construct($model, $attribute, $options);
        $this->additionalAttributes = $options['attributes'] ?? [];

        // Handle additional classes if provided
        if (isset($options['class'])) {
            $this->inputClass .= ' ' . $options['class'];
        }
    }

    /**
     * Set additional textarea attributes.
     *
     * @param array $attributes
     * @return self
     */
    public function setAttributes(array $attributes): self
    {
        $this->additionalAttributes = $attributes;
        return $this;
    }

    /**
     * Add a single attribute.
     *
     * @param string $key
     * @param string $value
     * @return self
     */
    public function addAttribute(string $key, string $value): self
    {
        $this->additionalAttributes[$key] = $value;
        return $this;
    }

    /**
     * Set placeholder for the textarea.
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
     * Render the textarea input.
     *
     * @return string
     */
    public function renderInput(): string
    {
        return sprintf('<textarea name="%s" class="form-control%s" placeholder="%s">%s</textarea>',
            $this->attribute,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->placeholder,
            $this->model->{$this->attribute}
        );
    }
}


    /*
        // In your view file
        echo $form->textAreaField($model, 'bio', [
            'class' => 'custom-textarea-class',
            'attributes' => [
                'rows' => '5',
                'placeholder' => 'Tell us about yourself...',
            ],
        ]);
    */

