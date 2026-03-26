<?php
// student/apply.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$page_title = 'Apply for Scholarship';
include '../includes/header.php';

$scholarship_id = $_GET['id'] ?? 0;
// Fetch scholarship details from database
?>

<div class="container" style="max-width: 800px;">
    <div class="card">
        <div class="card-header">
            <h2>Apply for Merit Scholarship 2025</h2>
            <p class="text-muted">Deadline: September 30, 2025</p>
        </div>
        
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" data-validate>
                <!-- Personal Information -->
                <h4 class="mb-4">Personal Information</h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" value="John Doe" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" value="john@example.com" readonly>
                    </div>
                </div>
                
                <!-- Academic Information -->
                <h4 class="mb-4 mt-6">Academic Information</h4>
                
                <div class="form-group">
                    <label class="form-label">Institution *</label>
                    <input type="text" name="institution" class="form-control" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Program of Study *</label>
                        <input type="text" name="program" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Current GPA *</label>
                        <input type="number" step="0.01" name="gpa" class="form-control" required>
                    </div>
                </div>
                
                <!-- Document Upload -->
                <h4 class="mb-4 mt-6">Required Documents</h4>
                
                <div class="form-group">
                    <label class="form-label">Academic Transcript *</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="transcript" name="transcript" accept=".pdf,.jpg,.png" required>
                        <label for="transcript" class="file-input-label">
                            <span>📎</span>
                            <span>Choose file or drag here</span>
                        </label>
                    </div>
                    <small class="form-text">PDF, JPG, PNG (Max 5MB)</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Recommendation Letter</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="letter" name="letter" accept=".pdf">
                        <label for="letter" class="file-input-label">
                            <span>📎</span>
                            <span>Choose file or drag here</span>
                        </label>
                    </div>
                    <small class="form-text">Optional but recommended</small>
                </div>
                
                <!-- Declaration -->
                <div class="form-group mt-6">
                    <label class="checkbox-label">
                        <input type="checkbox" name="confirm" required>
                        <span>I confirm that all information provided is true and complete.</span>
                    </label>
                </div>
                
                <!-- Submit Buttons -->
                <div class="form-row mt-6">
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                    <a href="scholarships.php" class="btn btn-soft">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}
</style>

<?php include '../includes/footer.php'; ?>