<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$page_title = 'My Profile';
$user_id = (int) ($_SESSION['user_id'] ?? 0);
$error = '';
$success = '';

$profile_result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id LIMIT 1");
if (!$profile_result || mysqli_num_rows($profile_result) === 0) {
    die('User profile not found.');
}

$profile = mysqli_fetch_assoc($profile_result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $institution = mysqli_real_escape_string($conn, trim($_POST['institution'] ?? ''));
    $program = mysqli_real_escape_string($conn, trim($_POST['program'] ?? ''));
    $gpa = trim($_POST['gpa'] ?? '');
    $year_of_study = trim($_POST['year_of_study'] ?? '1');

    if ($username === '' || $email === '' || $first_name === '' || $last_name === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($gpa !== '' && (!is_numeric($gpa) || (float) $gpa < 0 || (float) $gpa > 4.00)) {
        $error = 'GPA must be between 0.00 and 4.00.';
    } elseif ($date_of_birth !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_birth)) {
        $error = 'Date of birth must use YYYY-MM-DD format.';
    } elseif (!in_array($year_of_study, ['1', '2', '3', '4', '5'], true)) {
        $error = 'Invalid year of study.';
    } else {
        $check_sql = "
            SELECT user_id
            FROM users
            WHERE (email = '$email' OR username = '$username')
              AND user_id != $user_id
            LIMIT 1
        ";
        $check_result = mysqli_query($conn, $check_sql);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $error = 'That email or username is already in use.';
        } else {
            $phone_sql = $phone === '' ? 'NULL' : "'$phone'";
            $address_sql = $address === '' ? 'NULL' : "'$address'";
            $dob_sql = $date_of_birth === '' ? 'NULL' : "'" . mysqli_real_escape_string($conn, $date_of_birth) . "'";
            $institution_sql = $institution === '' ? 'NULL' : "'$institution'";
            $program_sql = $program === '' ? 'NULL' : "'$program'";
            $gpa_sql = $gpa === '' ? 'NULL' : "'" . mysqli_real_escape_string($conn, $gpa) . "'";

            $update_sql = "
                UPDATE users
                SET
                    username = '$username',
                    email = '$email',
                    first_name = '$first_name',
                    last_name = '$last_name',
                    phone = $phone_sql,
                    address = $address_sql,
                    date_of_birth = $dob_sql,
                    institution = $institution_sql,
                    program = $program_sql,
                    gpa = $gpa_sql,
                    year_of_study = '$year_of_study'
                WHERE user_id = $user_id
                LIMIT 1
            ";

            if (mysqli_query($conn, $update_sql)) {
                refreshCurrentUserSession();
                $success = 'Profile updated successfully.';
                $profile_result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id LIMIT 1");
                $profile = mysqli_fetch_assoc($profile_result);
            } else {
                $error = 'Failed to update profile: ' . mysqli_error($conn);
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="container" style="padding: 40px 0; max-width: 900px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
        <div>
            <h1 style="margin-bottom: 0.5rem;">My Profile</h1>
            <p style="color: var(--gray-600); margin: 0;">Keep your application details up to date.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success" style="margin-bottom: 1rem;"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($profile['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($profile['date_of_birth'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Institution</label>
                    <input type="text" name="institution" class="form-control" value="<?php echo htmlspecialchars($profile['institution'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Program</label>
                    <input type="text" name="program" class="form-control" value="<?php echo htmlspecialchars($profile['program'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">GPA</label>
                    <input type="number" step="0.01" min="0" max="4" name="gpa" class="form-control" value="<?php echo htmlspecialchars((string) ($profile['gpa'] ?? '')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Year of Study</label>
                    <select name="year_of_study" class="form-control">
                        <?php foreach (['1', '2', '3', '4', '5'] as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo (($profile['year_of_study'] ?? '1') === $year) ? 'selected' : ''; ?>>
                                Year <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="4"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Save Profile</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
