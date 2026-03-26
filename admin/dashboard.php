<?php
// admin/dashboard.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$page_title = 'Admin Dashboard';
include '../includes/header.php';
?>

<div style="padding: 40px 0;">
    <h1>Admin Dashboard</h1>
    <p style="color: var(--gray-600); margin-bottom: 2rem;">Welcome back, <?php echo $_SESSION['full_name']; ?></p>
    
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">150</div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">45</div>
            <div class="stat-label">Active Scholarships</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">380</div>
            <div class="stat-label">Total Applications</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">28</div>
            <div class="stat-label">Pending Review</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem;">Quick Actions</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="add_scholarship.php" class="btn btn-primary">+ Add Scholarship</a>
            <a href="manage_scholarships.php" class="btn btn-outline">Manage Scholarships</a>
            <a href="view_applications.php" class="btn btn-outline">Review Applications</a>
        </div>
    </div>
    
    <!-- Recent Applications -->
    <div class="card">
        <h3 style="margin-bottom: 1rem;">Recent Applications</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Scholarship</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#001</td>
                        <td>Marie Claude</td>
                        <td>Merit Scholarship</td>
                        <td>Mar 18, 2025</td>
                        <td><span class="badge badge-pending">Pending</span></td>
                        <td><a href="#" class="btn btn-sm btn-primary">Review</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>