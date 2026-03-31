<?php
// includes/auth.php
require_once 'config.php';

function syncSessionUser(array $user)
{
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    $_SESSION['profile_picture_path'] = $user['profile_picture_path'] ?? null;
}

// Register new user
function registerUser($data) {
    global $conn;

    $username = mysqli_real_escape_string($conn, trim($data['username'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($data['email'] ?? ''));
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $first_name = mysqli_real_escape_string($conn, trim($data['first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($data['last_name'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($data['phone'] ?? ''));
    $institution = mysqli_real_escape_string($conn, trim($data['institution'] ?? ''));
    $program = mysqli_real_escape_string($conn, trim($data['program'] ?? ''));
    $year_of_study = mysqli_real_escape_string($conn, trim($data['year_of_study'] ?? '1'));

    if ($username === '' || $email === '' || $first_name === '' || $last_name === '') {
        return ['success' => false, 'message' => 'Please fill in all required fields'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Please enter a valid email address'];
    }

    $allowed_years = ['1', '2', '3', '4', '5'];
    if (!in_array($year_of_study, $allowed_years, true)) {
        $year_of_study = '1';
    }

    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email' OR username = '$username'");
    if (!$check) {
        return ['success' => false, 'message' => 'Unable to validate account details'];
    }
    if(mysqli_num_rows($check) > 0) {
        return ['success' => false, 'message' => 'Email or username already registered'];
    }

    $phone_sql = $phone === '' ? 'NULL' : "'$phone'";
    $institution_sql = $institution === '' ? 'NULL' : "'$institution'";
    $program_sql = $program === '' ? 'NULL' : "'$program'";

    $query = "INSERT INTO users (
                username, email, password_hash, first_name, last_name, phone, institution, program, year_of_study, role
              ) VALUES (
                '$username', '$email', '$password', '$first_name', '$last_name', $phone_sql, $institution_sql, $program_sql, '$year_of_study', 'student'
              )";

    if(mysqli_query($conn, $query)) {
        return ['success' => true, 'message' => 'Registration successful!'];
    } else {
        return ['success' => false, 'message' => 'Registration failed: ' . mysqli_error($conn)];
    }
}

// Login user
function loginUser($email, $password) {
    global $conn;
    
    $email = mysqli_real_escape_string($conn, $email);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['password_hash'])) {
            syncSessionUser($user);
            return ['success' => true, 'role' => $user['role']];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid email or password'];
}

function refreshCurrentUserSession()
{
    global $conn;

    $user_id = (int) ($_SESSION['user_id'] ?? 0);
    if ($user_id <= 0) {
        return;
    }

    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id LIMIT 1");
    if ($result && mysqli_num_rows($result) === 1) {
        syncSessionUser(mysqli_fetch_assoc($result));
    }
}

// Require login
function requireLogin() {
    if(!isLoggedIn()) {
        redirect('login.php');
    }
}

// Require admin
function requireAdmin() {
    requireLogin();
    if(!isAdmin()) {
        redirect('index.php');
    }
}
?>
