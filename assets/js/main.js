// Myeline - Cancer Care & Comfort Hub JavaScript

$(document).ready(function() {
    // Initialize the application
    initializeApp();
});

function initializeApp() {
    // Setup event listeners
    setupEventListeners();
    
    // Setup form validation
    setupFormValidation();
    
    // Initialize tooltips and popovers
    initializeBootstrapComponents();
    
    // Setup AJAX defaults
    setupAjax();
}

function setupEventListeners() {
    // Login form submission
    $('#loginForm').on('submit', handleLogin);
    
    // Registration form submission
    $('#registerForm').on('submit', handleRegistration);
    
    // Smooth scrolling for navigation links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 800);
        }
    });
    
    // Navbar background change on scroll
    $(window).on('scroll', function() {
        const navbar = $('.navbar');
        if ($(window).scrollTop() > 50) {
            navbar.addClass('scrolled');
        } else {
            navbar.removeClass('scrolled');
        }
    });
    
    // Modal chain handling (login <-> register)
    $('#loginModal').on('hidden.bs.modal', function() {
        $('#loginForm')[0].reset();
        clearFormErrors('#loginForm');
    });
    
    $('#registerModal').on('hidden.bs.modal', function() {
        $('#registerForm')[0].reset();
        clearFormErrors('#registerForm');
    });
}

function setupFormValidation() {
    // Password confirmation validation
    $('#confirmPassword').on('input', function() {
        const password = $('#registerPassword').val();
        const confirmPassword = $(this).val();
        
        if (password !== confirmPassword && confirmPassword !== '') {
            showFieldError(this, 'Passwords do not match');
        } else {
            clearFieldError(this);
        }
    });
    
    // Email validation
    $('input[type="email"]').on('blur', function() {
        const email = $(this).val();
        if (email && !isValidEmail(email)) {
            showFieldError(this, 'Please enter a valid email address');
        } else {
            clearFieldError(this);
        }
    });
    
    // Password strength validation
    $('#registerPassword').on('input', function() {
        const password = $(this).val();
        const strength = checkPasswordStrength(password);
        showPasswordStrength(this, strength);
    });
}

function initializeBootstrapComponents() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

function setupAjax() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            }
        }
    });
}

// Authentication Functions
function handleLogin(e) {
    e.preventDefault();
    
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    
    // Clear previous errors
    clearFormErrors(form);
    
    // Validate form
    if (!validateLoginForm(form)) {
        return;
    }
    
    // Show loading state
    setButtonLoading(submitBtn, true);
    
    // Prepare form data
    const formData = {
        email: $('#loginEmail').val(),
        password: $('#loginPassword').val(),
        remember: $('#rememberMe').is(':checked')
    };
    
    // Submit login request
    $.ajax({
        url: 'api/auth/login.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Login successful! Redirecting...', 'success');
                setTimeout(function() {
                    window.location.href = response.redirect || 'dashboard.php';
                }, 1000);
            } else {
                showNotification(response.message || 'Login failed. Please try again.', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Login error:', error);
            showNotification('Login failed. Please check your connection and try again.', 'error');
        },
        complete: function() {
            setButtonLoading(submitBtn, false);
        }
    });
}

function handleRegistration(e) {
    e.preventDefault();
    
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    
    // Clear previous errors
    clearFormErrors(form);
    
    // Validate form
    if (!validateRegistrationForm(form)) {
        return;
    }
    
    // Show loading state
    setButtonLoading(submitBtn, true);
    
    // Prepare form data
    const formData = {
        firstName: $('#firstName').val(),
        lastName: $('#lastName').val(),
        email: $('#registerEmail').val(),
        username: $('#username').val(),
        password: $('#registerPassword').val(),
        userType: $('#userType').val(),
        agreeTerms: $('#agreeTerms').is(':checked')
    };
    
    // Submit registration request
    $.ajax({
        url: 'api/auth/register.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Registration successful! Please check your email to verify your account.', 'success');
                $('#registerModal').modal('hide');
                setTimeout(function() {
                    $('#loginModal').modal('show');
                }, 500);
            } else {
                showNotification(response.message || 'Registration failed. Please try again.', 'error');
                if (response.errors) {
                    showFormErrors(form, response.errors);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Registration error:', error);
            showNotification('Registration failed. Please check your connection and try again.', 'error');
        },
        complete: function() {
            setButtonLoading(submitBtn, false);
        }
    });
}

