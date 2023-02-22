<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $email_domain = explode('@', $value)[1] ?? null;

        if (!$email_domain) {
          return false;
        }
      
        if ($email_domain != 'dulub.com.br') {
          return false;
        }
      
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O email não é válido.';
    }
}
