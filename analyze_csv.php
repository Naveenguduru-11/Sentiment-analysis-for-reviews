<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_path = $_POST['file_path'];

    if (!file_exists($file_path)) {
        die("Error: File not found!");
    } elseif (!is_readable($file_path)) {
        die("Error: File is not readable!");
    }

    $escaped_path = escapeshellarg($file_path);

    // Run the Python script and capture stderr
    $command = "python analyze_csv.py " . $escaped_path . " 2>&1";
    $output = shell_exec($command);

    echo "<h1><a href='account.php' style='text-decoration: none; color: inherit; font-size: 24px; margin-right: 10px;'><span style='font-size: 36px; color: #007bff; cursor: pointer; border: 2px solid red; padding: 5px 10px; border-radius: 5px;'>&#8592;</span></a> Analysis Results</h1>";

    if ($output === null) {
        echo "<pre>Error: Failed to execute Python script.</pre>";
    } else {
        $data = json_decode($output, true); // Decode JSON output from Python

        if (isset($data['success']) && $data['success']) {
            // Extract sentiment counts
            $sentiment_counts = $data['analysis']['sentiment_counts'] ?? [];

            // Display histogram
            if (!empty($sentiment_counts)) {
                echo "<h2>Sentiment Distribution</h2>";
                echo '
                <canvas id="sentimentChart" width="150" height="50"></canvas>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const ctx = document.getElementById("sentimentChart").getContext("2d");
                    const sentimentChart = new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: ["Positive", "Negative", "Neutral"],
                            datasets: [{
                                label: "Number of Reviews",
                                data: [' . $sentiment_counts['Positive'] . ', ' . $sentiment_counts['Negative'] . ', ' . $sentiment_counts['Neutral'] . '],
                                backgroundColor: ["#4caf50", "#f44336", "#2196f3"]
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                </script>';

                // Add "More Detailed View" button
                echo '<button id="toggleDetails" onclick="toggleDetails()">More Detailed View</button>';
                // Hidden detailed view
                echo '<div id="detailedView" style="display: none;">';

                // Sentiment counts
                echo "<h2>Sentiment Counts</h2>";
                echo "<ul>";
                foreach ($sentiment_counts as $sentiment => $count) {
                    echo "<li><strong>" . htmlspecialchars($sentiment, ENT_QUOTES, 'UTF-8') . ":</strong> " . htmlspecialchars($count, ENT_QUOTES, 'UTF-8') . "</li>";
                }
                echo "</ul>";

                // Display positive reviews
                echo "<h2>Positive Reviews</h2>";
                echo "<ul>";
                foreach ($data['analysis']['positive_reviews'] as $review) {
                    echo "<li>" . htmlspecialchars($review, ENT_QUOTES, 'UTF-8') . "</li>";
                }
                echo "</ul>";

                // Display negative reviews
                echo "<h2>Negative Reviews</h2>";
                echo "<ul>";
                foreach ($data['analysis']['negative_reviews'] as $review) {
                    echo "<li>" . htmlspecialchars($review, ENT_QUOTES, 'UTF-8') . "</li>";
                }
                echo "</ul>";

                // Display neutral reviews
                echo "<h2>Neutral Reviews</h2>";
                echo "<ul>";
                foreach ($data['analysis']['neutral_reviews'] as $review) {
                    echo "<li>" . htmlspecialchars($review, ENT_QUOTES, 'UTF-8') . "</li>";
                }
                echo "</ul>";

                echo '</div>';

                // JavaScript to toggle detailed view
                echo '
                <script>
                    function toggleDetails() {
                        const detailedView = document.getElementById("detailedView");
                        const button = document.getElementById("toggleDetails");
                        if (detailedView.style.display === "none") {
                            detailedView.style.display = "block";
                            button.textContent = "Hide Detailed View";
                        } else {
                            detailedView.style.display = "none";
                            button.textContent = "More Detailed View";
                        }
                    }
                </script>';
            } else {
                echo "<p>Error: Sentiment counts are missing from the response.</p>";
            }
        } else {
            echo "<pre>Error: " . htmlspecialchars($data['error'] ?? 'Unknown error') . "</pre>";
        }
    }
} else {
    echo "Invalid request!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="analyze_csv.css">
</head>
<body>
</body>
</html>
