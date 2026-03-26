<?php
// login.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$page_title = 'Login';
include 'includes/header.php';

if(isLoggedIn()) {
    if(isAdmin()) redirect('admin/dashboard.php');
    else redirect('student/dashboard.php');
}

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = loginUser($_POST['email'], $_POST['password']);
    if($result['success']) {
        if($result['role'] == 'admin') redirect('admin/dashboard.php');
        else redirect('student/dashboard.php');
    } else {
        $error = $result['message'];
    }
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p>Login to your GrantGates account</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" style="width: 100%;">Login</button>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <p>Don't have an account? <a href="register.php" style="color: var(--gates-blue);">Register here</a></p>
                <p><a href="forgot-password.php" style="color: var(--gray-600);">Forgot Password?</a></p>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>