<?php
// Add this at the very top of student/dashboard.php
echo "<!-- DEBUG INFORMATION -->\n";
echo "<!-- Current file: " . __FILE__ . " -->\n";
echo "<!-- CSS should be at: " . $_SERVER['DOCUMENT_ROOT'] . "/grantgates/assets/css/style.css -->\n";

// Check if CSS file exists
$css_path = $_SERVER['DOCUMENT_ROOT'] . "/grantgates/assets/css/style.css";
if(file_exists($css_path)) {
    echo "<!-- CSS FILE EXISTS ✅ -->\n";
} else {
    echo "<!-- CSS FILE MISSING ❌: " . $css_path . " -->\n";
}
?>
<?php
// student/dashboard.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$page_title = 'Student Dashboard';
include '../includes/header.php';
?>

<div style="padding: 40px 0;">
    <h1>Welcome, <?php echo $_SESSION['full_name']; ?>!</h1>
    <p style="color: var(--gray-600); margin-bottom: 2rem;">Your scholarship journey starts here</p>
    
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">5</div>
            <div class="stat-label">Total Applications</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">2</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">2</div>
            <div class="stat-label">Under Review</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">1</div>
            <div class="stat-label">Approved</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="margin-bottom: 2rem;">
        <a href="apply.php" class="btn btn-primary">+ Apply for Scholarship</a>
    </div>
    
    <!-- Recent Applications -->
    <div class="card">
        <h3 style="margin-bottom: 1rem;">My Applications</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Scholarship</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Merit Scholarship</td>
                        <td>Mar 15, 2025</td>
                        <td><span class="badge badge-pending">Pending</span></td>
                        <td><a href="#" class="btn btn-sm btn-outline">View</a></td>
                    </tr>
                    <tr>
                        <td>Women in Tech</td>
                        <td>Mar 10, 2025</td>
                        <td><span class="badge badge-approved">Approved</span></td>
                        <td><a href="#" class="btn btn-sm btn-outline">View</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>