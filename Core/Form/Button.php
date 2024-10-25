<?php

namespace App\Core\Form;

abstract class Button
{
    protected string $text;
    protected string $type;
    protected array $additionalAttributes = [];
    protected string $buttonClass = 'btn';

    /**
     * Button constructor.
     *
     * @param string $text The button text.
     * @param string $type The button type (submit, reset, button).
     * @param array $options Additional attributes and classes.
     */
    public function __construct(string $text, string $type = 'button', array $options = [])
    {
        $this->text = $text;
        $this->type = $type;
        $this->additionalAttributes = $options['attributes'] ?? [];

        // Handle additional classes if provided
        if (isset($options['class'])) {
            $this->buttonClass .= ' ' . $options['class'];
        }
    }

    /**
     * Add multiple classes to the button.
     *
     * @param array $classes
     * @return self
     */
    public function addClasses(array $classes): self
    {
        foreach ($classes as $class) {
            $this->buttonClass .= ' ' . $class;
        }
        return $this;
    }

    /**
     * Add additional attributes to the button.
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
     * Render the button.
     *
     * @return string
     */
    public function render(): string
    {
        // Build additional attributes string
        $attr = '';
        foreach ($this->additionalAttributes as $key => $value) {
            $attr .= sprintf(' %s="%s"', htmlspecialchars($key), htmlspecialchars($value));
        }

        return sprintf(
            '<button type="%s" class="%s"%s>%s</button>',
            htmlspecialchars($this->type),
            htmlspecialchars(trim($this->buttonClass)),
            $attr,
            htmlspecialchars($this->text)
        );
    }

    /**
     * Magic method to convert the button to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
