document.addEventListener('DOMContentLoaded', function () {
    const suggestionsContainer = document.getElementById('suggestions-list');
    if (!suggestionsContainer) {
        return;
    }

    async function loadSuggestions() {
        suggestionsContainer.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>';

        try {
            const response = await fetch(`${BASE_URL}/api/matchmaking/suggestions.php`);
            const result = await response.json();
            if (!result.success) {
                suggestionsContainer.innerHTML = `<div class="alert alert-danger">${escapeHtml(result.message)}</div>`;
                return;
            }
            renderSuggestions(result.suggestions);
        } catch (error) {
            suggestionsContainer.innerHTML = '<div class="alert alert-danger">Unable to load matchmaking suggestions.</div>';
            console.error(error);
        }
    }

    function renderSuggestions(suggestions) {
        if (suggestions.length === 0) {
            suggestionsContainer.innerHTML = '<div class="alert alert-info">No compatible players found yet.</div>';
            return;
        }

        suggestionsContainer.innerHTML = suggestions.map(user => {
            const sharedGames = parseInt(user.shared_games, 10) || 0;
            const genreMatch  = parseInt(user.genre_match, 10)  || 0;
            const followBonus = user.is_followed ? 5 : 0;
            const skillBonus  = user.compatibility_score - (sharedGames * 4) - (genreMatch * 2) - followBonus;

            return `
            <div class="card mb-3 bg-surface">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">${escapeHtml(user.username)}</h5>
                            <p class="mb-2 text-muted">${escapeHtml(user.bio || 'No bio available')}</p>
                            <p class="mb-0"><strong>Skill:</strong> ${escapeHtml(user.skill_level)} · <strong>Availability:</strong> ${escapeHtml(user.availability)}</p>
                            <p class="mb-0"><strong>Genres:</strong> ${escapeHtml(user.preferred_genres || 'Not set')}</p>
                            ${user.shared_game_names && user.shared_game_names.length > 0 ? `
                            <div class="mt-2">
                                <span class="text-muted small d-block mb-1">Games in common:</span>
                                ${user.shared_game_names.map(n => `<span class="badge bg-secondary me-1 mb-1">${escapeHtml(n)}</span>`).join('')}
                            </div>` : ''}
                        </div>
                        <div class="score-wrapper">
                            <span class="badge bg-primary fs-6">Score ${user.compatibility_score}</span>
                            <div class="score-tooltip-box">
                                <div class="score-tooltip-row"><span>Shared games</span><span>${sharedGames} &times; 4 = <b>${sharedGames * 4}</b></span></div>
                                <div class="score-tooltip-row"><span>Genre match</span><span>${genreMatch} &times; 2 = <b>${genreMatch * 2}</b></span></div>
                                <div class="score-tooltip-row"><span>Skill / Availability</span><span><b>${Math.max(0, skillBonus)}</b></span></div>
                                <div class="score-tooltip-row"><span>Follow bonus</span><span><b>${followBonus}</b></span></div>
                                <div class="score-tooltip-divider"></div>
                                <div class="score-tooltip-row score-tooltip-total"><span>Total</span><span><b>${user.compatibility_score}</b></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    loadSuggestions();
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
