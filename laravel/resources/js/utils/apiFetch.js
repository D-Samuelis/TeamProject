export async function apiFetch(url, options = {}) {
    const headers = {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": window.BE_DATA?.csrf,
        ...options.headers,
    };

    if (options.body && !(options.body instanceof FormData) && !headers["Content-Type"]) {
        headers["Content-Type"] = "application/json";
    }

    const res = await fetch(url, { ...options, headers });

    if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        const error = new Error(err.message || err.error || `Request failed with status ${res.status}`);
        error.status = res.status;
        error.errors = err.errors ?? null; // populated on 422, used for field-level validation
        throw error;
    }

    return res.json().catch(() => ({}));
}