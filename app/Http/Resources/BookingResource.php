<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $flight = $this->flights()->first();
        return [
            'uuid' => $this->uuid,
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
            'user' => $this->user->id ?? null,
            'full_name' => $this->user->full_name ?? null,
            'status' => $this->status,
            'callsign' => $this->callsign,
            'acType' => $this->acType,
            'selcal' => $this->selcal,
            'dep' => $flight->airportDep->icao,
            'arr' => $flight->airportArr->icao,
            'ctot' => $flight->ctot,
            'eta' => $flight->eta,
            'route' => $flight->route,
            'oceanicFL' => $flight->oceanicFL,
            'oceanicTrack' => $flight->oceanicTrack,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'links' => [
                'user' => url('/api/users/' . $this->user->id),
                'dep' => url('/api/airports/' . $flight->airportDep->icao),
                'arr' => url('/api/airports/' . $flight->airportArr->icao),
            ],
        ];
    }
}
