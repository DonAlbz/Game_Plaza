<?php
require_once __DIR__ . '/../config/session.php';
$loggedIn = !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Game Plaza') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= APP_BASE_URL ?>/index.php">Game Plaza</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if ($loggedIn): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/feed.php">Feed</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/discover.php">Discover</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/matchmaking.php">Matchmaking</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/matches.php">Matches</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/games.php">Library</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/views/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-5">
    <?= $content ?? '' ?>
</div>
<?= $extraModals ?? '' ?>
<script>const BASE_URL = '<?= APP_BASE_URL ?>';</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init();</script>
<script src="<?= APP_BASE_URL ?>/assets/js/auth.js"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
