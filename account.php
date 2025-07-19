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
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $target_dir = "uploads/"; // Directory where files will be saved
    $target_file = $target_dir . basename($_FILES["csv_file"]["name"]);

    // Ensure the uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Allow only .csv files
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($file_type !== 'csv') {
        $error_message = "Only CSV files are allowed!";
    } else {
        // Move uploaded file to the server
        if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $target_file)) {
            $uploaded_file_path = $target_file; // Save the file path for later processing
            $success_message = "File uploaded successfully: " . htmlspecialchars(basename($target_file));
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    }

}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href = "account.css">
</head>
<body>
    <h1 style="color:red;">Login Success!</h1>
    <p>Username: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <hr>

    <!-- Upload File Form -->
    <form method="POST" enctype="multipart/form-data">
        <label for="csv_file">Upload CSV File:</label>
        <input type="file" id="csv_file" name="csv_file" required>
        <br><br>
        <button type="submit" name="upload">Upload</button>
        <br>
        <button type="button" style="background:rgb(219, 12, 12);" onclick="window.location.href='logout.php'">Logout</button>
    </form>

    <?php
    if (isset($success_message)) {
        echo "<p style='color:green;'>$success_message</p>";
        echo "<p>Uploaded File: <a href='" . htmlspecialchars($uploaded_file_path) . "'>" . htmlspecialchars($uploaded_file_path) . "</a></p>";
    }
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>

    <!-- Go Button to Analyze File -->
    <?php if (isset($uploaded_file_path)): ?>
    <form method="POST" action="analyze_csv.php">
        <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($uploaded_file_path); ?>">
        <button type="submit" name="analyze">Go</button>
    </form>
    <?php endif; ?>
</body>
</html>
