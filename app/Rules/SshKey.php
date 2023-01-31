<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SshKey implements Rule
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
        $key_parts = explode(' ', $value, 3);
        if (count($key_parts) < 2) {
            return false;
        }
        if (count($key_parts) > 3) {
            return false;
        }
        $algorithm = $key_parts[0];
        $key = $key_parts[1];
        if (!in_array($algorithm, array('ssh-rsa', 'ssh-dss','ssh-ed25519'))) {
            return false;
        }
        $key_base64_decoded = base64_decode($key, true);
        if ($key_base64_decoded == FALSE) {
            return false;
        }
        // $check = base64_decode(substr($key, 0, 16));
        // $check = preg_replace("/[^\w\-]/", "", $check);
        // if ((string)$check !== (string)$algorithm) {
        //     return false;
        // }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'SSH key invalid.';
    }
}
