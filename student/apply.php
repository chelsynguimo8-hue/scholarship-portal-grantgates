<?php
// student/apply.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$scholarship_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($scholarship_id <= 0) {
    die('Invalid scholarship selected.');
}

// Load scholarship from the real database
$scholarship = null;
$scholarship_sql = "
    SELECT scholarship_id, title, deadline, status
    FROM scholarships
    WHERE scholarship_id = $scholarship_id
    LIMIT 1
";
$scholarship_result = mysqli_query($conn, $scholarship_sql);

if ($scholarship_result && mysqli_num_rows($scholarship_result) > 0) {
    $scholarship = mysqli_fetch_assoc($scholarship_result);
} else {
    die('Scholarship not found.');
}

if (($scholarship['status'] ?? '') !== 'active') {
    die('This scholarship is no longer available.');
}

$page_title = 'Apply for Scholarship';
$success = '';
$error = '';

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$full_name = $_SESSION['full_name'] ?? '';
$email = $_SESSION['email'] ?? '';
$institution = '';
$program = '';
$gpa = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = trim($_POST['full_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $program     = trim($_POST['program'] ?? '');
    $gpa         = trim($_POST['gpa'] ?? '');

    if ($full_name === '' || $email === '' || $institution === '' || $program === '' || $gpa === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!isset($_POST['confirm'])) {
        $error = 'Please confirm that the information is true and complete.';
    } elseif (!isset($_FILES['transcript']) || $_FILES['transcript']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload your academic transcript.';
    } else {
        // Prevent duplicate applications
        $check_sql = "
            SELECT application_id
            FROM applications
            WHERE user_id = $user_id
              AND scholarship_id = $scholarship_id
            LIMIT 1
        ";
        $check_result = mysqli_query($conn, $check_sql);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $error = 'You have already applied for this scholarship.';
        } else {
            // Insert into your real applications table
            $insert_sql = "
                INSERT INTO applications (user_id, scholarship_id, status)
                VALUES ($user_id, $scholarship_id, 'pending')
            ";

            if (mysqli_query($conn, $insert_sql)) {
                // Optional: flash message (very useful)
                $_SESSION['success_message'] = 'Application submitted successfully!';

                // Redirect to dashboard
                redirect('student/dashboard.php');

                // Clear non-session fields after success
                $institution = '';
                $program = '';
                $gpa = '';
            } else {
                $error = 'Database error: ' . mysqli_error($conn);
            }
        }
    }
}

include '../includes/header.php';
?>

    <div class="container" style="max-width: 800px;">
        <div class="card">
            <div class="card-header">
                <h2>Apply for <?php echo htmlspecialchars($scholarship['title']); ?></h2>
                <p class="text-muted">
                    Deadline:
                    <?php
                    echo !empty($scholarship['deadline'])
                        ? htmlspecialchars(date('F d, Y', strtotime($scholarship['deadline'])))
                        : 'N/A';
                    ?>
                </p>
            </div>

            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success" style="margin-bottom: 1rem;">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error" style="margin-bottom: 1rem;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <h4 class="mb-4">Personal Information</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input
                                    type="text"
                                    name="full_name"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($full_name); ?>"
                                    required
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($email); ?>"
                                    required
                            >
                        </div>
                    </div>

                    <h4 class="mb-4 mt-6">Academic Information</h4>

                    <div class="form-group">
                        <label class="form-label">Institution *</label>
                        <input
                                type="text"
                                name="institution"
                                class="form-control"
                                value="<?php echo htmlspecialchars($institution); ?>"
                                required
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Program of Study *</label>
                            <input
                                    type="text"
                                    name="program"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($program); ?>"
                                    required
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Current GPA *</label>
                            <input
                                    type="number"
                                    step="0.01"
                                    name="gpa"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($gpa); ?>"
                                    required
                            >
                        </div>
                    </div>

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

                    <div class="form-group mt-6">
                        <label class="checkbox-label">
                            <input type="checkbox" name="confirm" required>
                            <span>I confirm that all information provided is true and complete.</span>
                        </label>
                    </div>

                    <div class="form-row mt-6">
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                        <a href="../scholarships.php" class="btn btn-soft">Cancel</a>
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