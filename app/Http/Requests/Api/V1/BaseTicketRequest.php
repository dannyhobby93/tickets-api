<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{
    public function mappedAttributes()
    {
        $attributes_map = [
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id'
        ];

        $attributes_to_update = [];

        foreach ($attributes_map as $key => $attribute) {
            if ($this->has($key)) {
                $attributes_to_update[$attribute] = $this->input($key);
            }
        }

        return $attributes_to_update;
    }
    public function messages()
    {
        return [
            'data.attributes.status' => 'data.attributes.status is invalid, please use: A, C, H or X.'
        ];
    }
}
