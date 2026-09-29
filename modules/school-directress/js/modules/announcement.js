function initAnnouncementsModule() {
    // ── Image preview ──────────────────────────────────────────────────────
    const imageInput = document.getElementById('ann-image');
    let previewEl    = document.getElementById('ann-image-preview');

    if (imageInput) {
        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];

            if (!previewEl) {
                previewEl    = document.createElement('img');
                previewEl.id = 'ann-image-preview';
                previewEl.classList.add('ann-image-preview');
                imageInput.parentElement.appendChild(previewEl);
            }

            if (file) {
                previewEl.src           = URL.createObjectURL(file);
                previewEl.style.display = 'block';
            } else {
                previewEl.src           = '';
                previewEl.style.display = 'none';
            }
        });
    }

    // ── Modal wiring ─────────────────────────────────────────────────────
    const modalOverlay  = document.getElementById('comm-modal-overlay');
    const openModalBtn  = document.getElementById('comm-open-modal');
    const closeModalBtn = document.getElementById('comm-modal-close');
    const form          = document.getElementById('announcement-form');

    const openCommModal = () => modalOverlay?.classList.add('active');
    const closeCommModal = () => {
        modalOverlay?.classList.remove('active');
        form?.reset();
        if (previewEl) {
            previewEl.src           = '';
            previewEl.style.display = 'none';
        }
    };

    openModalBtn?.addEventListener('click', openCommModal);
    closeModalBtn?.addEventListener('click', closeCommModal);
    modalOverlay?.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeCommModal();
    });

    if (!window.commEscBound) {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeCommModal();
        });
        window.commEscBound = true;
    }

    // ── Clear preview on manual reset ──────────────────────────────────────
    if (form) {
        form.addEventListener('reset', () => {
            if (previewEl) {
                previewEl.src           = '';
                previewEl.style.display = 'none';
            }
        });
    }

    // ── Refresh list + close modal after router-driven submit ─────────────
    if (!window.commSuccessBound) {
        window.addEventListener('form:success', async () => {
            const overlay = document.getElementById('comm-modal-overlay');
            overlay?.classList.remove('active');

            const listEl = document.querySelector('.announcements-list');
            if (!listEl) return;

            try {
                const response = await fetch(window.location.href, { cache: 'no-store' });
                if (!response.ok) return;

                const html    = await response.text();
                const parser  = new DOMParser();
                const doc     = parser.parseFromString(html, 'text/html');
                const newList = doc.querySelector('.announcements-list');

                if (newList) {
                    listEl.innerHTML = newList.innerHTML;
                }
            } catch {
                // Silent fail
            }
        });
        window.commSuccessBound = true;
    }
}

window.addEventListener('page:loaded', initAnnouncementsModule);
document.addEventListener('DOMContentLoaded', initAnnouncementsModule);