<?php
require_once __DIR__ . '/../config/session.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_BASE_URL . '/views/login.php');
    exit;
}
$title = 'Matches | Game Plaza';
ob_start();
?>
<div class="row" id="matches-row">
    <div class="col-12" id="matches-col" data-aos="fade-up">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="card-title mb-0">Available Matches</h2>
                    <button type="button" class="btn btn-primary" id="toggle-create-panel">
                        + Create a Match
                    </button>
                </div>
                <div id="matches-feed"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 d-none" id="create-match-col" data-aos="fade-left">
        <div class="card shadow-sm mb-4 bg-surface">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">New Match</h5>
                    <button type="button" class="btn-close btn-close-white" id="close-create-panel" aria-label="Close"></button>
                </div>
                <div id="match-message"></div>
                <form id="create-match-form">
                    <div class="mb-3">
                        <label for="name" class="form-label">Match title</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="game_id" class="form-label">Game</label>
                        <select class="form-select" id="game_id" name="game_id"></select>
                    </div>
                    <div class="mb-3">
                        <label for="match_type" class="form-label">Match type</label>
                        <select class="form-select" id="match_type" name="match_type">
                            <option>Casual</option>
                            <option>Tournament</option>
                            <option>Ranked</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="max_participants" class="form-label">Max participants</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" value="4" min="2" max="12" required>
                    </div>
                    <div class="mb-3">
                        <label for="scheduled_time" class="form-label">Schedule</label>
                        <input type="datetime-local" class="form-control" id="scheduled_time" name="scheduled_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Create Match</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$extraScripts = '<script src="' . APP_BASE_URL . '/assets/js/matches.js"></script>';
require __DIR__ . '/layout.php';
