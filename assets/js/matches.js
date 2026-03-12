document.addEventListener('DOMContentLoaded', function () {
    const matchesFeed = document.getElementById('matches-feed');
    const createForm = document.getElementById('create-match-form');
    const messageNode = document.getElementById('match-message');
    const matchesCol = document.getElementById('matches-col');
    const createCol = document.getElementById('create-match-col');
    const toggleBtn = document.getElementById('toggle-create-panel');
    const closeBtn = document.getElementById('close-create-panel');

    if (!matchesFeed || !createForm) {
        return;
    }

    function openPanel() {
        matchesCol.classList.remove('col-12');
        matchesCol.classList.add('col-lg-7');
        createCol.classList.remove('d-none');
    }

    function closePanel() {
        createCol.classList.add('d-none');
        matchesCol.classList.remove('col-lg-7');
        matchesCol.classList.add('col-12');
        messageNode.innerHTML = '';
    }

    toggleBtn.addEventListener('click', function () {
        const isOpen = !createCol.classList.contains('d-none');
        if (isOpen) {
            closePanel();
        } else {
            openPanel();
        }
    });

    closeBtn.addEventListener('click', closePanel);

    async function loadMatches() {
        matchesFeed.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>';
        try {
            const response = await fetch(`${BASE_URL}/api/matches/list.php`);
            const result = await response.json();
            if (!result.success) {
                matchesFeed.innerHTML = `<div class="alert alert-danger">${escapeHtml(result.message)}</div>`;
                return;
            }
            renderMatches(result.matches);
        } catch (error) {
            matchesFeed.innerHTML = '<div class="alert alert-danger">Unable to load matches.</div>';
            console.error(error);
        }
    }

    function renderMatches(matches) {
        if (matches.length === 0) {
            matchesFeed.innerHTML = '<div class="alert alert-info">No matches available yet. Create one to invite friends.</div>';
            return;
        }

        matchesFeed.innerHTML = matches.map(match => `
            <div class="card mb-3 bg-surface">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h5 class="mb-1">
                                ${escapeHtml(match.name)}
                                ${match.is_joined ? `<span class="badge bg-success ms-2">Joined</span>` : ''}
                            </h5>
                            <p class="mb-2 text-muted">${escapeHtml(match.game_name)} · ${escapeHtml(match.match_type)}</p>
                            <p class="mb-1">${escapeHtml((match.description || '').substring(0, 120))}${(match.description || '').length > 120 ? '...' : ''}</p>
                            <p class="mb-0 text-muted"><strong>Host:</strong> ${escapeHtml(match.creator_username)} · <strong>Scheduled:</strong> ${escapeHtml(match.scheduled_time)}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-secondary mb-2">${match.current_participants}/${escapeHtml(match.max_participants)}</span>
                            ${match.is_joined
                                ? `<button type="button" class="btn btn-sm btn-danger leave-match-btn" data-match-id="${match.id}">Leave</button>`
                                : `<button type="button" class="btn btn-sm btn-primary join-match-btn" data-match-id="${match.id}" ${match.current_participants >= match.max_participants ? 'disabled' : ''}>Join</button>`
                            }
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        matchesFeed.querySelectorAll('.join-match-btn').forEach(button => {
            button.addEventListener('click', handleJoinMatch);
        });
        matchesFeed.querySelectorAll('.leave-match-btn').forEach(button => {
            button.addEventListener('click', handleLeaveMatch);
        });
    }

    async function handleJoinMatch(event) {
        const matchId = event.target.dataset.matchId;
        if (!matchId) {
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/api/matches/join.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({match_id: matchId}),
            });
            const result = await response.json();
            messageNode.innerHTML = `<div class="alert alert-${result.success ? 'success' : 'danger'}">${escapeHtml(result.message)}</div>`;
            loadMatches();
        } catch (error) {
            messageNode.innerHTML = '<div class="alert alert-danger">Unable to join match.</div>';
            console.error(error);
        }
    }

    async function handleLeaveMatch(event) {
        const matchId = event.target.dataset.matchId;
        if (!matchId) {
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/api/matches/leave.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({match_id: matchId}),
            });
            const result = await response.json();
            messageNode.innerHTML = `<div class="alert alert-${result.success ? 'success' : 'danger'}">${escapeHtml(result.message)}</div>`;
            loadMatches();
        } catch (error) {
            messageNode.innerHTML = '<div class="alert alert-danger">Unable to leave match.</div>';
            console.error(error);
        }
    }

    createForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        const formData = new FormData(createForm);
        const payload = {};
        formData.forEach((value, key) => {
            payload[key] = value;
        });

        try {
            const response = await fetch(`${BASE_URL}/api/matches/create.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload),
            });
            const result = await response.json();
            messageNode.innerHTML = `<div class="alert alert-${result.success ? 'success' : 'danger'}">${escapeHtml(result.message)}</div>`;
            if (result.success) {
                createForm.reset();
                closePanel();
                loadMatches();
            }
        } catch (error) {
            messageNode.innerHTML = '<div class="alert alert-danger">Unable to create match.</div>';
            console.error(error);
        }
    });

    async function loadGameCatalog() {
        try {
            const response = await fetch(`${BASE_URL}/api/games/catalog.php`);
            const result = await response.json();
            if (!result.success) {
                return;
            }
            const select = document.getElementById('game_id');
            select.innerHTML = result.games.map(game => `<option value="${game.id}">${escapeHtml(game.name)}</option>`).join('');
        } catch (error) {
            console.error(error);
        }
    }

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

    loadMatches();
    loadGameCatalog();
});
