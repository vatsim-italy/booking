<?php

namespace App\Http\Controllers\ATC;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Availability;

class ATCController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = auth()->user();
        $this->authorize('report', $user);

        // Load availability from DB
        $userAvailability = auth()->user()->availability->map(fn($slot) => [
            'id' => $slot->id,
            'start' => $slot->start,
            'end' => $slot->end,
            'note' => $slot->note
        ])->toArray();

        // Example: get events (your existing helper)
        $events = nextEvents(false, false, true);

        


        return view('atc.availability', compact('userAvailability', 'events'));
    }

     public function save(Request $request)
    {
        $user = auth()->user();

        $data = $request->json()->all(); // get JSON payload

        $id = null;

        foreach ($data['availability'] as $slot) {
            if(isset($slot['id'])){
                // Update existing slot
                $existing = Availability::where('id', $slot['id'])
                    ->where('user_id', $user->id)
                    ->first();

                if($existing){
                    $id = $existing->id;
                    $existing->update([
                        'start' => $slot['start'],
                        'end'   => $slot['end'],
                        'note'  => $slot['note'] ?? null,
                    ]);
                }
            } else {
                // Create new slot
                $newSlot = Availability::create([
                    'user_id' => $user->id,
                    'start'   => $slot['start'],
                    'end'     => $slot['end'],
                    'note'    => $slot['note'] ?? null,
                ]);
                $id = $newSlot->id;
            }
        }

        return response()->json(['success' => true, 'id' => $id]);
    }

    public function delete(Request $request)
    {
        $user = auth()->user();
        $data = $request->json()->all(); // get JSON payload
        $slotId = $data['id'] ?? null;

        if ($slotId) {
            $slot = Availability::where('id', $slotId)
                ->where('user_id', $user->id)
                ->first();

            if ($slot) {
                $slot->delete();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 400);
    }
}
