<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$page_title = 'Reset Password';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    }
    redirect('student/dashboard.php');
}

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
$error = '';
$success = '';
$is_valid_request = false;
$reset_row = null;

if ($token === '' || $email === '') {
    $error = 'This reset link is incomplete.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'This reset link contains an invalid email address.';
} else {
    $email_escaped = mysqli_real_escape_string($conn, $email);
    $query = "
        SELECT pr.reset_id, pr.user_id, pr.token_hash, pr.expires_at, pr.used_at
        FROM password_resets pr
        INNER JOIN users u ON u.user_id = pr.user_id
        WHERE u.email = '$email_escaped'
        ORDER BY pr.created_at DESC
    ";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['used_at'])) {
                continue;
            }
            if (strtotime((string) $row['expires_at']) < time()) {
                continue;
            }
            if (password_verify($token, $row['token_hash'])) {
                $is_valid_request = true;
                $reset_row = $row;
                break;
            }
        }
    }

    if (!$is_valid_request && $error === '') {
        $error = 'This reset link is invalid or has expired.';
    }
}

if ($is_valid_request && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string) ($_POST['password'] ?? '');
    $confirm_password = (string) ($_POST['confirm_password'] ?? '');

    if (strlen($password) < 6) {
        $error = 'Your new password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'The passwords do not match.';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_hash_escaped = mysqli_real_escape_string($conn, $password_hash);
        $reset_id = (int) $reset_row['reset_id'];
        $user_id = (int) $reset_row['user_id'];

        $update_user_sql = "
            UPDATE users
            SET password_hash = '$password_hash_escaped'
            WHERE user_id = $user_id
            LIMIT 1
        ";

        $mark_used_sql = "
            UPDATE password_resets
            SET used_at = NOW()
            WHERE reset_id = $reset_id
            LIMIT 1
        ";

        if (mysqli_query($conn, $update_user_sql) && mysqli_query($conn, $mark_used_sql)) {
            mysqli_query($conn, "DELETE FROM password_resets WHERE user_id = $user_id AND reset_id != $reset_id");
            $success = 'Your password has been reset successfully. You can now sign in.';
            $is_valid_request = false;
        } else {
            $error = 'Unable to reset your password right now.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 560px;">
        <div class="auth-header">
            <h2>Reset Password</h2>
            <p>Choose a new password for your account.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
                <div style="margin-top: 0.75rem;">
                    <a href="<?php echo htmlspecialchars(url('login.php')); ?>" style="color: inherit; font-weight: 600;">Go to login</a>
                </div>
            </div>
        <?php elseif ($is_valid_request): ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" minlength="6" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
            </form>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="<?php echo htmlspecialchars(url('forgot-password.php')); ?>" style="color: var(--gates-blue);">Request another reset link</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
