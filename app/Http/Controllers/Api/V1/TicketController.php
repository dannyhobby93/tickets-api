<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Models\User;
use App\Models\Ticket;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\V1\TicketResource;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            User::findOrFail($request->input('data.relationships.author.data.id'));
        } catch (ModelNotFoundException $ex) {
            return $this->ok('User not found', [
                'error' => 'Provided user id does not exist'
            ]);
        }

        return new TicketResource($request->mappedAttributes());
    }

    /**
     * Display the specified resource.
     */
    public function show($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($this->include('author')) {
                return new TicketResource($ticket->load('user'));
            }

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $ex) {
            return $this->error('Ticket cannot be found.', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {
        // PATCH
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $ex) {
            return $this->error('Ticket cannot be found.', 404);
        }
    }

    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $ex) {
            return $this->error('Ticket cannot be found.', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();

            return $this->ok('Ticket successfully deleted.');
        } catch (ModelNotFoundException $ex) {
            return $this->error('Ticket cannot be found.', 404);
        }
    }
}
