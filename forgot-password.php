<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$page_title = 'Forgot Password';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    }
    redirect('student/dashboard.php');
}

$error = '';
$success = '';
$reset_link = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $email_escaped = mysqli_real_escape_string($conn, $email);
        $user_result = mysqli_query($conn, "
            SELECT user_id, email
            FROM users
            WHERE email = '$email_escaped'
            LIMIT 1
        ");

        if ($user_result && mysqli_num_rows($user_result) === 1) {
            $user = mysqli_fetch_assoc($user_result);
            $user_id = (int) $user['user_id'];
            $token = bin2hex(random_bytes(32));
            $token_hash = password_hash($token, PASSWORD_DEFAULT);

            mysqli_query($conn, "DELETE FROM password_resets WHERE user_id = $user_id OR expires_at < NOW() OR used_at IS NOT NULL");

            $token_hash_escaped = mysqli_real_escape_string($conn, $token_hash);
            $insert_sql = "
                INSERT INTO password_resets (user_id, token_hash, expires_at)
                VALUES ($user_id, '$token_hash_escaped', DATE_ADD(NOW(), INTERVAL 1 HOUR))
            ";

            if (mysqli_query($conn, $insert_sql)) {
                $reset_link = url('reset-password.php?token=' . urlencode($token) . '&email=' . urlencode($email));
                $success = 'Password reset link created. Since this local site does not send email yet, use the link below.';
            } else {
                $error = 'Unable to create a reset link right now.';
            }
        } else {
            $success = 'If an account exists for that email, a reset link has been prepared.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 560px;">
        <div class="auth-header">
            <h2>Forgot Password</h2>
            <p>Enter your account email to create a secure reset link.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Generate Reset Link</button>
        </form>

        <?php if ($reset_link): ?>
            <div class="card" style="margin-top: 1.5rem; padding: 1rem 1.25rem;">
                <p style="margin-bottom: 0.75rem; color: var(--gray-700);">Reset link:</p>
                <a href="<?php echo htmlspecialchars($reset_link); ?>" style="word-break: break-all;"><?php echo htmlspecialchars($reset_link); ?></a>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="<?php echo htmlspecialchars(url('login.php')); ?>" style="color: var(--gates-blue);">Back to login</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
