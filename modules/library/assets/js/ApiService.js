'use strict';
// ============================================================
// ApiService — all HTTP communication with api.php
// ============================================================
class ApiService {
    #endpoint = 'api.php';

    async get(params) {
        const url = this.#endpoint + '?' + new URLSearchParams(params);
        const res = await fetch(url);
        return res.json();
    }

    async post(action, data) {
        const res = await fetch(`${this.#endpoint}?action=${action}`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(data),
        });
        return res.json();
    }

    async upload(formData) {
        const res = await fetch(this.#endpoint, { method: 'POST', body: formData });
        return res.json();
    }
}
