<?php
// includes/auth.php
require_once 'config.php';

// Register new user
function registerUser($data) {
    global $conn;
    
    $username = mysqli_real_escape_string($conn, $data['username']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $first_name = mysqli_real_escape_string($conn, $data['first_name']);
    $last_name = mysqli_real_escape_string($conn, $data['last_name']);
    
    // Check if email exists
    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email'");
    if(mysqli_num_rows($check) > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    $query = "INSERT INTO users (username, email, password_hash, first_name, last_name, role) 
              VALUES ('$username', '$email', '$password', '$first_name', '$last_name', 'student')";
    
    if(mysqli_query($conn, $query)) {
        return ['success' => true, 'message' => 'Registration successful!'];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
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
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            return ['success' => true, 'role' => $user['role']];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid email or password'];
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