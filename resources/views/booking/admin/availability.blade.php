@extends('layouts.app')

@section('content')
<style>
.availability-table-container {
    overflow-x: auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fafafa;
    padding: 10px;
}
.availability-table {
    border-collapse: collapse;
    min-width: 1000px;
    width: 100%;
}
.availability-table th,
.availability-table td {
    border: 1px solid #ddd;
    padding: 5px;
    text-align: center;
    min-width: 50px;
}
.availability-table th {
    position: sticky;
    top: 0;
    background: #f0f0f0;
    z-index: 2;
}
.availability-table .user-label {
    position: sticky;
    left: 0;
    background: #f0f0f0;
    font-weight: bold;
    text-align: right;
    padding-right: 10px;
    z-index: 1;
}
.availability-cell {
    cursor: pointer;
    transition: opacity 0.2s;
    border-radius: 4px;
}
.availability-cell.unavailable { background-color: #e0e0e0; }
.availability-cell.available { background-color: #4CAF50; color: #fff; }
.availability-cell.partial { background-color: #FF9800; color: #fff; }
.availability-cell:hover { opacity: 0.8; }

.legend {
    margin-top: 20px;
    display: flex;
    gap: 20px;
}
.legend div {
    display: flex;
    align-items: center;
    gap: 5px;
}
.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
}
</style>

<div class="container mt-4">
    <h2>Availability Matrix: {{ $event->name }}</h2>
    <p class="text-muted">
        Event: {{ \Carbon\Carbon::parse($event->startEvent)->format('M d, Y H:i') }} - 
        {{ \Carbon\Carbon::parse($event->endEvent)->format('H:i') }}
    </p>

    <div class="availability-table-container">
        <table class="availability-table">
            <thead>
                <tr>
                    <th class="user-label">User</th>
                    @php
                        $eventStart = \Carbon\Carbon::parse($event->startEvent);
                        $eventEnd = \Carbon\Carbon::parse($event->endEvent);
                        $timeSlots = [];
                        $slot = $eventStart->copy();
                        while ($slot < $eventEnd) {
                            $timeSlots[] = $slot->copy(); // store full Carbon datetime
                            $slot->addMinutes(30);
                        }
                    @endphp
                    @foreach ($timeSlots as $slot)
                        <th>{{ $slot->format('H:i') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $users = $availabilities->groupBy(fn($a) => $a->user?->id ?? 0);
                @endphp
                @foreach ($users as $userId => $userAvailabilities)
                    @php
                        $user = $userAvailabilities[0]->user;
                        $userName = $user ? $user->name_first . ' ' . $user->name_last : '—';

                        // Build availability per slot
                        $slotAvailability = array_fill(0, count($timeSlots), 0); // 0 = unavailable
                        foreach ($userAvailabilities as $availability) {
                            $start = \Carbon\Carbon::parse($availability->start);
                            $end = \Carbon\Carbon::parse($availability->end);

                            foreach ($timeSlots as $i => $slotTime) {
                                $slotStart = $slotTime;
                                $slotEnd = $slotStart->copy()->addMinutes(30);

                                if ($slotStart >= $start && $slotEnd <= $end) {
                                    $slotAvailability[$i] = 1; // Available
                                } elseif ($slotStart < $end && $slotEnd > $start) {
                                    $slotAvailability[$i] = 0.5; // Partially available
                                }
                            }
                        }
                    @endphp
                    <tr>
                        <td class="user-label">{{ $userName }}</td>
                        @foreach ($slotAvailability as $value)
                            @php
                                $class = $value === 1 ? 'available' : ($value === 0.5 ? 'partial' : 'unavailable');
                            @endphp
                            <td class="availability-cell {{ $class }}" 
                                onclick="alert('User: {{ $userName }}\nStatus: {{ $value === 1 ? 'Available' : ($value === 0.5 ? 'Partially Available' : 'Unavailable') }}')">
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="legend">
        <div><div class="legend-color" style="background-color: #e0e0e0;"></div> Unavailable</div>
        <div><div class="legend-color" style="background-color: #4CAF50;"></div> Available</div>
        <div><div class="legend-color" style="background-color: #FF9800;"></div> Partially Available</div>
    </div>
</div>
@endsection
