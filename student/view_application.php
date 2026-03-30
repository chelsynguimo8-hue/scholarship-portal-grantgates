<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

$application_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if ($application_id <= 0) {
    die('Invalid application ID.');
}

$application_sql = "
    SELECT
        a.*,
        s.title AS scholarship_title,
        s.description,
        s.eligibility,
        s.amount,
        s.deadline
    FROM applications a
    INNER JOIN scholarships s ON a.scholarship_id = s.scholarship_id
    WHERE a.application_id = $application_id
      AND a.user_id = $user_id
    LIMIT 1
";

$application_result = mysqli_query($conn, $application_sql);
if (!$application_result || mysqli_num_rows($application_result) === 0) {
    die('Application not found.');
}

$application = mysqli_fetch_assoc($application_result);
$documents = [];
$documents_result = mysqli_query($conn, "
    SELECT file_name, file_path, file_type, upload_date
    FROM documents
    WHERE application_id = $application_id
    ORDER BY upload_date DESC
");

if ($documents_result) {
    while ($row = mysqli_fetch_assoc($documents_result)) {
        $documents[] = $row;
    }
}

$page_title = 'Application Details';
include '../includes/header.php';
?>

<div class="container" style="padding: 40px 0; max-width: 1000px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
        <div>
            <h1 style="margin-bottom: 0.5rem;">Application Details</h1>
            <p style="color: var(--gray-600); margin: 0;">Application #<?php echo str_pad((string) $application['application_id'], 3, '0', STR_PAD_LEFT); ?></p>
        </div>
        <a href="my_applications.php" class="btn btn-outline">Back to My Applications</a>
    </div>

    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem;">Summary</h3>
        <div class="table-container">
            <table>
                <tbody>
                    <tr><th>Scholarship</th><td><?php echo htmlspecialchars($application['scholarship_title']); ?></td></tr>
                    <tr><th>Status</th><td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $application['status']))); ?></td></tr>
                    <tr><th>Applied On</th><td><?php echo htmlspecialchars(date('d M Y H:i', strtotime($application['application_date']))); ?></td></tr>
                    <tr><th>Deadline</th><td><?php echo htmlspecialchars(date('d M Y', strtotime($application['deadline']))); ?></td></tr>
                    <tr><th>Amount</th><td><?php echo $application['amount'] !== null ? htmlspecialchars(number_format((float) $application['amount'], 0, '.', ',') . ' FCFA') : 'N/A'; ?></td></tr>
                    <tr><th>Remarks</th><td><?php echo nl2br(htmlspecialchars($application['remarks'] ?? 'No remarks submitted.')); ?></td></tr>
                    <tr><th>Reviewed Date</th><td><?php echo !empty($application['reviewed_date']) ? htmlspecialchars(date('d M Y H:i', strtotime($application['reviewed_date']))) : 'Not reviewed yet'; ?></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem;">Documents</h3>
        <?php if (count($documents) > 0): ?>
            <ul style="margin: 0; padding-left: 1rem;">
                <?php foreach ($documents as $document): ?>
                    <li style="margin-bottom: 0.75rem;">
                        <a href="../<?php echo htmlspecialchars($document['file_path']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($document['file_name']); ?>
                        </a>
                        <span style="color: var(--gray-600);">
                            (<?php echo htmlspecialchars($document['file_type']); ?>, <?php echo htmlspecialchars(date('d M Y H:i', strtotime($document['upload_date']))); ?>)
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="margin: 0; color: var(--gray-600);">No uploaded documents found.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 1rem;">Scholarship Details</h3>
        <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($application['description'])); ?></p>
        <p style="margin-bottom: 0;"><strong>Eligibility:</strong><br><?php echo nl2br(htmlspecialchars($application['eligibility'])); ?></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
