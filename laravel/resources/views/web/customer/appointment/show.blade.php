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

@can('update', $appointment)
    <button onclick="openModal('editAppointmentModal')">Edit Appointment</button>
@endcan

@can('delete', $appointment)
    <form method="POST" action="{{ route('manage.appointment.delete', $appointment->id) }}"
          onsubmit="return confirm('Delete this appointment?')">
        @csrf @method('DELETE')
        <button type="submit" style="color:red;background:none;border:none;cursor:pointer;">Delete</button>
    </form>
@endcan

{{-- MODAL: Edit Appointment --}}
<div id="editAppointmentModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('editAppointmentModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">Edit Appointment</h2>

        <form action="{{ route('manage.appointment.update', $appointment->id) }}" method="POST">
            @csrf @method('PUT')

            <div style="margin-bottom:1rem;">
                <label>Date</label><br>
                <input type="date" name="date" class="modal-form__input"
                       value="{{ $appointment->date }}"
                       min="{{ now()->toDateString() }}"
                       style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                @error('date') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label>Time</label><br>
                <select name="start_at" id="editSlotSelect"
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                    <option value="{{ substr($appointment->start_at, 0, 5) }}">
                        {{ substr($appointment->start_at, 0, 5) }} (current)
                    </option>
                </select>
                @error('start_at') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label>Status</label><br>
                <select name="status" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                    <option value="pending"   {{ $appointment->status === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $appointment->status === 'confirmed' ? 'selected' : '' }}
                    @cannot('manage', $appointment) disabled @endcannot>
                        Confirmed
                    </option>
                    <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('editAppointmentModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-backdrop { position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center; }
    .modal-box      { background:#fff;border-radius:8px;padding:2rem;width:100%;max-width:580px;max-height:90vh;overflow-y:auto;position:relative; }
    .modal-close    { position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer; }
    .btn-primary    { padding:8px 16px;background:#1a1a1a;color:#fff;border:none;border-radius:4px;cursor:pointer; }
    .btn-secondary  { padding:8px 16px;background:#fff;border:1px solid #ccc;border-radius:4px;cursor:pointer; }
</style>

<script>
    function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) el.style.display = 'none'; });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const dateInput  = document.querySelector('#editAppointmentModal input[name="date"]');
        const slotSelect = document.getElementById('editSlotSelect');
        const assetId    = {{ $appointment->asset_id }};
        const serviceId  = {{ $appointment->service_id }};
        const currentSlot = '{{ substr($appointment->start_at, 0, 5) }}';

        async function loadSlots(date) {
            slotSelect.innerHTML = '<option>Loading...</option>';
            try {
                const res   = await fetch(`/appointments/slots?asset_id=${assetId}&service_id=${serviceId}&from=${date}&to=${date}`);
                const data  = await res.json();
                const slots = data[date] ?? [];
                const all   = slots.includes(currentSlot) ? slots : [currentSlot, ...slots];

                slotSelect.innerHTML = all.map(s =>
                    `<option value="${s}" ${s === currentSlot ? 'selected' : ''}>${s}${s === currentSlot ? ' (current)' : ''}</option>`
                ).join('');
            } catch {
                slotSelect.innerHTML = '<option>Failed to load slots</option>';
            }
        }

        dateInput.addEventListener('change', () => loadSlots(dateInput.value));
    });
</script>

@if ($errors->any())
    <script>openModal('editAppointmentModal');</script>
@endif
