<?php
require_once __DIR__ . '/../config/session.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_BASE_URL . '/views/login.php');
    exit;
}
$title = 'Matchmaking | Game Plaza';
ob_start();
?>
<div class="row">
    <div class="col-12" data-aos="fade-up">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title">Matchmaking Suggestions</h2>
                <p class="text-muted">Find players with shared games, similar preferences, and compatible schedules.</p>
                <div id="suggestions-list"></div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$extraScripts = '<script src="' . APP_BASE_URL . '/assets/js/matchmaking.js"></script>';
require __DIR__ . '/layout.php';
