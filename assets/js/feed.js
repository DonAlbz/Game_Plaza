document.addEventListener('DOMContentLoaded', function () {
    const results = document.getElementById('feed-results');
    const message = document.getElementById('feed-message');
    if (!results) {
        return;
    }

    const modalEl = document.getElementById('userModal');
    const userModal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

    async function loadFeed() {
        message.innerHTML = '';
        results.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>';

        try {
            const response = await fetch(`${BASE_URL}/api/social/following.php`);
            const data = await response.json();
            if (!data.success) {
                results.innerHTML = `<div class="alert alert-danger">${escapeHtml(data.message)}</div>`;
                return;
            }
            renderFeed(data.following);
        } catch (error) {
            results.innerHTML = '<div class="alert alert-danger">Unable to load follow feed.</div>';
            console.error(error);
        }
    }

    function renderFeed(users) {
        if (!users || users.length === 0) {
            results.innerHTML = '<div class="alert alert-info">You are not following anyone yet. Use Discover to follow players and build your feed.</div>';
            return;
        }

        results.innerHTML = users.map(user => `
            <div class="card mb-3 bg-surface user-card" role="button" style="cursor:pointer;"
                data-user-id="${user.id}"
                data-username="${escapeHtml(user.username)}"
                data-bio="${escapeHtml(user.bio || '')}"
                data-skill="${escapeHtml(user.skill_level)}"
                data-availability="${escapeHtml(user.availability)}"
                data-genres="${escapeHtml(user.preferred_genres || 'Not set')}"
                data-playstyle="${escapeHtml(user.preferred_playstyle || 'Not set')}"
                data-followers="${escapeHtml(user.followers_count ?? '')}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h5 class="mb-1">
                                ${escapeHtml(user.username)}
                                <span class="text-muted ms-2" style="font-size:0.8rem;font-weight:normal;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:-1px;">
                                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                    </svg>
                                    ${escapeHtml(String(user.followers_count ?? 0))}
                                </span>
                            </h5>
                            <p class="mb-0 text-muted"><strong>Genres:</strong> ${escapeHtml(user.preferred_genres || 'Not set')}</p>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        results.querySelectorAll('.user-card').forEach(card => {
            card.addEventListener('click', function () {
                document.getElementById('userModalLabel').textContent = card.dataset.username;
                document.getElementById('modal-bio').textContent = card.dataset.bio || 'No bio available';
                document.getElementById('modal-skill').textContent = card.dataset.skill;
                document.getElementById('modal-availability').textContent = card.dataset.availability;
                document.getElementById('modal-genres').textContent = card.dataset.genres;
                document.getElementById('modal-playstyle').textContent = card.dataset.playstyle;
                document.getElementById('modal-followers').textContent = card.dataset.followers || '—';
                document.getElementById('modal-games').innerHTML = '<div class="spinner-border spinner-border-sm text-light" role="status"></div>';
                if (userModal) userModal.show();

                fetch(`${BASE_URL}/api/users/games.php?user_id=${card.dataset.userId}`)
                    .then(r => r.json())
                    .then(data => {
                        const el = document.getElementById('modal-games');
                        if (!data.success || data.games.length === 0) {
                            el.innerHTML = '<span class="text-muted small">No games in library.</span>';
                            return;
                        }
                        el.innerHTML = data.games.map(g =>
                            `<span class="badge bg-secondary me-1 mb-1">${escapeHtml(g.name)}</span>`
                        ).join('');
                    })
                    .catch(() => {
                        document.getElementById('modal-games').innerHTML = '<span class="text-muted small">Unable to load games.</span>';
                    });
            });
        });
    }

    loadFeed();
});

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[char];
    });
}
