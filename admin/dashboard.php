<?php
// admin/dashboard.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$page_title = 'Admin Dashboard';

/*
|--------------------------------------------------------------------------
| Stats
|--------------------------------------------------------------------------
*/
$total_students = 0;
$active_scholarships = 0;
$total_applications = 0;
$pending_review = 0;

// Total students
$students_result = mysqli_query($conn, "
    SELECT COUNT(*) AS total_students
    FROM users
    WHERE role = 'student'
");

if ($students_result && mysqli_num_rows($students_result) > 0) {
    $row = mysqli_fetch_assoc($students_result);
    $total_students = (int) ($row['total_students'] ?? 0);
}

// Active scholarships
$scholarships_result = mysqli_query($conn, "
    SELECT COUNT(*) AS active_scholarships
    FROM scholarships
    WHERE status = 'active'
");

if ($scholarships_result && mysqli_num_rows($scholarships_result) > 0) {
    $row = mysqli_fetch_assoc($scholarships_result);
    $active_scholarships = (int) ($row['active_scholarships'] ?? 0);
}

// Total applications
$applications_result = mysqli_query($conn, "
    SELECT COUNT(*) AS total_applications
    FROM applications
");

if ($applications_result && mysqli_num_rows($applications_result) > 0) {
    $row = mysqli_fetch_assoc($applications_result);
    $total_applications = (int) ($row['total_applications'] ?? 0);
}

// Pending review
$pending_result = mysqli_query($conn, "
    SELECT COUNT(*) AS pending_review
    FROM applications
    WHERE status = 'pending'
");

if ($pending_result && mysqli_num_rows($pending_result) > 0) {
    $row = mysqli_fetch_assoc($pending_result);
    $pending_review = (int) ($row['pending_review'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| Recent Applications
|--------------------------------------------------------------------------
*/
$recent_applications = [];

$recent_sql = "
    SELECT
        a.application_id,
        a.application_date,
        a.status,
        u.first_name,
        u.last_name,
        s.title AS scholarship_title
    FROM applications a
    INNER JOIN users u ON a.user_id = u.user_id
    INNER JOIN scholarships s ON a.scholarship_id = s.scholarship_id
    ORDER BY a.application_date DESC
    LIMIT 10
";

$recent_result = mysqli_query($conn, $recent_sql);

if ($recent_result) {
    while ($row = mysqli_fetch_assoc($recent_result)) {
        $recent_applications[] = $row;
    }
}

include '../includes/header.php';
?>

    <div class="container" style="padding: 40px 0;">
        <h1>Admin Dashboard</h1>
        <p style="color: var(--gray-600); margin-bottom: 2rem;">
            Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?>
        </p>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_students; ?></div>
                <div class="stat-label">Total Students</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $active_scholarships; ?></div>
                <div class="stat-label">Active Scholarships</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $total_applications; ?></div>
                <div class="stat-label">Total Applications</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $pending_review; ?></div>
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

            <?php if (count($recent_applications) > 0): ?>
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
                        <?php foreach ($recent_applications as $application): ?>
                            <?php
                            $status = $application['status'] ?? 'pending';
                            $badge_class = 'badge-pending';

                            if ($status === 'approved') {
                                $badge_class = 'badge-approved';
                            } elseif ($status === 'under_review') {
                                $badge_class = 'badge-review';
                            } elseif ($status === 'rejected') {
                                $badge_class = 'badge-rejected';
                            }

                            $student_name = trim(
                                ($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? '')
                            );
                            ?>
                            <tr>
                                <td>#<?php echo str_pad((string) $application['application_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($student_name); ?></td>
                                <td><?php echo htmlspecialchars($application['scholarship_title']); ?></td>
                                <td>
                                    <?php
                                    echo !empty($application['application_date'])
                                        ? htmlspecialchars(date('M d, Y', strtotime($application['application_date'])))
                                        : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $status))); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="review_application.php?id=<?php echo (int) $application['application_id']; ?>" class="btn btn-sm btn-primary">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: var(--gray-600); margin: 0;">
                    No applications found yet.
                </p>
            <?php endif; ?>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>