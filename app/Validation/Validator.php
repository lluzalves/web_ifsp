<?php

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;


class Validator
{
    protected $errors;

    public function validate($request, array $rules)
    {
        foreach ($rules as $field => $rule) {
            try {
                $rule->setName($field)->assert($request->getParam($field));
            } catch (NestedValidationException$exception) {
                $this->errors[$field] = $exception->getMessages();
            }
        }

        $_SESSION['errors'] = $this->errors;

        return $this;

    }

    public function failed()
    {
        return !empty($this->errors);
    }

}