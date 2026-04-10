{{-- resources/views/pages/private/asset/index.blade.php --}}

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

{{-- Asset list --}}
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h1>Appointments</h1>
    </div>

    @forelse($appointments as $appointment)
        <div>
            <a href="{{ route('manage.appointment.show', $appointment->id) }}">{{ $appointment->id }}</a>
        </div>
    @empty
        <p>No appointments yet.</p>
    @endforelse
</div>
