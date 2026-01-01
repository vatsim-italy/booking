@extends('layouts.app')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/main.min.css" rel="stylesheet" />
    <style>
        .fc-event {
            border-radius: 0.5rem !important;
            font-weight: 500;
            padding: 2px 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            color: white !important;
        }
        .fc-event:hover {
            opacity: 0.85;
            cursor: pointer;
        }
        .fc-toolbar-title {
            font-weight: 600;
            font-size: 1.5rem;
        }
        .fc-timegrid-slot {
            border-bottom: 1px dashed #dee2e6;
        }
    </style>
@endsection

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Availability</h2>

    <div class="mb-4">
        Click or drag on the calendar below to mark the times you are <strong>available</strong>.
        By default, all times are considered <strong>unavailable</strong>.
        As soon as you update this webpage, the results are instantaneously saved.
    </div>

    <div id="calendar" class="border rounded shadow-sm" style="min-height: 600px"></div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    // Load existing slots from backend
    const existingSlots = @json($userAvailability);

    const formattedEventsJSON = @json($events);
    const formattedEvents = formattedEventsJSON.map(event => ({
        id: null, 
        title: event.name,
        start: event.startEvent,
        end: event.endEvent,
        backgroundColor: '#0aca54ff',
        borderColor: '#0a8c3aff',
        extendedProps: {
            isEvent: true
        }
    }));

    console.log(formattedEventsJSON);


    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        slotMinTime: "08:00:00",
        slotMaxTime: "23:00:00",
        selectable: true,
        editable: true,
        selectMirror: true,
        height: "auto",
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek,dayGridMonth'
        },




events: [
    ...formattedEvents,
    ...existingSlots.map(slot => ({
        id: slot.id,
        title: "Available",
        start: slot.start,
        end: slot.end,
        backgroundColor: "#0d6efd",
        borderColor: "#0a58ca",
        textColor: "#fff",
        extendedProps: {
            id: slot.id,
            editable: true,
            note: slot.note
        }
    }))
],

        select: async function(info) {
            const overlapping = calendar.getEvents()
                .filter(ev => ev.extendedProps.editable)
                .some(ev => info.start < ev.end && info.end > ev.start);

            if(overlapping){
                return Swal.fire("Overlap", "This slot overlaps with an existing one!", "warning");
            }

            const { value: formValues } = await Swal.fire({
                title: "Set Availability",
                html: `<textarea id="swal-note" class="swal2-textarea" placeholder="Add a note..."></textarea>`,
                showCancelButton: true,
                confirmButtonText: "Add",
                preConfirm: () => ({ note: document.getElementById('swal-note').value.trim() })
            });

            if(!formValues) {
                calendar.unselect();
                return;
            }

            const slotData = {
                start: info.start.toISOString(),
                end: info.end.toISOString(),
                note: formValues.note
            };

            fetch("{{ route('atc.save') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ availability: [slotData] })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    calendar.addEvent({
                        title: "Available",
                        start: info.start,
                        end: info.end,
                        backgroundColor: "#0d6efd",
                        borderColor: "#0a58ca",
                        textColor: "#fff",
                        extendedProps: {
                            editable: true,
                            note: formValues.note,
                            id: data.id
                        }
                    });
                }
            });
            
            calendar.unselect();
        },

        eventChange: function(info) {
            const slotId = info.event.extendedProps.id;
            
            if(!slotId) return; // if it wasn't saved yet, skip
const overlapping = calendar.getEvents()
    .filter(ev => ev.extendedProps.editable && ev.extendedProps.id !== info.event.extendedProps.id)
    .some(ev => info.event.start < ev.end && info.event.end > ev.start);
    
            if(overlapping){
                return Swal.fire("Overlap", "This slot overlaps with an existing one!", "warning");
            }

            const slotData = {
                id: slotId,
                start: info.event.start.toISOString(),
                end: info.event.end.toISOString(),
                note: info.event.extendedProps.note
            };

            fetch("{{ route('atc.save') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ availability: [slotData] })
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) {
                    Swal.fire("Error", "Could not update slot", "error");
                    info.revert(); // undo change if failed
                }
            })
            .catch(() => info.revert());
        },

        eventClick: async function(info) {
            if(!info.event.extendedProps.editable) return;

            const note = info.event.extendedProps.note || "";
            const result = await Swal.fire({
                title: `Delete this block?`,
                html: `<b>${info.event.title}</b><br><small>${note}</small>`,
                showCancelButton: true,
                confirmButtonText: "Delete",
                icon: "warning"
            });

            if(result.isConfirmed){
                const slotId = info.event.extendedProps.id;
                console.log(info.event.extendedProps);
                if(slotId){
                    fetch("{{ route('atc.delete') }}", {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ id: slotId })
                    }).then(res => res.json())
                    .then(data => {
                        if(data.success) info.event.remove();
                    });
                } else {
                    info.event.remove(); // just remove local if it wasn't saved yet
                }
            }
        }

    });

    calendar.render();
});
</script>
@endpush
