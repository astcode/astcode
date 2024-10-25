<?php

namespace App\Core\Form;

use App\Core\Model;

abstract class BaseField
{
    public Model $model;
    public string $attribute;
    public array $options = [];
    public string $labelClass = 'form-label';
    public string $inputClass = 'form-control';
    public string $errorClass = 'invalid-feedback';

    abstract public function renderInput(): string;

    /**
     * BaseField constructor.
     *
     * @param \App\Core\Model $model
     * @param string $attribute
     * @param array $options Additional HTML attributes and classes
     */
    public function __construct(Model $model, string $attribute, array $options = [])
    {
        $this->model = $model;
        $this->attribute = $attribute;
        $this->options = $options;

        // Handle input classes
        if (isset($options['class'])) {
            $this->inputClass .= ' ' . $options['class'];
        }

        // Handle label classes
        if (isset($options['labelClass'])) {
            $this->labelClass = $options['labelClass'];
        }

        // Handle custom error class
        if (isset($options['errorClass'])) {
            $this->errorClass = $options['errorClass'];
        }
    }

    /**
     * Set label class.
     *
     * @param string $class
     * @return self
     */
    public function setLabelClass(string $class): self
    {
        $this->labelClass = $class;
        return $this;
    }

    /**
     * Set input class.
     *
     * @param string $class
     * @return self
     */
    public function setInputClass(string $class): self
    {
        $this->inputClass = $class;
        return $this;
    }

    /**
     * Set error class.
     *
     * @param string $class
     * @return self
     */
    public function setErrorClass(string $class): self
    {
        $this->errorClass = $class;
        return $this;
    }

    /**
     * Render the entire field (label, input, error).
     *
     * @return string
     */
    public function __toString(): string
    {
        $label = $this->renderLabel();
        $input = $this->renderInput();
        $error = $this->renderError();

        return sprintf(
            '<div class="mb-3">
                %s
                %s
                %s
            </div>',
            $label,
            $input,
            $error
        );
    }

    /**
     * Render the label.
     *
     * @return string
     */
    protected function renderLabel(): string
    {
        $labelText = $this->model->getLabel($this->attribute) ?? ucfirst($this->attribute);
        $for = htmlspecialchars($this->attribute);
        $classes = $this->labelClass;

        // Allow overriding label text
        if (isset($this->options['label'])) {
            $labelText = htmlspecialchars($this->options['label']);
        }

        // Allow custom HTML in label
        if (isset($this->options['labelHtml'])) {
            return sprintf('<label class="%s" for="%s">%s</label>', $classes, $for, $this->options['labelHtml']);
        }

        return sprintf('<label class="%s" for="%s">%s</label>', $classes, $for, $labelText);
    }

    /**
     * Render the error message.
     *
     * @return string
     */
    protected function renderError(): string
    {
        $error = $this->model->getFirstError($this->attribute);
        if ($error) {
            return sprintf('<div class="%s">%s</div>', $this->errorClass, htmlspecialchars($error));
        }
        return '';
    }
}
