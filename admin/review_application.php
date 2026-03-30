<?php
// admin/review_application.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$application_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($application_id <= 0) {
    die('Invalid application ID.');
}

$error = '';
$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = trim($_POST['status'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');

    $allowed_statuses = ['pending', 'under_review', 'approved', 'rejected'];

    if (!in_array($new_status, $allowed_statuses, true)) {
        $error = 'Invalid status selected.';
    } else {
        $new_status_escaped = mysqli_real_escape_string($conn, $new_status);
        $remarks_escaped = mysqli_real_escape_string($conn, $remarks);
        $reviewer_id = (int) ($_SESSION['user_id'] ?? 0);

        $update_sql = "
            UPDATE applications
            SET
                status = '$new_status_escaped',
                remarks = " . ($remarks === '' ? "NULL" : "'$remarks_escaped'") . ",
                reviewed_by = " . ($reviewer_id > 0 ? $reviewer_id : "NULL") . ",
                reviewed_date = NOW()
            WHERE application_id = $application_id
            LIMIT 1
        ";

        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['success_message'] = 'Application status updated successfully.';
            redirect('admin/dashboard.php');
        } else {
            $error = 'Failed to update application: ' . mysqli_error($conn);
        }
    }
}

// Fetch application details
$application_sql = "
    SELECT
        a.application_id,
        a.application_date,
        a.status,
        a.remarks,
        a.reviewed_by,
        a.reviewed_date,
        a.user_id,
        a.scholarship_id,
        u.username,
        u.email,
        u.first_name,
        u.last_name,
        u.phone,
        u.institution AS student_institution,
        u.program AS student_program,
        u.gpa AS student_gpa,
        u.year_of_study,
        s.title AS scholarship_title,
        s.description AS scholarship_description,
        s.eligibility,
        s.amount,
        s.deadline,
        s.status AS scholarship_status
    FROM applications a
    INNER JOIN users u ON a.user_id = u.user_id
    INNER JOIN scholarships s ON a.scholarship_id = s.scholarship_id
    WHERE a.application_id = $application_id
    LIMIT 1
";

$application_result = mysqli_query($conn, $application_sql);

if (!$application_result || mysqli_num_rows($application_result) === 0) {
    die('Application not found.');
}

$application = mysqli_fetch_assoc($application_result);

$page_title = 'Review Application';
include '../includes/header.php';
?>

    <div class="container" style="padding: 40px 0; max-width: 1000px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <div>
                <h1 style="margin-bottom: 0.5rem;">Review Application</h1>
                <p style="color: var(--gray-600); margin: 0;">
                    Application #<?php echo str_pad((string) $application['application_id'], 3, '0', STR_PAD_LEFT); ?>
                </p>
            </div>

            <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: 1rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem;">Application Summary</h3>

            <div class="table-container">
                <table>
                    <tbody>
                    <tr>
                        <th style="width: 220px;">Application ID</th>
                        <td>#<?php echo str_pad((string) $application['application_id'], 3, '0', STR_PAD_LEFT); ?></td>
                    </tr>
                    <tr>
                        <th>Student Name</th>
                        <td>
                            <?php
                            echo htmlspecialchars(
                                trim(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? ''))
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><?php echo htmlspecialchars($application['username'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($application['email'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?php echo htmlspecialchars($application['phone'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Institution</th>
                        <td><?php echo htmlspecialchars($application['student_institution'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Program</th>
                        <td><?php echo htmlspecialchars($application['student_program'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>GPA</th>
                        <td><?php echo $application['student_gpa'] !== null ? htmlspecialchars((string) $application['student_gpa']) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Year of Study</th>
                        <td><?php echo htmlspecialchars($application['year_of_study'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Scholarship</th>
                        <td><?php echo htmlspecialchars($application['scholarship_title'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>
                            <?php
                            echo $application['amount'] !== null
                                ? number_format((float) $application['amount'], 2) . ' FCFA'
                                : 'N/A';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Application Date</th>
                        <td>
                            <?php
                            echo !empty($application['application_date'])
                                ? htmlspecialchars(date('F d, Y H:i', strtotime($application['application_date'])))
                                : 'N/A';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Scholarship Deadline</th>
                        <td>
                            <?php
                            echo !empty($application['deadline'])
                                ? htmlspecialchars(date('F d, Y', strtotime($application['deadline'])))
                                : 'N/A';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Current Status</th>
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
                    <tr>
                        <th>Reviewer Remarks</th>
                        <td><?php echo nl2br(htmlspecialchars($application['remarks'] ?? 'No remarks added yet.')); ?></td>
                    </tr>
                    <tr>
                        <th>Reviewed Date</th>
                        <td>
                            <?php
                            echo !empty($application['reviewed_date'])
                                ? htmlspecialchars(date('F d, Y H:i', strtotime($application['reviewed_date'])))
                                : 'Not reviewed yet';
                            ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem;">Scholarship Details</h3>

            <div style="margin-bottom: 1rem;">
                <strong>Description</strong>
                <p style="margin-top: 0.5rem; color: var(--gray-700);">
                    <?php echo nl2br(htmlspecialchars($application['scholarship_description'] ?? 'No description available.')); ?>
                </p>
            </div>

            <div>
                <strong>Eligibility</strong>
                <p style="margin-top: 0.5rem; color: var(--gray-700);">
                    <?php echo nl2br(htmlspecialchars($application['eligibility'] ?? 'No eligibility information available.')); ?>
                </p>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1rem;">Update Application Status</h3>

            <form method="POST">
                <div class="form-group" style="max-width: 320px;">
                    <label for="status" class="form-label">Select Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="pending" <?php echo ($application['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="under_review" <?php echo ($application['status'] === 'under_review') ? 'selected' : ''; ?>>Under Review</option>
                        <option value="approved" <?php echo ($application['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo ($application['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>

                <div class="form-group" style="max-width: 640px;">
                    <label for="remarks" class="form-label">Reviewer Remarks</label>
                    <textarea name="remarks" id="remarks" class="form-control" rows="4"><?php echo htmlspecialchars($application['remarks'] ?? ''); ?></textarea>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary">Save Status</button>
                    <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
