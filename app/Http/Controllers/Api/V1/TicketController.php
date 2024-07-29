<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\V1\TicketResource;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    protected string $policy_class = TicketPolicy::class;

    /**
     * Get all Tickets
     * 
     * @group Managing Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *fix*
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Create a Ticket
     * 
     * Creates a new ticket record. Users can only create tickets for themselves. Managers can create tickets for any user.
     * 
     * @group Managing Tickets
     * 
     * @response {"data":{"type":"ticket","id":107,"attributes":{"title":"asdfasdfasdfasdfasdfsadf","description":"test ticket","status":"A","createdAt":"2024-03-26T04:40:48.000000Z","updatedAt":"2024-03-26T04:40:48.000000Z"},"relationships":{"author":{"data":{"type":"user","id":1},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/1"}}},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/tickets\/107"}}}
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            Gate::authorize('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttributes()));
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to create this resource.', 403);
        }
    }

    /**
     * Show a specific Ticket
     * 
     * Display an individual ticket.
     * 
     * @group Managing Tickets
     * 
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
     * Update Ticket
     * 
     * Update the specified ticket in storage.
     * 
     * @group Managing Tickets
     * 
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            Gate::authorize('update', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $ex) {
            return $this->error('Ticket cannot be found.', 404);
        } catch (AuthorizationException $ex) {
            return $this->error('You are not authorized to update this resource.', 403);
        }
    }

    /**
     * Replace Ticket
     * 
     * Replace the specified ticket in storage.
     * 
     * @group Managing Tickets
     * 
     */
    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            Gate::authorize('replace', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $ex) {
            return $this->error('Ticket cannot be found.', 404);
        }
    }

    /**
     * Delete Ticket
     * 
     * Remove the specified resource from storage.
     * 
     * @group Managing Tickets
     * 
     */
    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            Gate::authorize('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket successfully deleted.');
        } catch (ModelNotFoundException $ex) {
            return $this->error('Ticket cannot be found.', 404);
        }
    }
}
