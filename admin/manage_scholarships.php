<?php
// admin/manage_scholarships.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$page_title = 'Manage Scholarships';
include '../includes/header.php';

// Get all scholarships
$query = "SELECT s.*, u.first_name, u.last_name 
          FROM scholarships s 
          LEFT JOIN users u ON s.created_by = u.user_id 
          ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="manage-scholarships">
    <h1>Manage Scholarships</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <div class="action-bar">
        <a href="add_scholarship.php" class="btn btn-success">Add New Scholarship</a>
    </div>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($scholarship = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $scholarship['scholarship_id']; ?></td>
                            <td><?php echo htmlspecialchars($scholarship['title']); ?></td>
                            <td><?php echo number_format($scholarship['amount'], 0, '.', ','); ?> FCFA</td>
                            <td><?php echo date('d M Y', strtotime($scholarship['deadline'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $scholarship['status']; ?>">
                                    <?php echo ucfirst($scholarship['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($scholarship['first_name'] . ' ' . $scholarship['last_name']); ?></td>
                            <td><?php echo date('d M Y', strtotime($scholarship['created_at'])); ?></td>
                            <td>
                                <a href="edit_scholarship.php?id=<?php echo $scholarship['scholarship_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="delete_scholarship.php?id=<?php echo $scholarship['scholarship_id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   data-confirm="Are you sure you want to delete this scholarship?">Delete</a>
                                <a href="view_applications.php?scholarship_id=<?php echo $scholarship['scholarship_id']; ?>" 
                                   class="btn btn-info btn-sm">View Applications</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>No scholarships found.</p>
            <a href="add_scholarship.php" class="btn btn-success">Add Your First Scholarship</a>
        </div>
    <?php endif; ?>
</div>

<style>
.action-bar {
    margin-bottom: 2rem;
}

.btn-info {
    background-color: #3498db;
    color: #fff;
}
</style>

<?php include '../includes/footer.php'; ?>