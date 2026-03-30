<?php
// student/apply.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$scholarship_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($scholarship_id <= 0) {
    die('Invalid scholarship selected.');
}

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
$error = '';

$user_result = mysqli_query($conn, "
    SELECT first_name, last_name, email, institution, program, gpa, year_of_study
    FROM users
    WHERE user_id = $user_id
    LIMIT 1
");

$user = $user_result && mysqli_num_rows($user_result) === 1
    ? mysqli_fetch_assoc($user_result)
    : [];

$full_name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$email = $user['email'] ?? ($_SESSION['email'] ?? '');
$institution = $user['institution'] ?? '';
$program = $user['program'] ?? '';
$gpa = isset($user['gpa']) ? (string) $user['gpa'] : '';
$year_of_study = $user['year_of_study'] ?? '1';
$remarks = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $gpa = trim($_POST['gpa'] ?? '');
    $year_of_study = trim($_POST['year_of_study'] ?? '1');
    $remarks = trim($_POST['remarks'] ?? '');

    if (!in_array($year_of_study, ['1', '2', '3', '4', '5'], true)) {
        $year_of_study = '1';
    }

    if ($full_name === '' || $email === '' || $institution === '' || $program === '' || $gpa === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!is_numeric($gpa) || (float) $gpa < 0 || (float) $gpa > 4.00) {
        $error = 'Please enter a GPA between 0.00 and 4.00.';
    } elseif (!isset($_POST['confirm'])) {
        $error = 'Please confirm that the information is true and complete.';
    } elseif (!isset($_FILES['transcript']) || $_FILES['transcript']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload your academic transcript.';
    } else {
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
            $name_parts = preg_split('/\s+/', $full_name);
            $first_name = mysqli_real_escape_string($conn, $name_parts[0] ?? '');
            $last_name = mysqli_real_escape_string($conn, trim(implode(' ', array_slice($name_parts, 1))));
            if ($last_name === '') {
                $last_name = $first_name;
            }

            $email_escaped = mysqli_real_escape_string($conn, $email);
            $institution_escaped = mysqli_real_escape_string($conn, $institution);
            $program_escaped = mysqli_real_escape_string($conn, $program);
            $gpa_escaped = mysqli_real_escape_string($conn, $gpa);
            $remarks_escaped = mysqli_real_escape_string($conn, $remarks);

            $update_user_sql = "
                UPDATE users
                SET
                    first_name = '$first_name',
                    last_name = '$last_name',
                    email = '$email_escaped',
                    institution = '$institution_escaped',
                    program = '$program_escaped',
                    gpa = '$gpa_escaped',
                    year_of_study = '$year_of_study'
                WHERE user_id = $user_id
                LIMIT 1
            ";

            if (!mysqli_query($conn, $update_user_sql)) {
                $error = 'Failed to update your profile details: ' . mysqli_error($conn);
            } else {
                $insert_sql = "
                    INSERT INTO applications (user_id, scholarship_id, status, remarks)
                    VALUES ($user_id, $scholarship_id, 'pending', " . ($remarks === '' ? "NULL" : "'$remarks_escaped'") . ")
                ";

                if (mysqli_query($conn, $insert_sql)) {
                    $application_id = (int) mysqli_insert_id($conn);
                    $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documents';

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $files_to_process = [
                        'transcript' => true,
                        'letter' => false,
                    ];

                    foreach ($files_to_process as $field => $required) {
                        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
                            if ($required) {
                                $error = 'A required document is missing.';
                            }
                            continue;
                        }

                        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                            $error = 'Failed to upload ' . $field . '.';
                            break;
                        }

                        $original_name = basename($_FILES[$field]['name']);
                        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

                        if (!in_array($extension, $allowed_extensions, true)) {
                            $error = 'Only PDF, JPG, JPEG, and PNG files are allowed.';
                            break;
                        }

                        $safe_name = preg_replace('/[^A-Za-z0-9._-]/', '_', $original_name);
                        $target_name = $application_id . '_' . $field . '_' . time() . '_' . $safe_name;
                        $target_path = $upload_dir . DIRECTORY_SEPARATOR . $target_name;

                        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target_path)) {
                            $error = 'Unable to save uploaded file.';
                            break;
                        }

                        $db_file_name = mysqli_real_escape_string($conn, $original_name);
                        $db_file_path = mysqli_real_escape_string($conn, 'uploads/documents/' . $target_name);
                        $db_file_type = mysqli_real_escape_string($conn, $_FILES[$field]['type'] ?: strtoupper($extension));

                        $document_sql = "
                            INSERT INTO documents (application_id, file_name, file_path, file_type)
                            VALUES ($application_id, '$db_file_name', '$db_file_path', '$db_file_type')
                        ";

                        if (!mysqli_query($conn, $document_sql)) {
                            $error = 'Application saved, but file metadata could not be recorded.';
                            break;
                        }
                    }

                    if ($error === '') {
                        refreshCurrentUserSession();
                        $_SESSION['success_message'] = 'Application submitted successfully!';
                        redirect('student/dashboard.php');
                    }

                    mysqli_query($conn, "DELETE FROM applications WHERE application_id = $application_id LIMIT 1");
                } else {
                    $error = 'Database error: ' . mysqli_error($conn);
                }
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
                                    min="0"
                                    max="4"
                                    name="gpa"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($gpa); ?>"
                                    required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Year of Study *</label>
                        <select name="year_of_study" class="form-control" required>
                            <?php foreach (['1', '2', '3', '4', '5'] as $year): ?>
                                <option value="<?php echo $year; ?>" <?php echo $year_of_study === $year ? 'selected' : ''; ?>>
                                    Year <?php echo $year; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Application Remarks</label>
                        <textarea
                                name="remarks"
                                class="form-control"
                                rows="4"
                                placeholder="Share a short note about your goals or motivation."
                        ><?php echo htmlspecialchars($remarks); ?></textarea>
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
