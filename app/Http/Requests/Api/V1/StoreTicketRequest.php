<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $is_tickets_controller = $this->routeIs('tickets.store');
        $author_id_attr = $is_tickets_controller ? 'data.relationships.author.data.id' : 'author';
        $author_rule = 'required|integer|exists:users,id';

        $user = Auth::user();

        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
        ];

        if ($is_tickets_controller) {
            $rules['data.relationships'] = 'required|array';
            $rules['data.relationships.author'] = 'required|array';
            $rules['data.relationships.author.data'] = 'required|array';
        }

        $rules[$author_id_attr] = $author_rule . '|size:' . $user->id;

        if ($user->tokenCan(Abilities::CreateTicket)) {
            $rules[$author_id_attr] = $author_rule;
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')
            ]);
        }
    }

    public function bodyParameters()
    {
        $documentation = [
            'data.attributes.title' => [
                'description' => "The ticket's title (method)",
                'example' => 'No-example'
            ],
            'data.attributes.description' => [
                'description' => "The ticket's description",
                'example' => 'No-example',
            ],
            'data.attributes.status' => [
                'description' => "The ticket's status",
                'example' => 'No-example',
            ],
        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => 'No-example'
            ];
        } else {
            $documentation['author'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => 'No-example'
            ];
        }

        return $documentation;

    }
}
