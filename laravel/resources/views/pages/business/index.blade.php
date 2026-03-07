<div class="page">

    <div class="header">
        <h1>Businesses</h1>

        <button onclick="toggleModal('create-business-modal')" class="create-btn">
            + New Business
        </button>
    </div>

    <h2 class="section-title">Active Businesses</h2>

    <div class="business-grid">

        @foreach ($businesses as $business)
            <div class="business-card">

                <div class="card-header">
                    <h2>{{ $business->name }}</h2>
                    <p>
                        @if ($business->is_published)
                            Published
                        @else
                            Hidden
                        @endif
                    </p>
                </div>

                <p class="description">
                    {{ $business->description ?? 'No description provided.' }}
                </p>

                <div class="actions">

                    <a href="{{ route('business.show', $business->id) }}" class="manage-btn">
                        Manage
                    </a>

                    <form method="POST" action="{{ route('business.delete', $business->id) }}"
                        onsubmit="return confirm('Archive this business?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="delete-btn">
                            Delete
                        </button>
                    </form>

                </div>

            </div>
        @endforeach

    </div>



    {{-- DELETED BUSINESSES --}}
    @if ($deletedBusinesses->count())
        <h2 class="section-title deleted-section">Archived Businesses</h2>

        <div class="business-grid">

            @foreach ($deletedBusinesses as $business)
                <div class="business-card deleted">

                    <div class="card-header">
                        <h2>{{ $business->name }}</h2>
                        <span class="deleted-badge">Deleted</span>
                    </div>

                    <p class="description">
                        {{ $business->description ?? 'No description provided.' }}
                    </p>

                    <div class="actions">

                        <form method="POST" action="{{ route('business.restore', $business->id) }}" style="width:100%">
                            @csrf

                            <button type="submit" class="restore-btn">
                                Restore
                            </button>

                        </form>

                    </div>

                </div>
            @endforeach

        </div>
    @endif

    <!-- ================= CREATE BUSINESS MODAL ================= -->
    <div id="create-business-modal" class="modal hidden">
        <div class="modal-overlay" onclick="toggleModal('create-business-modal')"></div>
        <div class="modal-content">
            <h2>Create New Business</h2>
            <form method="POST" action="{{ route('business.store') }}">
                @csrf
                <input type="text" name="name" placeholder="Business Name" required>
                <textarea name="description" placeholder="Description"></textarea>
                <button type="submit">Create Business</button>
            </form>
            <button class="close-btn" onclick="toggleModal('create-business-modal')">Close</button>
        </div>
    </div>
</div>

<style>
    .page {
        padding: 32px;
        background: #f3f4f6;
        min-height: 100vh;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .header h1 {
        font-size: 24px;
        font-weight: bold;
    }

    .create-btn {
        background: #2563eb;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
    }

    .create-btn:hover {
        background: #1d4ed8;
    }

    /* dynamic grid */
    .business-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
    }

    /* card */
    .business-card {
        width: 320px;
        display: flex;
        flex-direction: column;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: white;
        padding: 24px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    /* deleted card */
    .business-card.deleted {
        background: #fef2f2;
        border-color: #fecaca;
    }

    /* card header */
    .card-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .card-header h2 {
        font-size: 18px;
        font-weight: 600;
    }

    .deleted-badge {
        font-size: 12px;
        background: #fecaca;
        color: #b91c1c;
        padding: 2px 6px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* description */
    .description {
        color: #4b5563;
        font-size: 14px;
        margin-bottom: 24px;
    }

    /* actions */
    .actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .actions form {
        margin: 0;
    }

    .manage-btn {
        flex: 1;
        text-align: center;
        background: #eff6ff;
        color: #2563eb;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
        text-decoration: none;
    }

    .manage-btn:hover {
        background: #dbeafe;
    }

    .delete-btn {
        color: #ef4444;
        background: none;
        border: none;
        cursor: pointer;
        text-align: center;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #ef4444;
    }

    .delete-btn:hover {
        color: #b91c1c;
    }

    .restore-btn {
        width: 100%;
        background: #16a34a;
        color: white;
        padding: 8px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
    }

    .restore-btn:hover {
        background: #15803d;
    }
</style>

<style>
    /* Modal container */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 50;
    }

    /* Hidden by default */
    .hidden {
        display: none;
    }

    /* Overlay */
    .modal-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }

    /* Modal content */
    .modal-content {
        position: relative;
        background: white;
        border-radius: 8px;
        padding: 24px;
        width: 400px;
        max-width: 90%;
        margin: 100px auto;
        z-index: 100;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Inputs and button */
    .modal-content input,
    .modal-content textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 12px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }

    .modal-content button {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        background: #2563eb;
        color: white;
    }

    .modal-content button:hover {
        background: #1d4ed8;
    }

    .close-btn {
        margin-top: 8px;
        background: #ef4444;
    }

    .close-btn:hover {
        background: #b91c1c;
    }
</style>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.classList.toggle('hidden');
    }
</script>
