document.addEventListener('DOMContentLoaded', function () {
    const libraryContainer = document.getElementById('game-library');
    const form = document.getElementById('add-game-form');
    const messageNode = document.getElementById('library-message');

    if (!libraryContainer || !form) {
        return;
    }

    async function loadLibrary() {
        try {
            const [libraryResponse, catalogResponse] = await Promise.all([
                fetch(`${BASE_URL}/api/games/library.php`),
                fetch(`${BASE_URL}/api/games/catalog.php`),
            ]);
            const libraryData = await libraryResponse.json();
            const catalogData = await catalogResponse.json();

            if (!libraryData.success) {
                libraryContainer.innerHTML = '<div class="alert alert-danger">Unable to load library.</div>';
                return;
            }

            if (!catalogData.success) {
                document.getElementById('game_id').innerHTML = '<option>Select game failed</option>';
                return;
            }

            renderLibrary(libraryData.library);
            populateCatalog(catalogData.games, libraryData.library);
        } catch (error) {
            libraryContainer.innerHTML = '<div class="alert alert-danger">Unable to fetch library data.</div>';
            console.error(error);
        }
    }

    function renderLibrary(list) {
        if (list.length === 0) {
            libraryContainer.innerHTML = '<div class="alert alert-info">Your library is empty. Add games to get started.</div>';
            return;
        }

        const rows = list.map(item => {
            return `
                <div class="card mb-3 bg-surface">
                    <div class="card-body d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="mb-1">${escapeHtml(item.game_name)}</h5>
                            <p class="mb-0 text-muted">${escapeHtml(item.genre)} • ${escapeHtml(item.platform)}</p>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-game-id="${item.game_id}">Remove</button>
                    </div>
                </div>
            `;
        }).join('');

        libraryContainer.innerHTML = rows;
        libraryContainer.querySelectorAll('button[data-game-id]').forEach(button => {
            button.addEventListener('click', handleRemoveGame);
        });
    }

    function populateCatalog(games, libraryItems) {
        const existingIds = new Set(libraryItems.map(item => item.game_id));
        const select = document.getElementById('game_id');
        select.innerHTML = games.map(game => `
            <option value="${game.id}" ${existingIds.has(game.id) ? 'disabled' : ''}>
                ${escapeHtml(game.name)}${existingIds.has(game.id) ? ' (Owned)' : ''}
            </option>
        `).join('');
    }

    async function handleRemoveGame(event) {
        const gameId = event.target.dataset.gameId;
        if (!gameId) {
            return;
        }
        try {
            const response = await fetch(`${BASE_URL}/api/games/remove.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({game_id: gameId}),
            });
            const result = await response.json();
            messageNode.innerHTML = `<div class="alert alert-${result.success ? 'success' : 'danger'}">${escapeHtml(result.message)}</div>`;
            loadLibrary();
        } catch (error) {
            messageNode.innerHTML = '<div class="alert alert-danger">Could not remove game.</div>';
            console.error(error);
        }
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const data = {};
        new FormData(form).forEach((value, key) => {
            data[key] = value;
        });

        try {
            const response = await fetch(`${BASE_URL}/api/games/add.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data),
            });
            const result = await response.json();
            messageNode.innerHTML = `<div class="alert alert-${result.success ? 'success' : 'danger'}">${escapeHtml(result.message)}</div>`;
            if (result.success) {
                loadLibrary();
            }
        } catch (error) {
            messageNode.innerHTML = '<div class="alert alert-danger">Unable to add game.</div>';
            console.error(error);
        }
    });

    loadLibrary();
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
