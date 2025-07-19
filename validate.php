<?php
session_start();

// Database connection details
$host = 'localhost';
$db = 'user_database';
$user = 'root';
$pass = '1234'; // Your MySQL password
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Input validation
    if (empty($username) || empty($password)) {
        echo "Username and password cannot be empty.";
        echo '<a href="index.php">Back to Login</a>';
        exit();
    }

    // Hash the password for secure comparison
    $hashed_password = hash('sha256', data: $password);

    // Check if the username already exists
    $check_sql = "SELECT username FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "Error: The username is already taken.";
        echo '<a href="register.php">Back to Registration</a>';
    } else {
        // Register the new user
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            echo "Registration successful!";
            echo '<a href="index.php">Back to Login</a>';
        } else {
            echo "Error: Could not register the user. Please try again.";
        }

        $stmt->close();
    }

    $check_stmt->close();
}
$conn->close();
?>
