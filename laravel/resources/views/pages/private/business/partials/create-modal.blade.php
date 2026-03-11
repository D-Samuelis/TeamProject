<div id="create-business-modal" class="business-modal hidden">
    <div class="business-modal__overlay"></div>
    <div class="business-modal__content">
        <div class="business-modal__header mb-4">
            <h2 class="timeline-header__title">Create New Business</h2>
        </div>
        <form method="POST" action="{{ route('business.store') }}">
            @csrf
            <div class="business__search-container mb-3" style="width: 100%">
                <input type="text" name="name" placeholder="Business Name" required>
            </div>
            <div class="business__search-container mb-4" style="width: 100%; height: auto;">
                <textarea name="description" placeholder="Description (optional)" style="width: 100%; border: none; background: transparent; outline: none; padding: 5px; min-height: 80px;"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="business__nav-link is-active" style="border: none; cursor: pointer;">
                    Save Business
                </button>
                <button type="button" class="business__nav-link modal-close-trigger" style="border: none; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>