<?php
// register.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$page_title = 'Register';
include 'includes/header.php';

if(isLoggedIn()) {
    if(isAdmin()) redirect('admin/dashboard.php');
    else redirect('student/dashboard.php');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = registerUser($_POST);
    if($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 500px;">
        <div class="auth-header">
            <h2>Create Account</h2>
            <p>Join GrantGates today</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Login here</a></div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Institution</label>
                    <input type="text" name="institution" class="form-control" value="<?php echo htmlspecialchars($_POST['institution'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Program</label>
                    <input type="text" name="program" class="form-control" value="<?php echo htmlspecialchars($_POST['program'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Year of Study</label>
                    <select name="year_of_study" class="form-control">
                        <?php foreach (['1', '2', '3', '4', '5'] as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo (($_POST['year_of_study'] ?? '1') === $year) ? 'selected' : ''; ?>>
                                Year <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="width: 100%;">Create Account</button>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <p>Already have an account? <a href="login.php" style="color: var(--gates-blue);">Login here</a></p>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
