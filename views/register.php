<?php
require_once __DIR__ . '/../config/config.php';
$title = 'Register | Game Plaza';
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6" data-aos="fade-up">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">Register</h2>
                <div id="auth-message"></div>
                <form id="register-form">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Create account</button>
                </form>
                <div class="mt-3 text-center">
                    <small>Already registered? <a href="<?= APP_BASE_URL ?>/views/login.php">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
