<?php
require_once __DIR__ . '/../config/session.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_BASE_URL . '/views/login.php');
    exit;
}
$title = 'Game Library | Game Plaza';
ob_start();
?>
<div class="row">
    <div class="col-lg-6" data-aos="fade-up">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title">My Game Library</h2>
                <div id="library-message"></div>
                <div id="game-library"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6" data-aos="fade-left">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title">Add a Game</h2>
                <form id="add-game-form">
                    <div class="mb-3">
                        <label for="game_id" class="form-label">Select Game</label>
                        <select class="form-select" id="game_id" name="game_id"></select>
                    </div>
                    <div class="mb-3">
                        <label for="platform" class="form-label">Platform</label>
                        <select class="form-select" id="platform" name="platform">
                            <option>Steam</option>
                            <option>Epic Games</option>
                            <option>Blizzard</option>
                            <option>Riot Games</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Add to library</button>
                </form>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title">Tip</h3>
                <p class="text-muted">Add the games you play most often. This improves matchmaking suggestions and lets other players discover you faster.</p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$extraScripts = '<script src="' . APP_BASE_URL . '/assets/js/games.js"></script>';
require __DIR__ . '/layout.php';
