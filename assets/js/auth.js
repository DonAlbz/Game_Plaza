document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginForm) {
        loginForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            await handleAuthSubmit(loginForm, `${BASE_URL}/api/auth/login.php`);
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            await handleAuthSubmit(registerForm, `${BASE_URL}/api/auth/register.php`);
        });
    }
});

async function handleAuthSubmit(form, endpoint) {
    const data = {};
    new FormData(form).forEach((value, key) => {
        data[key] = value;
    });

    const messageNode = document.getElementById('auth-message');
    messageNode.innerHTML = '';

    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });

        const result = await response.json();
        if (result.success) {
            messageNode.innerHTML = `<div class="alert alert-success">${escapeHtml(result.message)}</div>`;
            setTimeout(() => {
                window.location.href = `${BASE_URL}/index.php`;
            }, 800);
            return;
        }

        messageNode.innerHTML = `<div class="alert alert-danger">${escapeHtml(result.message || 'Authentication failed.')}</div>`;
    } catch (error) {
        messageNode.innerHTML = `<div class="alert alert-danger">Unable to contact server. Please try again.</div>`;
        console.error('Auth error', error);
    }
}

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
