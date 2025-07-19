<?php
$registration_successful = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection
    $host = 'localhost';
    $db = 'user_database';
    $user = 'admin_user';
    $pass = 'here'; // Your MySQL password
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password === $confirm_password) {
        // Hash the password for security
        $hashed_password = hash('sha256', $password);

        // Insert into database
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $registration_successful = true;
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Passwords do not match.</p>";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right,rgb(0, 0, 0),rgb(0, 0, 0));
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container, .success-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 1em;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        button, a {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            background:rgb(200, 224, 69);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            display: inline-block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
        }

        button:hover, a:hover {
            background:rgb(165, 164, 165);
        }

        .success-container {
            text-align: center;
        }

        .success-icon {
            font-size: 4em;
            color:rgb(28, 223, 244);
        }

        .success-message {
            font-size: 1.2em;
            color: #333;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php if ($registration_successful): ?>
        <div class="success-container">
            <div class="success-icon">✔</div>
            <p class="success-message">Registration Successful!</p>
            <a href="index.php">Back to Login</a>
        </div>
    <?php else: ?>
        <div class="form-container">
            <h1>Register</h1>
            <form method="POST" action="validate.php">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button type="submit">Register</button>
                <a href="index.php">Back to Login</a>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
