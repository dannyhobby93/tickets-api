<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseUserRequest extends FormRequest
{
    public function mappedAttributes(array $other_attributes = [])
    {
        $attributes_map = array_merge([
            'data.attributes.name' => 'name',
            'data.attributes.email' => 'email',
            'data.attributes.isManager' => 'is_manager',
            'data.attributes.password' => 'password',
        ], $other_attributes);

        $attributes_to_update = [];

        foreach ($attributes_map as $key => $attribute) {
            if ($this->has($key)) {
                $value = $this->input($key);

                // Not needed in laravel 11
                // if ($attribute === 'password') {
                //     $value = bcrypt($value);
                // }

                $attributes_to_update[$attribute] = $value;
            }
        }

        return $attributes_to_update;
    }
}
