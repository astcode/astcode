<?php

namespace App\Core\Form;

class SubmitButton extends Button
{
    /**
     * SubmitButton constructor.
     *
     * @param string $text The button text.
     * @param array $options Additional options (class, attributes).
     */
    public function __construct(string $text, array $options = [])
    {
        parent::__construct($text, 'submit', $options);
    }

    public function renderInput(): string
    {
        return sprintf('<button type="%s" class="%s" %s>%s</button>',
            $this->type, $this->buttonClass, $this->additionalAttributes, $this->text);
    }
}