// Validation Functions
function validateLoginForm(form) {
    let isValid = true;
    
    const email = $('#loginEmail').val().trim();
    const password = $('#loginPassword').val();
    
    if (!email) {
        showFieldError('#loginEmail', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showFieldError('#loginEmail', 'Please enter a valid email address');
        isValid = false;
    }
    
    if (!password) {
        showFieldError('#loginPassword', 'Password is required');
        isValid = false;
    }
    
    return isValid;
}

function validateRegistrationForm(form) {
    let isValid = true;
    
    // Validate all required fields
    const requiredFields = ['#firstName', '#lastName', '#registerEmail', '#username', '#registerPassword', '#userType'];
    
    requiredFields.forEach(function(field) {
        const value = $(field).val().trim();
        if (!value) {
            showFieldError(field, 'This field is required');
            isValid = false;
        }
    });
    
    // Validate email
    const email = $('#registerEmail').val().trim();
    if (email && !isValidEmail(email)) {
        showFieldError('#registerEmail', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password strength
    const password = $('#registerPassword').val();
    if (password && password.length < 8) {
        showFieldError('#registerPassword', 'Password must be at least 8 characters long');
        isValid = false;
    }
    
    // Validate password confirmation
    const confirmPassword = $('#confirmPassword').val();
    if (password !== confirmPassword) {
        showFieldError('#confirmPassword', 'Passwords do not match');
        isValid = false;
    }
    
    // Validate terms agreement
    if (!$('#agreeTerms').is(':checked')) {
        showFieldError('#agreeTerms', 'You must agree to the terms and conditions');
        isValid = false;
    }
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = [];
    
    if (password.length >= 8) strength++;
    else feedback.push('At least 8 characters');
    
    if (/[a-z]/.test(password)) strength++;
    else feedback.push('Lowercase letter');
    
    if (/[A-Z]/.test(password)) strength++;
    else feedback.push('Uppercase letter');
    
    if (/[0-9]/.test(password)) strength++;
    else feedback.push('Number');
    
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    else feedback.push('Special character');
    
    return {
        score: strength,
        feedback: feedback,
        label: ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'][strength]
    };
}

// UI Helper Functions
function showFieldError(field, message) {
    const $field = $(field);
    const $group = $field.closest('.mb-3');
    
    // Remove existing error
    $group.find('.invalid-feedback').remove();
    $field.removeClass('is-invalid');
    
    // Add error
    $field.addClass('is-invalid');
    $group.append(`<div class="invalid-feedback">${message}</div>`);
}

function clearFieldError(field) {
    const $field = $(field);
    const $group = $field.closest('.mb-3');
    
    $field.removeClass('is-invalid');
    $group.find('.invalid-feedback').remove();
}

function clearFormErrors(form) {
    $(form).find('.is-invalid').removeClass('is-invalid');
    $(form).find('.invalid-feedback').remove();
}

function showFormErrors(form, errors) {
    Object.keys(errors).forEach(function(field) {
        showFieldError(`#${field}`, errors[field]);
    });
}

function showPasswordStrength(field, strength) {
    const $field = $(field);
    const $group = $field.closest('.mb-3');
    
    // Remove existing strength indicator
    $group.find('.password-strength').remove();
    
    if ($field.val().length > 0) {
        const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#198754'];
        const color = colors[strength.score] || colors[0];
        
        const strengthHtml = `
            <div class="password-strength mt-2">
                <div class="d-flex justify-content-between small">
                    <span>Password Strength</span>
                    <span style="color: ${color}">${strength.label}</span>
                </div>
                <div class="progress mt-1" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: ${(strength.score / 5) * 100}%; background-color: ${color};">
                    </div>
                </div>
                ${strength.feedback.length > 0 ? `
                    <div class="small text-muted mt-1">
                        Missing: ${strength.feedback.join(', ')}
                    </div>
                ` : ''}
            </div>
        `;
        
        $group.append(strengthHtml);
    }
}

function setButtonLoading(button, loading) {
    const $btn = $(button);
    
    if (loading) {
        $btn.data('original-text', $btn.html());
        $btn.html('<span class="spinner"></span> Loading...');
        $btn.prop('disabled', true);
    } else {
        $btn.html($btn.data('original-text'));
        $btn.prop('disabled', false);
    }
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    $('.notification').remove();
    
    const typeClasses = {
        success: 'alert-success',
        error: 'alert-danger',
        warning: 'alert-warning',
        info: 'alert-info'
    };
    
    const notification = $(`
        <div class="notification alert ${typeClasses[type]} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}

// Dashboard Functions (for when user navigates to dashboard)
function initializeDashboard() {
    if (typeof dashboardWidgets !== 'undefined') {
        loadDashboardWidgets();
        setupWidgetInteractions();
        startRealTimeUpdates();
    }
}

function loadDashboardWidgets() {
    // Load today's care card
    loadTodaysCareCard();
    
    // Load medication schedule
    loadMedicationSchedule();
    
    // Load symptom trends
    loadSymptomTrends();
    
    // Load weather widget
    loadWeatherWidget();
    
    // Load hydration tracker
    loadHydrationTracker();
    
    // Load quick actions
    setupQuickActions();
}

function setupQuickActions() {
    $('.quick-action-btn').on('click', function(e) {
        e.preventDefault();
        const action = $(this).data('action');
        
        switch(action) {
            case 'log-pain':
                openPainLogger();
                break;
            case 'log-mood':
                openMoodLogger();
                break;
            case 'log-symptom':
                openSymptomLogger();
                break;
            case 'log-hydration':
                openHydrationLogger();
                break;
            case 'log-vitals':
                openVitalsLogger();
                break;
            case 'emergency':
                showEmergencyDialog();
                break;
        }
    });
}

function openPainLogger() {
    // Implementation for pain logging modal
    console.log('Opening pain logger...');
}

function openMoodLogger() {
    // Implementation for mood logging modal
    console.log('Opening mood logger...');
}

function openSymptomLogger() {
    // Implementation for symptom logging modal
    console.log('Opening symptom logger...');
}

function openHydrationLogger() {
    // Implementation for hydration logging
    console.log('Opening hydration logger...');
}

function openVitalsLogger() {
    // Implementation for vitals logging modal
    console.log('Opening vitals logger...');
}

function showEmergencyDialog() {
    const emergencyModal = $(`
        <div class="modal fade" id="emergencyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Emergency Assistance
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="lead">Do you need immediate medical assistance?</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-danger btn-lg" onclick="callEmergency()">
                                <i class="fas fa-phone me-2"></i>Call 911
                            </button>
                            <button class="btn btn-warning btn-lg" onclick="contactCaregiver()">
                                <i class="fas fa-user me-2"></i>Contact Caregiver
                            </button>
                            <button class="btn btn-info btn-lg" onclick="showMedicalInfo()">
                                <i class="fas fa-id-card me-2"></i>Show Medical ID
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);
    
    $('body').append(emergencyModal);
    $('#emergencyModal').modal('show');
    
    $('#emergencyModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function callEmergency() {
    window.location.href = 'tel:911';
}

function contactCaregiver() {
    // Implementation for contacting caregiver
    showNotification('Contacting your caregiver...', 'info');
}

function showMedicalInfo() {
    // Implementation for showing medical ID card
    showNotification('Displaying medical information...', 'info');
}

// Real-time updates
function startRealTimeUpdates() {
    // Simulate real-time updates every 30 seconds
    setInterval(function() {
        updateDashboardData();
    }, 30000);
}

function updateDashboardData() {
    // Check for new messages, appointments, etc.
    $.ajax({
        url: 'api/dashboard/updates.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update various dashboard components
                if (response.newMessages) {
                    updateMessageCount(response.newMessages);
                }
                if (response.upcomingMeds) {
                    updateMedicationReminders(response.upcomingMeds);
                }
                if (response.weatherUpdate) {
                    updateWeatherWidget(response.weatherUpdate);
                }
            }
        },
        error: function() {
            console.log('Failed to fetch updates');
        }
    });
}

// Utility functions
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatTime(time) {
    return new Date(time).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export functions for global access
window.Myeline = {
    showNotification,
    initializeDashboard,
    openPainLogger,
    openMoodLogger,
    openSymptomLogger,
    openHydrationLogger,
    openVitalsLogger,
    showEmergencyDialog
};