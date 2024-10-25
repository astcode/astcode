<?php

namespace App\Core\Form;

use App\Core\Form\BaseField;
use App\Core\Model;

class Label extends BaseField
{
    protected string $for;
    protected string $text;
    protected array $additionalAttributes = [];

    /**
     * Label constructor.
     *
     * @param Model $model
     * @param string $attribute
     * @param array $options
     */
    public function __construct(Model $model, string $attribute, array $options = [])
    {
        parent::__construct($model, $attribute);
        $this->for = $options['for'] ?? $attribute;
        $this->text = $model->getLabel($attribute);
        unset($options['for']);
        $this->setAttributes($options);
    }

    /**
     * Add a CSS class to the label.
     *
     * @param string $class
     * @return self
     */
    public function addClass(string $class): self
    {
        $this->options[] = $class;
        return $this;
    }

    /**
     * Add an HTML attribute to the label.
     *
     * @param string $attribute
     * @param string $value
     * @return self
     */
    public function setAttribute(string $attribute, string $value): self
    {
        $this->additionalAttributes[$attribute] = $value;
        return $this;
    }

    /**
     * Render the label HTML.
     *
     * @return string
     */
    public function render(): string
    {
        $attributes = array_merge([
            'for' => $this->for,
            'class' => 'form-label',
        ], $this->options, $this->additionalAttributes);

        // Merge classes
        if (isset($attributes['class']) && !empty($this->options)) {
            $attributes['class'] .= ' ' . implode(' ', $this->options);
        }

        // Remove empty attributes
        foreach ($attributes as $key => $value) {
            if ($value === '') {
                unset($attributes[$key]);
            }
        }

        // Render attributes
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= sprintf('%s="%s" ', $key, htmlspecialchars($value, ENT_QUOTES));
        }
        $attrString = trim($attrString);

        return sprintf('<label %s>%s</label>', $attrString, htmlspecialchars($this->text, ENT_QUOTES));
    }

    /**
     * Prevent __toString usage.
     *
     * @return void
     */
    public function __toString(): string
    {
        return $this->render();
    }

    protected function setAttributes(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Implement the renderInput method required by BaseField.
     *
     * @return string
     */
    public function renderInput(): string
    {
        return $this->render(); // Reuse the existing render method
    }
}
