<?php
// admin/edit_scholarship.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$scholarship_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($scholarship_id <= 0) {
    die('Invalid scholarship ID.');
}

$error = '';

// Fetch scholarship
$query = "
    SELECT *
    FROM scholarships
    WHERE scholarship_id = $scholarship_id
    LIMIT 1
";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die('Scholarship not found.');
}

$scholarship = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eligibility = trim($_POST['eligibility'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    $allowed_statuses = ['active', 'expired', 'draft'];

    if ($title === '' || $description === '') {
        $error = 'Title and description are required.';
    } elseif ($amount !== '' && !is_numeric($amount)) {
        $error = 'Amount must be a valid number.';
    } elseif ($deadline !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
        $error = 'Deadline must be in YYYY-MM-DD format.';
    } elseif (!in_array($status, $allowed_statuses, true)) {
        $error = 'Invalid scholarship status.';
    } else {
        $title_escaped = mysqli_real_escape_string($conn, $title);
        $description_escaped = mysqli_real_escape_string($conn, $description);
        $eligibility_escaped = mysqli_real_escape_string($conn, $eligibility);
        $status_escaped = mysqli_real_escape_string($conn, $status);

        $amount_sql = ($amount === '') ? "NULL" : "'" . mysqli_real_escape_string($conn, $amount) . "'";
        $deadline_sql = ($deadline === '') ? "NULL" : "'" . mysqli_real_escape_string($conn, $deadline) . "'";

        $update_query = "
            UPDATE scholarships
            SET
                title = '$title_escaped',
                description = '$description_escaped',
                eligibility = '$eligibility_escaped',
                amount = $amount_sql,
                deadline = $deadline_sql,
                status = '$status_escaped'
            WHERE scholarship_id = $scholarship_id
            LIMIT 1
        ";

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = 'Scholarship updated successfully.';
            redirect('admin/manage_scholarships.php');
        } else {
            $error = 'Failed to update scholarship: ' . mysqli_error($conn);
        }
    }

    // Refresh form values after failed validation
    $scholarship['title'] = $title;
    $scholarship['description'] = $description;
    $scholarship['eligibility'] = $eligibility;
    $scholarship['amount'] = $amount;
    $scholarship['deadline'] = $deadline;
    $scholarship['status'] = $status;
}

$page_title = 'Edit Scholarship';
include '../includes/header.php';
?>

    <div class="container" style="padding: 40px 0; max-width: 900px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
            <div>
                <h1 style="margin-bottom: 0.5rem;">Edit Scholarship</h1>
                <p style="color: var(--gray-600); margin: 0;">
                    Scholarship #<?php echo (int) $scholarship['scholarship_id']; ?>
                </p>
            </div>

            <a href="manage_scholarships.php" class="btn btn-outline">← Back to Manage Scholarships</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: 1rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="title" class="form-label">Scholarship Title *</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        value="<?php echo htmlspecialchars($scholarship['title'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="description" class="form-label">Description *</label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="6"
                        required
                    ><?php echo htmlspecialchars($scholarship['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="eligibility" class="form-label">Eligibility</label>
                    <textarea
                        id="eligibility"
                        name="eligibility"
                        class="form-control"
                        rows="4"
                    ><?php echo htmlspecialchars($scholarship['eligibility'] ?? ''); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label for="amount" class="form-label">Amount (FCFA)</label>
                        <input
                            type="number"
                            step="0.01"
                            id="amount"
                            name="amount"
                            class="form-control"
                            value="<?php echo htmlspecialchars($scholarship['amount'] ?? ''); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input
                            type="date"
                            id="deadline"
                            name="deadline"
                            class="form-control"
                            value="<?php echo htmlspecialchars($scholarship['deadline'] ?? ''); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" <?php echo (($scholarship['status'] ?? '') === 'active') ? 'selected' : ''; ?>>
                                Active
                            </option>
                            <option value="expired" <?php echo (($scholarship['status'] ?? '') === 'expired') ? 'selected' : ''; ?>>
                                Expired
                            </option>
                            <option value="draft" <?php echo (($scholarship['status'] ?? '') === 'draft') ? 'selected' : ''; ?>>
                                Draft
                            </option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary">Update Scholarship</button>
                    <a href="manage_scholarships.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
