{{-- resources/views/pages/private/asset/show.blade.php --}}

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

{{-- Asset list --}}
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h1>Appointment</h1>
    </div>

    <div>
        id: {{ $appointment->id }}
    </div>
    <div>
        user: {{ $appointment->user }}
    </div>
    <div>
        service: {{ $appointment->service }}
    </div>
</div>
