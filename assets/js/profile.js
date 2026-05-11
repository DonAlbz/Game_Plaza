document.addEventListener('DOMContentLoaded', function () {
    const profileForm = document.getElementById('profile-form');
    const messageNode = document.getElementById('profile-message');

    if (!profileForm) {
        return;
    }

    let selectedGenres = [];

    function syncGenresHidden() {
        document.getElementById('preferred_genres').value = selectedGenres.join(', ');
    }

    async function loadGenres(currentGenres) {
        const selector = document.getElementById('genres-selector');
        try {
            const response = await fetch(`${BASE_URL}/api/users/genres.php`);
            const data = await response.json();
            if (!data.success) {
                selector.innerHTML = '<span class="text-muted small">Unable to load genres.</span>';
                return;
            }

            const allGenres = data.genres;
            currentGenres.forEach(g => {
                if (g && !allGenres.includes(g)) allGenres.push(g);
            });
            allGenres.sort();

            selector.innerHTML = allGenres.map(genre => {
                const active = currentGenres.includes(genre) ? 'active' : '';
                return `<button type="button" class="btn btn-sm genre-btn ${active ? 'btn-primary' : 'btn-outline-secondary'}" data-genre="${escapeHtml(genre)}">${escapeHtml(genre)}</button>`;
            }).join('');

            selector.querySelectorAll('.genre-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const genre = this.dataset.genre;
                    if (selectedGenres.includes(genre)) {
                        selectedGenres = selectedGenres.filter(g => g !== genre);
                        this.classList.replace('btn-primary', 'btn-outline-secondary');
                        this.classList.remove('active');
                    } else {
                        selectedGenres.push(genre);
                        this.classList.replace('btn-outline-secondary', 'btn-primary');
                        this.classList.add('active');
                    }
                    syncGenresHidden();
                });
            });
        } catch (error) {
            selector.innerHTML = '<span class="text-muted small">Unable to load genres.</span>';
            console.error(error);
        }
    }

    async function loadProfile() {
        try {
            const response = await fetch(`${BASE_URL}/api/users/get.php`);
            const result = await response.json();
            if (!result.success) {
                messageNode.innerHTML = `<div class="alert alert-danger">${escapeHtml(result.message)}</div>`;
                return;
            }
            const user = result.user;
            const preferences = result.preferences || {};

            document.getElementById('username').value = user.username || '';
            document.getElementById('bio').value = user.bio || '';
            document.getElementById('skill_level').value = user.skill_level || 'Beginner';
            document.getElementById('preferred_playstyle').value = preferences.preferred_playstyle || '';
            document.getElementById('competitive_preference').value = preferences.competitive_preference || 'Mixed';
            document.getElementById('team_size_preference').value = preferences.team_size_preference || 'Squad';

            document.getElementById('availability').value = user.availability || 'Evenings';

            selectedGenres = (preferences.preferred_genres || '')
                .split(',').map(g => g.trim()).filter(g => g !== '');
            syncGenresHidden();
            await loadGenres(selectedGenres);
        } catch (error) {
            messageNode.innerHTML = '<div class="alert alert-danger">Unable to load profile data.</div>';
            console.error(error);
        }
    }

    profileForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        const data = {};
        new FormData(profileForm).forEach((value, key) => {
            data[key] = value;
        });

        try {
            const response = await fetch(`${BASE_URL}/api/users/update.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data),
            });
            const result = await response.json();
            const type = result.success ? 'success' : 'danger';
            messageNode.innerHTML = `<div class="alert alert-${type}">${escapeHtml(result.message)}</div>`;
        } catch (error) {
            messageNode.innerHTML = '<div class="alert alert-danger">Unable to update profile.</div>';
            console.error(error);
        }
    });

    loadProfile();
});

function escapeHtml(value) {
    return value.replace(/[&<>"']/g, function (char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[char];
    });
}
