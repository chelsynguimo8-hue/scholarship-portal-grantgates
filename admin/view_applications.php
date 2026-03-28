<?php
// admin/view_applications.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$page_title = 'View Applications';

$scholarship_id = isset($_GET['scholarship_id']) ? (int) $_GET['scholarship_id'] : 0;

$scholarship = null;
$filter_title = 'All Applications';

/*
|--------------------------------------------------------------------------
| Optional scholarship filter
|--------------------------------------------------------------------------
*/
if ($scholarship_id > 0) {
    $scholarship_sql = "
        SELECT scholarship_id, title
        FROM scholarships
        WHERE scholarship_id = $scholarship_id
        LIMIT 1
    ";

    $scholarship_result = mysqli_query($conn, $scholarship_sql);

    if ($scholarship_result && mysqli_num_rows($scholarship_result) > 0) {
        $scholarship = mysqli_fetch_assoc($scholarship_result);
        $filter_title = 'Applications for: ' . $scholarship['title'];
    } else {
        $scholarship_id = 0;
    }
}

/*
|--------------------------------------------------------------------------
| Fetch applications
|--------------------------------------------------------------------------
*/
$applications = [];

$query = "
    SELECT
        a.application_id,
        a.user_id,
        a.scholarship_id,
        a.application_date,
        a.status,
        u.first_name,
        u.last_name,
        u.email,
        s.title AS scholarship_title,
        s.amount,
        s.deadline
    FROM applications a
    INNER JOIN users u ON a.user_id = u.user_id
    INNER JOIN scholarships s ON a.scholarship_id = s.scholarship_id
";

if ($scholarship_id > 0) {
    $query .= " WHERE a.scholarship_id = $scholarship_id ";
}

$query .= " ORDER BY a.application_date DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {
    $applications[] = $row;
}

include '../includes/header.php';
?>

    <div class="container" style="padding: 40px 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
            <div>
                <h1 style="margin-bottom: 0.5rem;">View Applications</h1>
                <p style="color: var(--gray-600); margin: 0;">
                    <?php echo htmlspecialchars($filter_title); ?>
                </p>
            </div>

            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <?php if ($scholarship_id > 0): ?>
                    <a href="view_applications.php" class="btn btn-outline">View All Applications</a>
                <?php endif; ?>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                <?php
                echo htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <?php if (count($applications) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Scholarship</th>
                            <th>Amount</th>
                            <th>Deadline</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($applications as $application): ?>
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

                                <td><?php echo htmlspecialchars($application['email'] ?? 'N/A'); ?></td>

                                <td><?php echo htmlspecialchars($application['scholarship_title']); ?></td>

                                <td>
                                    <?php
                                    echo $application['amount'] !== null
                                        ? number_format((float) $application['amount'], 0, '.', ',') . ' FCFA'
                                        : 'N/A';
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo !empty($application['deadline'])
                                        ? htmlspecialchars(date('d M Y', strtotime($application['deadline'])))
                                        : 'N/A';
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo !empty($application['application_date'])
                                        ? htmlspecialchars(date('d M Y H:i', strtotime($application['application_date'])))
                                        : 'N/A';
                                    ?>
                                </td>

                                <td>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $status))); ?>
                                    </span>
                                </td>

                                <td>
                                    <a href="review_application.php?id=<?php echo (int) $application['application_id']; ?>" class="btn btn-primary btn-sm">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="padding: 1rem 0;">
                    <p style="color: var(--gray-600); margin-bottom: 1rem;">No applications found.</p>

                    <?php if ($scholarship_id > 0): ?>
                        <a href="manage_scholarships.php" class="btn btn-outline">Back to Scholarships</a>
                    <?php else: ?>
                        <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.875rem;
        }
    </style>

<?php include '../includes/footer.php'; ?>