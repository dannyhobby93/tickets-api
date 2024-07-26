<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Resources\V1\UserResource;
use App\Models\User;

class AuthorsController extends ApiController
{
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(
            User::has('tickets')
                ->filter($filters)
                ->paginate()
        );
    }

    public function show(User $author)
    {
        if ($this->include('tickets')) {
            return new UserResource($author->load('tickets'));
        }
        return new UserResource($author);
    }
}
