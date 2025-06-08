<?php
// Database setup script for NSCT project
// This script will create the database and tables needed for the project

// Include configuration
require_once "includes/config.php";

// Function to execute SQL from a file
function executeSQLFile($pdo, $file) {
    try {
        // Read the SQL file
        $sql = file_get_contents($file);
        
        // Execute multi-query SQL
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
        
        echo "<div style='color: green; margin: 10px 0;'>SQL file executed successfully!</div>";
        return true;
    } catch (PDOException $e) {
        echo "<div style='color: red; margin: 10px 0;'>Error executing SQL: " . $e->getMessage() . "</div>";
        return false;
    }
}

// HTML header
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>NSCT Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        h1 {
            color: #003366;
            text-align: center;
        }
        .container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .button {
            background-color: #003366;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #004080;
        }
    </style>
</head>
<body>
    <h1>NSCT Database Setup</h1>
    <div class='container'>";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    try {
        // Create a PDO connection without specifying the database
        $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        
        // Execute the SQL file
        if (executeSQLFile($pdo, "database_setup.sql")) {
            echo "<p class='success'>Database and tables created successfully!</p>";
            echo "<p>You can now <a href='index.php'>go to the homepage</a> and start using the application.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error'>Connection failed: " . $e->getMessage() . "</p>";
    }
} else {
    // Display setup form
    echo "<p>This script will set up the database and tables needed for the NSCT project.</p>
          <p>Make sure you have configured the database connection details in <code>includes/config.php</code>.</p>
          <p>Current database configuration:</p>
          <ul>
              <li>Host: " . DB_HOST . "</li>
              <li>Database Name: " . DB_NAME . "</li>
              <li>Username: " . DB_USER . "</li>
              <li>Password: " . (empty(DB_PASS) ? "(empty)" : "****") . "</li>
          </ul>
          <p>Click the button below to set up the database:</p>
          <form method='post'>
              <input type='submit' name='setup' value='Set Up Database' class='button'>
          </form>";
}

// HTML footer
echo "</div>
</body>
</html>";