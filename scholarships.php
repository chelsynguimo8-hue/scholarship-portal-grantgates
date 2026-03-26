<?php
// scholarships.php
require_once 'includes/config.php';
$page_title = 'Scholarships';
include 'includes/header.php';
?>

<section class="scholarships-section">
    <div class="container">
        <div class="section-title">
            <h2>Available Scholarships</h2>
            <p>Find the perfect scholarship for your educational journey</p>
        </div>
        
        <!-- Search Bar -->
        <div style="background: white; padding: 2rem; border-radius: var(--radius-xl); margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
            <form style="display: flex; gap: 1rem;">
                <input type="text" placeholder="Search scholarships..." style="flex: 1; padding: 1rem; border: 2px solid var(--gray-200); border-radius: var(--radius-lg);">
                <select style="padding: 1rem; border: 2px solid var(--gray-200); border-radius: var(--radius-lg);">
                    <option>All Categories</option>
                    <option>Merit-based</option>
                    <option>Need-based</option>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        
        <!-- Scholarships Grid -->
        <div class="scholarship-grid">
            <!-- Scholarship 1 -->
            <div class="scholarship-card">
                <div class="scholarship-header">
                    <h3>Merit Scholarship for Engineering</h3>
                    <div class="scholarship-meta">
                        <span>📅 Deadline: Sep 30, 2025</span>
                    </div>
                </div>
                <div class="scholarship-body">
                    <p class="scholarship-description">For outstanding engineering students with excellent academic records and passion for innovation.</p>
                    <div class="scholarship-details">
                        <div class="detail-item">
                            <i>💰</i> Amount: 500,000 FCFA
                        </div>
                        <div class="detail-item">
                            <i>🎓</i> GPA Required: 3.5+
                        </div>
                    </div>
                    <div class="scholarship-amount">500,000 FCFA</div>
                </div>
                <div class="scholarship-footer">
                    <a href="login.php" class="btn btn-primary">Apply Now</a>
                    <a href="#" class="btn btn-outline">Details</a>
                </div>
            </div>
            
            <!-- Scholarship 2 -->
            <div class="scholarship-card">
                <div class="scholarship-header">
                    <h3>Need-Based Grant</h3>
                    <div class="scholarship-meta">
                        <span>📅 Deadline: Oct 15, 2025</span>
                    </div>
                </div>
                <div class="scholarship-body">
                    <p class="scholarship-description">Financial assistance for students from low-income backgrounds demonstrating academic potential.</p>
                    <div class="scholarship-details">
                        <div class="detail-item">
                            <i>💰</i> Amount: 300,000 FCFA
                        </div>
                        <div class="detail-item">
                            <i>📋</i> Income verification required
                        </div>
                    </div>
                    <div class="scholarship-amount">300,000 FCFA</div>
                </div>
                <div class="scholarship-footer">
                    <a href="login.php" class="btn btn-primary">Apply Now</a>
                    <a href="#" class="btn btn-outline">Details</a>
                </div>
            </div>
            
            <!-- Scholarship 3 -->
            <div class="scholarship-card">
                <div class="scholarship-header">
                    <h3>Women in Tech Scholarship</h3>
                    <div class="scholarship-meta">
                        <span>📅 Deadline: Oct 31, 2025</span>
                    </div>
                </div>
                <div class="scholarship-body">
                    <p class="scholarship-description">Encouraging and supporting women pursuing careers in technology and computer science.</p>
                    <div class="scholarship-details">
                        <div class="detail-item">
                            <i>💰</i> Amount: 400,000 FCFA
                        </div>
                        <div class="detail-item">
                            <i>👩‍💻</i> STEM majors only
                        </div>
                    </div>
                    <div class="scholarship-amount">400,000 FCFA</div>
                </div>
                <div class="scholarship-footer">
                    <a href="login.php" class="btn btn-primary">Apply Now</a>
                    <a href="#" class="btn btn-outline">Details</a>
                </div>
            </div>
            
            <!-- Scholarship 4 -->
            <div class="scholarship-card">
                <div class="scholarship-header">
                    <h3>Medical Excellence Scholarship</h3>
                    <div class="scholarship-meta">
                        <span>📅 Deadline: Nov 15, 2025</span>
                    </div>
                </div>
                <div class="scholarship-body">
                    <p class="scholarship-description">For future doctors and medical professionals with outstanding academic achievements.</p>
                    <div class="scholarship-details">
                        <div class="detail-item">
                            <i>💰</i> Amount: 600,000 FCFA
                        </div>
                        <div class="detail-item">
                            <i>⚕️</i> Medical students only
                        </div>
                    </div>
                    <div class="scholarship-amount">600,000 FCFA</div>
                </div>
                <div class="scholarship-footer">
                    <a href="login.php" class="btn btn-primary">Apply Now</a>
                    <a href="#" class="btn btn-outline">Details</a>
                </div>
            </div>
            
            <!-- Scholarship 5 -->
            <div class="scholarship-card">
                <div class="scholarship-header">
                    <h3>Business Leadership Grant</h3>
                    <div class="scholarship-meta">
                        <span>📅 Deadline: Dec 1, 2025</span>
                    </div>
                </div>
                <div class="scholarship-body">
                    <p class="scholarship-description">For aspiring entrepreneurs and business leaders with innovative ideas.</p>
                    <div class="scholarship-details">
                        <div class="detail-item">
                            <i>💰</i> Amount: 450,000 FCFA
                        </div>
                        <div class="detail-item">
                            <i>💼</i> Business plan required
                        </div>
                    </div>
                    <div class="scholarship-amount">450,000 FCFA</div>
                </div>
                <div class="scholarship-footer">
                    <a href="login.php" class="btn btn-primary">Apply Now</a>
                    <a href="#" class="btn btn-outline">Details</a>
                </div>
            </div>
            
            <!-- Scholarship 6 -->
            <div class="scholarship-card">
                <div class="scholarship-header">
                    <h3>Arts & Creativity Scholarship</h3>
                    <div class="scholarship-meta">
                        <span>📅 Deadline: Dec 15, 2025</span>
                    </div>
                </div>
                <div class="scholarship-body">
                    <p class="scholarship-description">Supporting talented artists, musicians, and creative individuals.</p>
                    <div class="scholarship-details">
                        <div class="detail-item">
                            <i>💰</i> Amount: 350,000 FCFA
                        </div>
                        <div class="detail-item">
                            <i>🎨</i> Portfolio required
                        </div>
                    </div>
                    <div class="scholarship-amount">350,000 FCFA</div>
                </div>
                <div class="scholarship-footer">
                    <a href="login.php" class="btn btn-primary">Apply Now</a>
                    <a href="#" class="btn btn-outline">Details</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>