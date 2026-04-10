export function initNotify() {
    const email = document.getElementById("notif-email");
    const sms = document.getElementById("notif-sms");
    const quiet = document.getElementById("notif-quiet");

    function applyQuietModeState() {
        if (!email || !sms || !quiet) return;

        if (quiet.checked) {
            email.checked = false;
            sms.checked = false;
            email.disabled = true;
            sms.disabled = true;
        } else {
            email.disabled = false;
            sms.disabled = false;
        }
    }

    if (quiet) {
        quiet.addEventListener("change", applyQuietModeState);
    }

    if (email) {
        email.addEventListener("change", () => {
            if (!quiet) return;
            if (email.checked) quiet.checked = false;
            applyQuietModeState();
        });
    }

    if (sms) {
        sms.addEventListener("change", () => {
            if (!quiet) return;
            if (sms.checked) quiet.checked = false;
            applyQuietModeState();
        });
    }

    applyQuietModeState();
}