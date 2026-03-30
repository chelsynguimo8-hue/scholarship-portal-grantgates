<?php
// student/dashboard.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$page_title = 'Student Dashboard';

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$full_name = $_SESSION['full_name'] ?? 'Student';

/*
|--------------------------------------------------------------------------
| Stats
|--------------------------------------------------------------------------
*/
$total_applications = 0;
$pending_count = 0;
$under_review_count = 0;
$approved_count = 0;

$stats_sql = "
    SELECT 
        COUNT(*) AS total_applications,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) AS under_review_count,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count
    FROM applications
    WHERE user_id = $user_id
";

$stats_result = mysqli_query($conn, $stats_sql);

if ($stats_result && mysqli_num_rows($stats_result) > 0) {
    $stats = mysqli_fetch_assoc($stats_result);
    $total_applications = (int) ($stats['total_applications'] ?? 0);
    $pending_count = (int) ($stats['pending_count'] ?? 0);
    $under_review_count = (int) ($stats['under_review_count'] ?? 0);
    $approved_count = (int) ($stats['approved_count'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| My Applications
|--------------------------------------------------------------------------
*/
$applications = [];

$applications_sql = "
    SELECT 
        a.application_id,
        a.scholarship_id,
        a.application_date,
        a.status,
        s.title,
        s.deadline,
        s.amount
    FROM applications a
    INNER JOIN scholarships s 
        ON a.scholarship_id = s.scholarship_id
    WHERE a.user_id = $user_id
    ORDER BY a.application_date DESC
";

$applications_result = mysqli_query($conn, $applications_sql);

if ($applications_result) {
    while ($row = mysqli_fetch_assoc($applications_result)) {
        $applications[] = $row;
    }
}

include '../includes/header.php';
?>

    <div class="container" style="padding: 40px 0;">
        <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
        <p style="color: var(--gray-600); margin-bottom: 2rem;">Your scholarship journey starts here</p>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                <?php
                echo htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_applications; ?></div>
                <div class="stat-label">Total Applications</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $pending_count; ?></div>
                <div class="stat-label">Pending</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $under_review_count; ?></div>
                <div class="stat-label">Under Review</div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?php echo $approved_count; ?></div>
                <div class="stat-label">Approved</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="margin-bottom: 2rem;">
            <a href="../scholarships.php" class="btn btn-primary">+ Browse Scholarships</a>
            <a href="profile.php" class="btn btn-outline">Update Profile</a>
        </div>

        <!-- My Applications -->
        <div class="card">
            <h3 style="margin-bottom: 1rem;">My Applications</h3>

            <?php if (count($applications) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>Scholarship</th>
                            <th>Amount</th>
                            <th>Deadline</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($application['title']); ?></td>
                                <td>
                                    <?php
                                    echo $application['amount'] !== null
                                        ? number_format((float) $application['amount'], 2) . ' FCFA'
                                        : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo !empty($application['deadline'])
                                        ? htmlspecialchars(date('M d, Y', strtotime($application['deadline'])))
                                        : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo !empty($application['application_date'])
                                        ? htmlspecialchars(date('M d, Y', strtotime($application['application_date'])))
                                        : 'N/A';
                                    ?>
                                </td>
                                <td>
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
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $status))); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="padding: 1rem 0;">
                    <p style="color: var(--gray-600); margin-bottom: 1rem;">
                        You have not applied for any scholarships yet.
                    </p>
                    <a href="../scholarships.php" class="btn btn-primary">Browse Scholarships</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
