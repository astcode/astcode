<?php

namespace App\Core\Form;

use App\Core\Model;
use App\Core\Application;
use App\Core\Middlewares\CsrfMiddleware;

class Form
{
    /**
     * Begin the form.
     *
     * @param string $action The form action URL.
     * @param string $method The HTTP method (GET, POST).
     * @return Form
     */
    public static function begin(string $action = '', string $method = 'post'): Form
    {
        echo sprintf('<form action="%s" method="%s">', htmlspecialchars($action, ENT_QUOTES, 'UTF-8'), htmlspecialchars($method, ENT_QUOTES, 'UTF-8'));
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = CsrfMiddleware::generateCsrfToken();
        }
        
        // Add CSRF token to form
        echo sprintf('<input type="hidden" name="csrf_token" value="%s">', htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'));
        
        return new Form();
    }

    /**
     * End the form.
     */
    public static function end(): void
    {
        echo '</form>';
    }

    /**
     * Create an input field.
     *
     * @param \App\Core\Model $model The model instance.
     * @param string $attribute The attribute name.
     * @param string $type The input type (e.g., text, email).
     * @param array $options Additional attributes and classes.
     * @return InputField
     */
    public function field(Model $model, string $attribute, string $type = 'text'): InputField
    {
        $field = new InputField($model, $attribute);
        $field->type = $type;
        return $field;
    }

    /**
     * Create a textarea field.
     *
     * @param \App\Core\Model $model The model instance.
     * @param string $attribute The attribute name.
     * @return TextAreaField
     */
    public function textAreaField(Model $model, string $attribute): TextAreaField
    {
        return new TextAreaField($model, $attribute);
    }

    /**
     * Create a select field.
     *
     * @param \App\Core\Model $model The model instance.
     * @param string $attribute The attribute name.
     * @param array $optionsList The list of options (value => label).
     * @param array $options Additional attributes and classes.
     * @return SelectField
     */
    public function selectField(Model $model, string $attribute, array $optionsList, array $options = []): SelectField
    {
        return new SelectField($model, $attribute, ['optionsList' => $optionsList] + $options);
    }

    /**
     * Create a checkbox field.
     *
     * @param \App\Core\Model $model The model instance.
     * @param string $attribute The attribute name.
     * @param string $label The label for the checkbox.
     * @param string $value The value attribute for the checkbox.
     * @param bool $isArray Indicates if multiple selections are allowed.
     * @return CheckboxField
     */
    public function checkboxField(Model $model, string $attribute, string $label = '', string $value = '1', bool $isArray = false): CheckboxField
    {
        return new CheckboxField($model, $attribute, $label, $value, $isArray);
    }

    /**
     * Create a radio field.
     *
     * @param \App\Core\Model $model The model instance.
     * @param string $attribute The attribute name.
     * @param array $optionsList The list of options (value => label).
     * @param array $options Additional attributes and classes.
     * @return RadioField
     */
    public function radioField(Model $model, string $attribute, array $optionsList, array $options = []): RadioField
    {
        return new RadioField($model, $attribute, ['optionsList' => $optionsList] + $options);
    }

    /**
     * Create a submit button.
     *
     * @param string $text The button text.
     * @param array $options Additional attributes and classes.
     * @return SubmitButton
     */
    public function submitButton(string $text, array $options = []): SubmitButton
    {
        return new SubmitButton($text, $options);
    }
}
