@auth
    <div id="snackbar-stack"></div>

    <template id="snackbar-template">
        <div class="snackbar-body">
            <span class="snackbar-type"></span>
            <p class="snackbar-label">Navigate to detail page of <span class="snackbar-type-inline"></span> <strong class="snackbar-name"></strong>?</p>
            <div class="snackbar-actions">
                <button class="snackbar-proceed">Proceed</button>
                <button class="snackbar-dismiss">Dismiss</button>
            </div>
        </div>
        <div class="snackbar-progress">
            <div class="snackbar-progress-bar"></div>
        </div>
    </template>
@endauth
