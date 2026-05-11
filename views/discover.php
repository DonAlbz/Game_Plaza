<?php
require_once __DIR__ . '/../config/session.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_BASE_URL . '/views/login.php');
    exit;
}
$title = 'Discover Players | Game Plaza';
ob_start();
?>
<div class="row">
    <div class="col-12" data-aos="fade-up">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title">Discover Players</h2>
                <p class="text-muted">Search gamers by username, genre, or playstyle and connect with new teammates.</p>
                <form id="discover-form" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="discover-query" name="q" placeholder="Search by username or genre">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="discover-genre" name="genre">
                            <option value="">All genres</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
                <div id="discover-message"></div>
                <div id="discover-results"></div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$extraScripts = '<script src="' . APP_BASE_URL . '/assets/js/discover.js"></script>';
$extraModals = '
<div class="modal fade" id="discoverUserModal" tabindex="-1" aria-labelledby="discoverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bg-surface">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="discoverModalLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="dm-bio" class="text-muted mb-3"></p>
                <ul class="list-unstyled mb-3">
                    <li class="mb-1"><strong>Skill:</strong> <span id="dm-skill"></span></li>
                    <li class="mb-1"><strong>Availability:</strong> <span id="dm-availability"></span></li>
                    <li class="mb-1"><strong>Genres:</strong> <span id="dm-genres"></span></li>
                    <li class="mb-1"><strong>Playstyle:</strong> <span id="dm-playstyle"></span></li>
                    <li class="mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:-1px;">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                        </svg>
                        <span id="dm-followers"></span> followers
                    </li>
                </ul>
                <h6 class="mb-2">Game library</h6>
                <div id="dm-games"><div class="spinner-border spinner-border-sm text-light" role="status"></div></div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>';
require __DIR__ . '/layout.php';
