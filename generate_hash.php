<?php
// generate_hash.php - DELETE AFTER USE!
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password<br>";
echo "Hash: $hash<br>";
echo "<br>Copy this hash and update the admin user in database:";
echo "<br><strong>$hash</strong>";
?>