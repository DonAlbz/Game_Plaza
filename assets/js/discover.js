document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('discover-form');
    const results = document.getElementById('discover-results');
    const message = document.getElementById('discover-message');
    const modalEl = document.getElementById('discoverUserModal');
    const discoverModal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

    if (!form) {
        return;
    }

    async function openUserModal(user) {
        document.getElementById('discoverModalLabel').textContent = user.username;
        document.getElementById('dm-bio').textContent = user.bio || 'No bio available';
        document.getElementById('dm-skill').textContent = user.skill_level;
        document.getElementById('dm-availability').textContent = user.availability;
        document.getElementById('dm-genres').textContent = user.preferred_genres || 'Not set';
        document.getElementById('dm-playstyle').textContent = user.preferred_playstyle || 'Not set';
        document.getElementById('dm-followers').textContent = user.followers_count ?? 0;
        document.getElementById('dm-games').innerHTML = '<div class="spinner-border spinner-border-sm text-light" role="status"></div>';
        if (discoverModal) discoverModal.show();

        try {
            const response = await fetch(`${BASE_URL}/api/users/games.php?user_id=${user.id}`);
            const data = await response.json();
            if (!data.success || data.games.length === 0) {
                document.getElementById('dm-games').innerHTML = '<span class="text-muted small">No games in library.</span>';
                return;
            }
            document.getElementById('dm-games').innerHTML = data.games.map(g =>
                `<span class="badge bg-secondary me-1 mb-1">${escapeHtml(g.name)}</span>`
            ).join('');
        } catch (e) {
            document.getElementById('dm-games').innerHTML = '<span class="text-muted small">Unable to load games.</span>';
        }
    }

    async function searchUsers(params = {}) {
        message.innerHTML = '';
        results.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>';

        try {
            const response = await fetch(`${BASE_URL}/api/users/search.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(params),
            });
            const data = await response.json();
            if (!data.success) {
                results.innerHTML = `<div class="alert alert-danger">${escapeHtml(data.message)}</div>`;
                return;
            }
            renderUsers(data.users);
        } catch (error) {
            results.innerHTML = '<div class="alert alert-danger">Search failed. Try again later.</div>';
            console.error(error);
        }
    }

    function renderUsers(users) {
        if (users.length === 0) {
            results.innerHTML = '<div class="alert alert-info">No gamers found. Try a broader keyword.</div>';
            return;
        }

        results.innerHTML = users.map(user => `
            <div class="card mb-3 bg-surface user-card" style="cursor:pointer;">
                <div class="card-body d-flex justify-content-between align-items-start gap-3">
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
                        <p class="mb-2 text-muted">${escapeHtml(user.bio || 'No bio available')}</p>
                        <p class="mb-0"><strong>Skill:</strong> ${escapeHtml(user.skill_level)} · <strong>Availability:</strong> ${escapeHtml(user.availability)}</p>
                        <p class="mb-0"><strong>Genres:</strong> ${escapeHtml(user.preferred_genres || 'Not set')}</p>
                    </div>
                    <div class="text-end">
                        ${user.canFollow
                            ? `<button type="button" class="btn btn-sm btn-outline-primary follow-btn" data-user-id="${user.id}">Follow</button>`
                            : `<button type="button" class="btn btn-sm btn-danger unfollow-btn" data-user-id="${user.id}">Unfollow</button>`
                        }
                    </div>
                </div>
            </div>
        `).join('');

        results.querySelectorAll('.follow-btn').forEach(button => {
            button.addEventListener('click', handleFollow);
        });
        results.querySelectorAll('.unfollow-btn').forEach(button => {
            button.addEventListener('click', handleUnfollow);
        });
        results.querySelectorAll('.user-card').forEach((card, i) => {
            card.addEventListener('click', function (e) {
                if (e.target.closest('button')) return;
                openUserModal(users[i]);
            });
        });
    }

    async function handleFollow(event) {
        const btn = event.target;
        const userId = btn.dataset.userId;
        if (!userId) {
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/api/social/follow.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({user_id: userId}),
            });
            const result = await response.json();
            message.innerHTML = `<div class="alert alert-${result.success ? 'success' : 'danger'}">${escapeHtml(result.message)}</div>`;
            if (result.success) {
                btn.textContent = 'Unfollow';
                btn.classList.remove('btn-outline-primary', 'follow-btn');
                btn.classList.add('btn-danger', 'unfollow-btn');
                btn.removeEventListener('click', handleFollow);
                btn.addEventListener('click', handleUnfollow);
            }
        } catch (error) {
            message.innerHTML = '<div class="alert alert-danger">Unable to follow player.</div>';
            console.error(error);
        }
    }

    async function handleUnfollow(event) {
        const btn = event.target;
        const userId = btn.dataset.userId;
        if (!userId) {
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/api/social/unfollow.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({user_id: userId}),
            });
            const result = await response.json();
            message.innerHTML = `<div class="alert alert-${result.success ? 'success' : 'danger'}">${escapeHtml(result.message)}</div>`;
            if (result.success) {
                btn.textContent = 'Follow';
                btn.classList.remove('btn-danger', 'unfollow-btn');
                btn.classList.add('btn-outline-primary', 'follow-btn');
                btn.removeEventListener('click', handleUnfollow);
                btn.addEventListener('click', handleFollow);
            }
        } catch (error) {
            message.innerHTML = '<div class="alert alert-danger">Unable to unfollow player.</div>';
            console.error(error);
        }
    }

    async function loadGenres() {
        try {
            const response = await fetch(`${BASE_URL}/api/users/genres.php`);
            const data = await response.json();
            if (!data.success) return;
            const select = document.getElementById('discover-genre');
            data.genres.forEach(genre => {
                const option = document.createElement('option');
                option.value = genre;
                option.textContent = genre;
                select.appendChild(option);
            });
        } catch (error) {
            console.error(error);
        }
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        searchUsers({
            q: document.getElementById('discover-query').value,
            genre: document.getElementById('discover-genre').value,
        });
    });

    loadGenres();
    searchUsers({});
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
