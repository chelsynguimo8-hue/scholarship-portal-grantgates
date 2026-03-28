<?php
// scholarships.php
require_once 'includes/config.php';

$page_title = 'Scholarships';
include 'includes/header.php';

// Fetch scholarships from DB
$query = "
    SELECT *
    FROM scholarships
    WHERE status = 'active'
    ORDER BY created_at DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}
?>

    <section class="scholarships-section">
        <div class="container">
            <div class="section-title">
                <h2>Available Scholarships</h2>
                <p>Find the perfect scholarship for your educational journey</p>
            </div>

            <div class="scholarship-grid">

                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php while ($scholarship = mysqli_fetch_assoc($result)): ?>

                        <div class="scholarship-card">
                            <div class="scholarship-header">
                                <h3><?php echo htmlspecialchars($scholarship['title']); ?></h3>

                                <div class="scholarship-meta">
                                <span>
                                    📅 Deadline:
                                    <?php
                                    echo !empty($scholarship['deadline'])
                                        ? date('M d, Y', strtotime($scholarship['deadline']))
                                        : 'N/A';
                                    ?>
                                </span>
                                </div>
                            </div>

                            <div class="scholarship-body">
                                <p class="scholarship-description">
                                    <?php echo htmlspecialchars(substr($scholarship['description'], 0, 120)); ?>...
                                </p>

                                <div class="scholarship-details">
                                    <div class="detail-item">
                                        💰 Amount:
                                        <?php
                                        echo $scholarship['amount'] !== null
                                            ? number_format($scholarship['amount'], 0, '.', ',') . ' FCFA'
                                            : 'N/A';
                                        ?>
                                    </div>
                                </div>

                                <div class="scholarship-amount">
                                    <?php
                                    echo $scholarship['amount'] !== null
                                        ? number_format($scholarship['amount'], 0, '.', ',') . ' FCFA'
                                        : 'N/A';
                                    ?>
                                </div>
                            </div>

                            <div class="scholarship-footer">
                                <?php if (isLoggedIn()): ?>
                                    <a href="student/apply.php?id=<?php echo (int)$scholarship['scholarship_id']; ?>" class="btn btn-primary">
                                        Apply Now
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary">
                                        Login to Apply
                                    </a>
                                <?php endif; ?>

                                <a href="#" class="btn btn-outline">Details</a>
                            </div>
                        </div>

                    <?php endwhile; ?>

                <?php else: ?>
                    <p style="color: var(--gray-600);">
                        No scholarships available at the moment.
                    </p>
                <?php endif; ?>

            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>