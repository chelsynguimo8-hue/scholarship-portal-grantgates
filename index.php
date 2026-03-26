<?php
// index.php
require_once 'includes/config.php';
$page_title = 'Home';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>
                <span class="grant-highlight">Grant</span>
                <span class="gates-highlight">Gates</span><br>
                Opening Doors to Your Future
            </h1>
            <p>Discover thousands of scholarship opportunities tailored to your academic profile and financial needs. Your dream education is just a click away.</p>
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
                <a href="scholarships.php" class="btn btn-outline btn-lg">Browse Scholarships</a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose GrantGates?</h2>
            <p>We make scholarship hunting simple and accessible</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3>Personalized Matches</h3>
                <p>Get scholarship recommendations based on your profile and preferences</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Easy Application</h3>
                <p>Apply to multiple scholarships with a single profile</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Real-time Tracking</h3>
                <p>Track your application status every step of the way</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Secure & Private</h3>
                <p>Your documents and data are always protected</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🌍</div>
                <h3>Global Opportunities</h3>
                <p>Access scholarships from institutions worldwide</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>Expert Support</h3>
                <p>Get help from our scholarship advisors</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>