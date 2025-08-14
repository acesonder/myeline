<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Require user to be logged in
requireLogin();

$user = getCurrentUser();
if (!$user) {
    header('Location: index.php');
    exit();
}

// Get today's data
$today = date('Y-m-d');
$userId = $user['id'];

// Get today's hydration
$db = getDB();
$hydrationToday = $db->fetch("
    SELECT COALESCE(SUM(amount_ml), 0) as total
    FROM hydration_logs 
    WHERE user_id = ? AND DATE(logged_at) = ?
", [$userId, $today]);

// Get recent pain levels
$recentPain = $db->fetch("
    SELECT pain_level, logged_at
    FROM pain_logs 
    WHERE user_id = ? 
    ORDER BY logged_at DESC 
    LIMIT 1
", [$userId]);

// Get recent mood
$recentMood = $db->fetch("
    SELECT mood_score, mood_type, logged_at
    FROM mood_logs 
    WHERE user_id = ? 
    ORDER BY logged_at DESC 
    LIMIT 1
", [$userId]);

// Get upcoming medications
$upcomingMeds = $db->fetchAll("
    SELECT m.name, m.dosage, ml.scheduled_time
    FROM medication_logs ml
    JOIN medications m ON ml.medication_id = m.id
    WHERE ml.user_id = ? 
    AND ml.status = 'scheduled'
    AND ml.scheduled_time >= NOW()
    AND ml.scheduled_time <= DATE_ADD(NOW(), INTERVAL 4 HOUR)
    ORDER BY ml.scheduled_time
    LIMIT 3
", [$userId]);

// Get unread messages count
$unreadMessages = getUnreadMessageCount($userId);

// Get daily content
$dailyQuote = getDailyContent('quote');
$dailyAffirmation = getDailyContent('affirmation');

// Get weather data
$weatherData = getWeatherData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Myeline</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center">
                <i class="fas fa-heart text-primary me-2"></i>
                <span class="fw-bold">Myeline</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="Myeline.openPainLogger()">
                        <i class="fas fa-chart-line me-2"></i> Log Symptoms
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#medications">
                        <i class="fas fa-pills me-2"></i> Medications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#appointments">
                        <i class="fas fa-calendar me-2"></i> Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#messages">
                        <i class="fas fa-comments me-2"></i> Messages
                        <?php if ($unreadMessages > 0): ?>
                            <span class="badge bg-danger ms-2"><?= $unreadMessages ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#comfort">
                        <i class="fas fa-spa me-2"></i> Comfort Zone
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#settings">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Sign Out
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <button class="btn btn-outline-secondary d-md-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="h3 mb-0">Welcome back, <?= htmlspecialchars($user['first_name']) ?>!</h1>
                <p class="text-muted mb-0"><?= formatDate(date('Y-m-d'), 'l, F j, Y') ?></p>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary me-2" onclick="Myeline.showEmergencyDialog()">
                    <i class="fas fa-phone me-1"></i> Emergency
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#profile">Profile</a></li>
                        <li><a class="dropdown-item" href="#settings">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions mb-4">
            <button class="quick-action-btn" data-action="log-pain">
                <i class="fas fa-exclamation-triangle quick-action-icon"></i>
                <span>Log Pain</span>
            </button>
            <button class="quick-action-btn" data-action="log-mood">
                <i class="fas fa-smile quick-action-icon"></i>
                <span>Log Mood</span>
            </button>
            <button class="quick-action-btn" data-action="log-symptom">
                <i class="fas fa-thermometer-half quick-action-icon"></i>
                <span>Log Symptom</span>
            </button>
            <button class="quick-action-btn" data-action="log-hydration">
                <i class="fas fa-tint quick-action-icon"></i>
                <span>Hydration</span>
            </button>
            <button class="quick-action-btn" data-action="log-vitals">
                <i class="fas fa-heartbeat quick-action-icon"></i>
                <span>Vitals</span>
            </button>
        </div>

        <!-- Dashboard Widgets -->
        <div class="row">
            <!-- Today's Care Card -->
            <div class="col-lg-8 col-xl-9">
                <div class="widget">
                    <div class="widget-header">
                        <h5 class="widget-title">
                            <i class="fas fa-calendar-day widget-icon me-2"></i>
                            Today's Care Card
                        </h5>
                    </div>
                    
                    <div class="row g-3">
                        <!-- Hydration Progress -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-tint text-info me-2"></i>
                                        Hydration Goal
                                    </h6>
                                    <?php 
                                    $hydrationGoal = 2000; // 2L daily goal
                                    $hydrationCurrent = $hydrationToday['total'];
                                    $hydrationPercent = min(100, ($hydrationCurrent / $hydrationGoal) * 100);
                                    ?>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-info" style="width: <?= $hydrationPercent ?>%"></div>
                                    </div>
                                    <small class="text-muted">
                                        <?= $hydrationCurrent ?>ml of <?= $hydrationGoal ?>ml 
                                        (<?= round($hydrationPercent) ?>%)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Pain Level -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-chart-line text-warning me-2"></i>
                                        Recent Pain Level
                                    </h6>
                                    <?php if ($recentPain): ?>
                                        <div class="d-flex align-items-center">
                                            <span class="display-6 me-2"><?= $recentPain['pain_level'] ?></span>
                                            <div>
                                                <small class="text-muted d-block">out of 10</small>
                                                <small class="text-muted"><?= timeAgo($recentPain['logged_at']) ?></small>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No pain data logged today</p>
                                        <button class="btn btn-sm btn-outline-primary" onclick="Myeline.openPainLogger()">
                                            Log Pain Level
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Mood -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-smile text-success me-2"></i>
                                        Recent Mood
                                    </h6>
                                    <?php if ($recentMood): ?>
                                        <div class="d-flex align-items-center">
                                            <span class="display-6 me-2"><?= $recentMood['mood_score'] ?></span>
                                            <div>
                                                <small class="text-muted d-block"><?= ucfirst($recentMood['mood_type'] ?? 'general') ?></small>
                                                <small class="text-muted"><?= timeAgo($recentMood['logged_at']) ?></small>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No mood data logged today</p>
                                        <button class="btn btn-sm btn-outline-primary" onclick="Myeline.openMoodLogger()">
                                            Log Mood
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Medications -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-pills text-primary me-2"></i>
                                        Next Medications
                                    </h6>
                                    <?php if (!empty($upcomingMeds)): ?>
                                        <?php foreach ($upcomingMeds as $med): ?>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <div class="fw-medium"><?= htmlspecialchars($med['name']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($med['dosage']) ?></small>
                                                </div>
                                                <small class="text-muted">
                                                    <?= formatTime($med['scheduled_time']) ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No medications scheduled</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Widgets -->
            <div class="col-lg-4 col-xl-3">
                <!-- Weather Widget -->
                <?php if ($weatherData): ?>
                <div class="widget">
                    <div class="widget-header">
                        <h6 class="widget-title">
                            <i class="fas fa-cloud-sun widget-icon me-2"></i>
                            Weather - Melfort, SK
                        </h6>
                    </div>
                    <div class="text-center">
                        <div class="display-4 mb-2"><?= round($weatherData['main']['temp']) ?>°C</div>
                        <p class="mb-1"><?= ucfirst($weatherData['weather'][0]['description']) ?></p>
                        <small class="text-muted">
                            Feels like <?= round($weatherData['main']['feels_like']) ?>°C
                        </small>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Daily Quote -->
                <?php if ($dailyQuote): ?>
                <div class="widget">
                    <div class="widget-header">
                        <h6 class="widget-title">
                            <i class="fas fa-quote-left widget-icon me-2"></i>
                            Daily Quote
                        </h6>
                    </div>
                    <blockquote class="blockquote">
                        <p class="mb-2">"<?= htmlspecialchars($dailyQuote['content']) ?>"</p>
                        <?php if ($dailyQuote['author']): ?>
                            <footer class="blockquote-footer">
                                <?= htmlspecialchars($dailyQuote['author']) ?>
                            </footer>
                        <?php endif; ?>
                    </blockquote>
                </div>
                <?php endif; ?>

                <!-- Daily Affirmation -->
                <?php if ($dailyAffirmation): ?>
                <div class="widget">
                    <div class="widget-header">
                        <h6 class="widget-title">
                            <i class="fas fa-heart widget-icon me-2"></i>
                            Daily Affirmation
                        </h6>
                    </div>
                    <p class="text-center fst-italic">
                        <?= htmlspecialchars($dailyAffirmation['content']) ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Emergency Contact -->
                <div class="widget">
                    <div class="widget-header">
                        <h6 class="widget-title">
                            <i class="fas fa-phone widget-icon me-2"></i>
                            Emergency Contact
                        </h6>
                    </div>
                    <?php if ($user['emergency_contact']): ?>
                        <div class="text-center">
                            <p class="mb-1 fw-medium"><?= htmlspecialchars($user['emergency_contact']) ?></p>
                            <p class="mb-3"><?= htmlspecialchars($user['emergency_phone']) ?></p>
                        </div>
                    <?php endif; ?>
                    <button class="btn btn-danger w-100" onclick="Myeline.showEmergencyDialog()">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Emergency Help
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <script>
        // Dashboard-specific JavaScript
        const dashboardWidgets = true;
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        // Initialize dashboard
        $(document).ready(function() {
            Myeline.initializeDashboard();
        });
        
        // Auto-refresh data every 5 minutes
        setInterval(function() {
            // Refresh dashboard data
            window.location.reload();
        }, 300000); // 5 minutes
    </script>
</body>
</html>