<?php

require_once __DIR__ . '/config/session.php';
$loggedIn = false;

if (!empty($_SESSION['user_id'])) {
    $loggedIn = true;
}

$title = 'Home | Game Plaza';
ob_start();
?>
<div class="text-center" data-aos="fade-down">
    <h1 class="display-5">Welcome to Game Plaza</h1>
    <p class="lead text-muted">Discover gamers, build libraries, and join matches.</p>

    <?php if ($loggedIn): ?>
    <div class="d-flex justify-content-center gap-2 mt-4">
        <a href="<?= APP_BASE_URL ?>/views/profile.php" class="btn btn-primary">My Profile</a>
        <a href="<?= APP_BASE_URL ?>/views/games.php" class="btn btn-success">My Library</a>
    </div>
<?php else: ?>
    <div class="d-flex justify-content-center gap-2 mt-4">
        <a href="<?= APP_BASE_URL ?>/api/auth/login.php" class="btn btn-primary">Login</a>
        <a href="<?= APP_BASE_URL ?>/api/auth/register.php" class="btn btn-outline-light">Register</a>
    </div>
<?php endif; ?>
   
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/views/layout.php';
