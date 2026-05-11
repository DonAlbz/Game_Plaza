<?php
require_once __DIR__ . '/../config/session.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_BASE_URL . '/views/login.php');
    exit;
}
$title = 'Profile | Game Plaza';
ob_start();
?>
<div class="row">
    <div class="col-lg-8" data-aos="fade-up">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title">Your Profile</h2>
                <div id="profile-message"></div>
                <form id="profile-form">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="skill_level" class="form-label">Skill level</label>
                        <select class="form-select" id="skill_level" name="skill_level">
                            <option>Beginner</option>
                            <option>Intermediate</option>
                            <option>Advanced</option>
                            <option>Pro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="availability" class="form-label">Availability</label>
                        <select class="form-select" id="availability" name="availability">
                            <option>Mornings</option>
                            <option>Afternoons</option>
                            <option>Evenings</option>
                            <option>Nights</option>
                            <option>Weekdays</option>
                            <option>Weekends</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Favorite genres</label>
                        <div id="genres-selector" class="d-flex flex-wrap gap-2">
                            <span class="text-muted small">Loading genres...</span>
                        </div>
                        <input type="hidden" id="preferred_genres" name="preferred_genres">
                    </div>
                    <div class="mb-3">
                        <label for="preferred_playstyle" class="form-label">Playstyle</label>
                        <select class="form-select" id="preferred_playstyle" name="preferred_playstyle">
                            <option>Aggressive</option>
                            <option>Casual</option>
                            <option>Hybrid</option>
                            <option>Supportive</option>
                            <option>Tactical</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="competitive_preference" class="form-label">Competitive preference</label>
                        <select class="form-select" id="competitive_preference" name="competitive_preference">
                            <option>Casual</option>
                            <option>Competitive</option>
                            <option>Mixed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="team_size_preference" class="form-label">Team size</label>
                        <select class="form-select" id="team_size_preference" name="team_size_preference">
                            <option>Solo</option>
                            <option>Duo</option>
                            <option>Squad</option>
                            <option>Large</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save profile</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4" data-aos="fade-left">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h3 class="card-title">Quick tips</h3>
                <p class="text-muted">Keep your availability updated to match players faster. Your favorite genres and playstyle help the matchmaking system suggest the best gamers.</p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$extraScripts = '<script src="' . APP_BASE_URL . '/assets/js/profile.js"></script>';
require __DIR__ . '/layout.php';
