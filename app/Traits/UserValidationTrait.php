<?php

namespace App\Traits;


trait UserValidationTrait
{
    public function nameRule($isRequired)
    {
        $required = $isRequired ? ['required'] : ['nullable'];


        return array_merge($required, ['string', 'min:3', 'max:15', 'regex:/^[\p{Arabic}\p{Latin}][\p{Arabic}\p{Latin}\d_]{2,14}$/u']);
    }

    public function emailRule($isRequired, $isUnique, $isRegex)
    {
        $required = $isRequired ? ['required'] : ['nullable'];

        $rule = ['string', 'email', 'min:11', 'max:64'];
        $rule[] = $isUnique ? 'unique:users,email,' . auth()->id() : 'exists:users,email';
        if ($isRegex) {
            $rule[] = 'regex:/^[a-zA-Z0-9._%+-]{3,}@gmail\.com$/';
        }

        return array_merge($required, $rule);
    }

    public function passwordRule($isRequired, $isRegex, $isConfirmed)
    {
        $required = $isRequired ? ['required'] : ['nullable'];

        $rule = ['string', 'min:8', 'max:20'];

        if ($isRegex) {
            $rule[] = 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\*\@\.\-\+])[a-zA-Z0-9\*\@\.\-\+\#\&]{8,20}$/';
        }
        if ($isConfirmed) {
            $rule[] = 'confirmed';
        }

        return array_merge($required, $rule);
    }
}
