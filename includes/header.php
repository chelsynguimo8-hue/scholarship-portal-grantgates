<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>GrantGates</title>
    
    <!-- CORRECTED CSS PATH - Use absolute path from root -->
    <link rel="stylesheet" href="/grantgates/assets/css/style.css">
    
    <!-- Backup inline style in case CSS fails -->
    <style>
        /* Emergency styles - will show if CSS doesn't load */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 1200px; margin: 0 auto; }
        header { background: white; padding: 20px 0; border-bottom: 1px solid #eee; }
        .navbar { display: flex; justify-content: space-between; align-items: center; }
        .nav-menu { display: flex; list-style: none; gap: 20px; }
        .nav-menu a { text-decoration: none; color: #333; }
        .btn { padding: 8px 20px; border-radius: 4px; text-decoration: none; }
        .btn-primary { background: #4361ee; color: white; }
        .btn-outline { border: 1px solid #4361ee; color: #4361ee; }
        footer { background: #0b1b32; color: white; padding: 30px 0; margin-top: 50px; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <!-- GrantGates Brand Logo -->
                <a href="/grantgates/index.php" class="grantgates-brand">
                    <div class="brand-line1">
                        <span class="grant">Grant</span><span class="gates">Gates</span>
                    </div>
                    <div class="brand-line2">OPENING DOORS TO YOUR FUTURE</div>
                    <div class="brand-line3">www.grantgates.com</div>
                </a>
                
                <ul class="nav-menu">
                    <li><a href="/grantgates/index.php">Home</a></li>
                    <li><a href="/grantgates/scholarships.php">Scholarships</a></li>
                    <li><a href="/grantgates/about.php">About</a></li>
                    <li><a href="/grantgates/contact.php">Contact</a></li>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['role'] == 'admin'): ?>
                            <li><a href="/grantgates/admin/dashboard.php">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="/grantgates/student/dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                        <li><a href="/grantgates/logout.php" class="btn btn-outline">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/grantgates/login.php" class="btn btn-outline">Login</a></li>
                        <li><a href="/grantgates/register.php" class="btn btn-primary">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>