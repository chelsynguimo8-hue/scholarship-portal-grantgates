<?php
// admin/add_scholarship.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$page_title = 'Add Scholarship';

$error = '';
$title = '';
$description = '';
$eligibility = '';
$amount = '';
$deadline = '';
$status = 'active';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eligibility = trim($_POST['eligibility'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    $allowed_statuses = ['active', 'expired', 'draft'];

    if ($title === '' || $description === '' || $eligibility === '' || $amount === '' || $deadline === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!is_numeric($amount) || (float)$amount < 0) {
        $error = 'Amount must be a valid positive number.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
        $error = 'Deadline must be a valid date.';
    } elseif (!in_array($status, $allowed_statuses, true)) {
        $error = 'Invalid scholarship status.';
    } else {
        $title_escaped = mysqli_real_escape_string($conn, $title);
        $description_escaped = mysqli_real_escape_string($conn, $description);
        $eligibility_escaped = mysqli_real_escape_string($conn, $eligibility);
        $amount_escaped = mysqli_real_escape_string($conn, $amount);
        $deadline_escaped = mysqli_real_escape_string($conn, $deadline);
        $status_escaped = mysqli_real_escape_string($conn, $status);

        $created_by = (int) ($_SESSION['user_id'] ?? 0);

        $query = "
            INSERT INTO scholarships (title, description, eligibility, amount, deadline, status, created_by)
            VALUES (
                '$title_escaped',
                '$description_escaped',
                '$eligibility_escaped',
                '$amount_escaped',
                '$deadline_escaped',
                '$status_escaped',
                " . ($created_by > 0 ? $created_by : "NULL") . "
            )
        ";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = 'Scholarship added successfully!';
            redirect('admin/manage_scholarships.php');
        } else {
            $error = 'Failed to add scholarship: ' . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

    <div class="container" style="padding: 40px 0; max-width: 920px;">
        <div class="page-topbar">
            <div>
                <h1 class="page-title">Add New Scholarship</h1>
                <p class="page-subtitle">Create a new scholarship opportunity for students.</p>
            </div>
            <a href="manage_scholarships.php" class="btn btn-outline">← Back to Manage Scholarships</a>
        </div>

        <div class="form-card">
            <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom: 1.25rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-section">
                    <h3>Scholarship Information</h3>

                    <div class="form-group">
                        <label for="title" class="form-label">Scholarship Title *</label>
                        <input
                                type="text"
                                id="title"
                                name="title"
                                class="form-control"
                                value="<?php echo htmlspecialchars($title); ?>"
                                required
                        >
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description *</label>
                        <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="6"
                                required
                        ><?php echo htmlspecialchars($description); ?></textarea>
                        <small class="form-hint">Provide a clear and detailed description of the scholarship.</small>
                    </div>

                    <div class="form-group">
                        <label for="eligibility" class="form-label">Eligibility Criteria *</label>
                        <textarea
                                id="eligibility"
                                name="eligibility"
                                class="form-control"
                                rows="5"
                                required
                        ><?php echo htmlspecialchars($eligibility); ?></textarea>
                        <small class="form-hint">List all requirements applicants must meet.</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Application Settings</h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="amount" class="form-label">Amount (FCFA) *</label>
                            <input
                                    type="number"
                                    id="amount"
                                    name="amount"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?php echo htmlspecialchars($amount); ?>"
                                    required
                            >
                        </div>

                        <div class="form-group">
                            <label for="deadline" class="form-label">Application Deadline *</label>
                            <input
                                    type="date"
                                    id="deadline"
                                    name="deadline"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($deadline); ?>"
                                    required
                            >
                        </div>

                        <div class="form-group">
                            <label for="status" class="form-label">Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>>Expired</option>
                                <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Scholarship</button>
                    <a href="manage_scholarships.php" class="btn btn-soft">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .page-topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .page-title {
            margin: 0 0 0.4rem;
            font-size: 2rem;
            line-height: 1.2;
        }

        .page-subtitle {
            margin: 0;
            color: var(--gray-600, #6b7280);
        }

        .form-card {
            background: #fff;
            border-radius: 18px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            border: 1px solid #eef2f7;
        }

        .form-section + .form-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #edf2f7;
        }

        .form-section h3 {
            margin: 0 0 1rem;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }

        .form-control {
            width: 100%;
            padding: 0.95rem 1rem;
            border: 1px solid #dbe3ee;
            border-radius: 12px;
            font-size: 0.98rem;
            background: #fff;
            box-sizing: border-box;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.12);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-hint {
            display: block;
            margin-top: 0.45rem;
            color: #6b7280;
            font-size: 0.88rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .form-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-soft {
            background: #f3f6fb;
            color: #1f2937;
            border: 1px solid #dbe3ee;
        }

        .btn-soft:hover {
            background: #e8eef8;
        }

        @media (max-width: 640px) {
            .form-card {
                padding: 1.25rem;
            }

            .page-title {
                font-size: 1.6rem;
            }
        }
    </style>

<?php include '../includes/footer.php'; ?>
