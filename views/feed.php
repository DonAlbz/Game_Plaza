<?php
require_once __DIR__ . '/../config/session.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_BASE_URL . '/views/login.php');
    exit;
}
$title = 'Feed | Game Plaza';
ob_start();
?>
<div class="row">
    <div class="col-12" data-aos="fade-up">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title">Follow Feed</h2>
                <p class="text-muted">See the players you follow and their key details. This feed helps you stay connected with your favorite gamers.</p>
                <div id="feed-message"></div>
                <div id="feed-results"></div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$extraScripts = '<script src="' . APP_BASE_URL . '/assets/js/feed.js"></script>';
$extraModals = '
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-surface">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="userModalLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modal-bio" class="text-muted mb-3"></p>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><strong>Skill:</strong> <span id="modal-skill"></span></li>
                    <li class="mb-1"><strong>Availability:</strong> <span id="modal-availability"></span></li>
                    <li class="mb-1"><strong>Genres:</strong> <span id="modal-genres"></span></li>
                    <li class="mb-1"><strong>Playstyle:</strong> <span id="modal-playstyle"></span></li>
                    <li class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:-1px;">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                        </svg>
                        <span id="modal-followers"></span> followers
                    </li>
                </ul>
                <hr class="border-secondary">
                <h6 class="mb-2">Game library</h6>
                <div id="modal-games"></div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>';
require __DIR__ . '/layout.php';
