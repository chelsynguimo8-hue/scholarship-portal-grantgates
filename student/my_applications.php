<?php
// student/my_applications.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$page_title = 'My Applications';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Get all applications with scholarship details
$query = "SELECT a.*, s.title as scholarship_title, s.amount, s.deadline 
          FROM applications a 
          JOIN scholarships s ON a.scholarship_id = s.scholarship_id 
          WHERE a.user_id = $user_id 
          ORDER BY a.application_date DESC";
$result = mysqli_query($conn, $query);
?>

<div class="applications-container">
    <h1>My Scholarship Applications</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Scholarship</th>
                        <th>Amount</th>
                        <th>Application Date</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Documents</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($app = mysqli_fetch_assoc($result)): 
                        // Get documents for this application
                        $doc_query = "SELECT * FROM documents WHERE application_id = " . $app['application_id'];
                        $doc_result = mysqli_query($conn, $doc_query);
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['scholarship_title']); ?></td>
                            <td><?php echo number_format($app['amount'], 0, '.', ','); ?> FCFA</td>
                            <td><?php echo date('d M Y', strtotime($app['application_date'])); ?></td>
                            <td><?php echo date('d M Y', strtotime($app['deadline'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $app['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (mysqli_num_rows($doc_result) > 0): ?>
                                    <span class="badge badge-success"><?php echo mysqli_num_rows($doc_result); ?> files</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">No files</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="view_application.php?id=<?php echo $app['application_id']; ?>" class="btn btn-primary btn-sm">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>You haven't applied for any scholarships yet.</p>
            <a href="apply.php" class="btn btn-success">Browse Scholarships</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>