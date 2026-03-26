<?php
// admin/add_scholarship.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$page_title = 'Add Scholarship';
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $eligibility = mysqli_real_escape_string($conn, $_POST['eligibility']);
    $amount = (float)$_POST['amount'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];
    
    $query = "INSERT INTO scholarships (title, description, eligibility, amount, deadline, status, created_by) 
              VALUES ('$title', '$description', '$eligibility', $amount, '$deadline', '$status', $created_by)";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Scholarship added successfully!';
        redirect('manage_scholarships.php');
    } else {
        $error = 'Failed to add scholarship: ' . mysqli_error($conn);
    }
}
?>

<div class="admin-form">
    <div class="card">
        <h1>Add New Scholarship</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" data-validate>
            <div class="form-group">
                <label for="title">Scholarship Title *</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="5" required></textarea>
                <small>Provide a detailed description of the scholarship</small>
            </div>
            
            <div class="form-group">
                <label for="eligibility">Eligibility Criteria *</label>
                <textarea id="eligibility" name="eligibility" rows="5" required></textarea>
                <small>List all requirements applicants must meet</small>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="amount">Amount (FCFA) *</label>
                    <input type="number" id="amount" name="amount" min="0" step="1000" required>
                </div>
                
                <div class="form-group">
                    <label for="deadline">Application Deadline *</label>
                    <input type="date" id="deadline" name="deadline" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active">Active</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Add Scholarship</button>
                <a href="manage_scholarships.php" class="btn btn-warning">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>