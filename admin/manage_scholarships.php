<?php
// admin/manage_scholarships.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$page_title = 'Manage Scholarships';
include '../includes/header.php';

// Get all scholarships
$query = "
    SELECT *
    FROM scholarships
    ORDER BY created_at DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}
?>

    <div class="container" style="padding: 40px 0;">
        <h1 style="margin-bottom: 1.5rem;">Manage Scholarships</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                <?php
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="action-bar">
            <a href="add_scholarship.php" class="btn btn-primary">+ Add New Scholarship</a>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="card">
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($scholarship = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo (int) $scholarship['scholarship_id']; ?></td>

                                <td><?php echo htmlspecialchars($scholarship['title']); ?></td>

                                <td>
                                    <?php
                                    echo $scholarship['amount'] !== null
                                        ? number_format((float) $scholarship['amount'], 0, '.', ',') . ' FCFA'
                                        : 'N/A';
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo !empty($scholarship['deadline'])
                                        ? htmlspecialchars(date('d M Y', strtotime($scholarship['deadline'])))
                                        : 'N/A';
                                    ?>
                                </td>

                                <td>
                                    <span class="badge badge-<?php echo htmlspecialchars($scholarship['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($scholarship['status'])); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php
                                    echo !empty($scholarship['created_at'])
                                        ? htmlspecialchars(date('d M Y', strtotime($scholarship['created_at'])))
                                        : 'N/A';
                                    ?>
                                </td>

                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a href="edit_scholarship.php?id=<?php echo (int) $scholarship['scholarship_id']; ?>" class="btn btn-primary btn-sm">
                                            Edit
                                        </a>

                                        <a href="delete_scholarship.php?id=<?php echo (int) $scholarship['scholarship_id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this scholarship?');">
                                            Delete
                                        </a>

                                        <a href="view_applications.php?scholarship_id=<?php echo (int) $scholarship['scholarship_id']; ?>"
                                           class="btn btn-info btn-sm">
                                            View Applications
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p>No scholarships found.</p>
                <a href="add_scholarship.php" class="btn btn-primary">Add Your First Scholarship</a>
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

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.875rem;
        }
    </style>

<?php include '../includes/footer.php'; ?>