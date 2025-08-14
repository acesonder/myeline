<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Myeline - Cancer Care & Comfort Hub</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="landing-page">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-heart text-primary me-2"></i>
                Myeline
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Sign In
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="display-4 fw-bold mb-4">
                            Your Personal <span class="text-primary">Care Hub</span>
                        </h1>
                        <p class="lead mb-4">
                            A secure, personalized platform designed to help manage health, comfort, and connections 
                            during cancer care — with full caregiver support and AI-powered insights.
                        </p>
                        <div class="hero-buttons">
                            <button class="btn btn-primary btn-lg me-3" data-bs-toggle="modal" data-bs-target="#registerModal">
                                Get Started
                            </button>
                            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#loginModal">
                                Sign In
                            </button>
                        </div>
                        <div class="trust-indicators mt-4">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-2"></i>PHIPA/PIPEDA Compliant
                                <i class="fas fa-lock ms-3 me-2"></i>Secure & Private
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="assets/images/care-dashboard.svg" alt="Care Dashboard" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold">Everything You Need in One Place</h2>
                    <p class="lead">Comprehensive tools designed to make daily care management simple and supportive.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Symptom Tracking</h4>
                        <p>Log and track symptoms, pain levels, mood, and vitals with intelligent insights and trends.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h4>Medication Management</h4>
                        <p>Never miss a dose with smart reminders and adherence tracking for all medications.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h4>Caregiver Connection</h4>
                        <p>Secure messaging and real-time updates keep you connected with your care team.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4>AI Assistant</h4>
                        <p>Get personalized suggestions, health insights, and proactive care recommendations.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <h4>Comfort Features</h4>
                        <p>Relaxation tools, guided breathing, music, and daily inspiration for emotional well-being.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4>Emergency Support</h4>
                        <p>Quick access to emergency contacts and critical medical information when needed.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-4">Built with Care and Understanding</h2>
                    <p class="mb-4">
                        Myeline was created specifically for those facing the challenges of stage 4 cancer care. 
                        We understand that every day matters, and technology should make life easier, not harder.
                    </p>
                    <div class="about-features">
                        <div class="about-feature mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Designed for simplicity and accessibility</span>
                        </div>
                        <div class="about-feature mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Fully compliant with healthcare privacy regulations</span>
                        </div>
                        <div class="about-feature mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Responsive design works on all devices</span>
                        </div>
                        <div class="about-feature mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Real-time updates and AI-powered insights</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/about-care.svg" alt="About Care" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Welcome Back</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="loginEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">Forgot password?</a>
                    </div>
                    <hr>
                    <div class="text-center">
                        <span class="text-muted">Don't have an account?</span>
                        <button class="btn btn-link p-0" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">
                            Sign up here
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Create Your Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="registerEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registerPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="registerPassword" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="userType" class="form-label">I am a:</label>
                            <select class="form-select" id="userType" required>
                                <option value="">Please select...</option>
                                <option value="patient">Patient</option>
                                <option value="caregiver">Caregiver</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                            <label class="form-check-label" for="agreeTerms">
                                I agree to the <a href="#" class="text-decoration-none">PHIPA/PIPEDA Terms</a> and 
                                <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <span class="text-muted">Already have an account?</span>
                        <button class="btn btn-link p-0" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Sign in here
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer py-4 bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 Myeline. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-light text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-light text-decoration-none me-3">Terms of Service</a>
                    <a href="#" class="text-light text-decoration-none">Support</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>