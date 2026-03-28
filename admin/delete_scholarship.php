<?php
// admin/delete_scholarship.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$scholarship_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($scholarship_id <= 0) {
    $_SESSION['success'] = 'Invalid scholarship ID.';
    redirect('admin/manage_scholarships.php');
}

// Check if scholarship exists
$check_query = "
    SELECT scholarship_id, title
    FROM scholarships
    WHERE scholarship_id = $scholarship_id
    LIMIT 1
";

$check_result = mysqli_query($conn, $check_query);

if (!$check_result || mysqli_num_rows($check_result) === 0) {
    $_SESSION['success'] = 'Scholarship not found.';
    redirect('admin/manage_scholarships.php');
}

$scholarship = mysqli_fetch_assoc($check_result);

// Check if there are related applications
$applications_query = "
    SELECT COUNT(*) AS total_applications
    FROM applications
    WHERE scholarship_id = $scholarship_id
";

$applications_result = mysqli_query($conn, $applications_query);

$total_applications = 0;

if ($applications_result && mysqli_num_rows($applications_result) > 0) {
    $applications_row = mysqli_fetch_assoc($applications_result);
    $total_applications = (int) ($applications_row['total_applications'] ?? 0);
}

// Prevent deletion if applications exist
if ($total_applications > 0) {
    $_SESSION['success'] = 'Cannot delete scholarship "' . $scholarship['title'] . '" because it already has applications.';
    redirect('admin/manage_scholarships.php');
}

// Delete scholarship
$delete_query = "
    DELETE FROM scholarships
    WHERE scholarship_id = $scholarship_id
    LIMIT 1
";

if (mysqli_query($conn, $delete_query)) {
    $_SESSION['success'] = 'Scholarship "' . $scholarship['title'] . '" deleted successfully.';
} else {
    $_SESSION['success'] = 'Failed to delete scholarship: ' . mysqli_error($conn);
}

redirect('admin/manage_scholarships.php');
?>