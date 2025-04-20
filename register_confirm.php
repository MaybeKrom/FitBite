<?php
session_start();
include 'config.php'; 

if (!isset($conn) || $conn->connect_error) {
    die("Database configuration error. Cannot complete registration."); 
}

$required_keys = [
    'register_step1_name',
    'register_step1_email',
    'register_step1_hashed_password',
    'register_step2_diet',
    'register_step3_exclusions',
    'register_step4_gender',
    'register_step4_height',
    'register_step4_weight',
    'register_step4_age',
    'register_step4_activity'
];

$all_keys_present = true;
foreach ($required_keys as $key) {
    if (!isset($_SESSION[$key])) {
        $all_keys_present = false;
        break; 
    }
}

if (!$all_keys_present) {
    header("Location: register.php");
    exit();
}

$name = $_SESSION['register_step1_name'];
$email = $_SESSION['register_step1_email'];
$hashed_password = $_SESSION['register_step1_hashed_password']; 
$diet = $_SESSION['register_step2_diet'];
$food_exclusions = $_SESSION['register_step3_exclusions']; 
$gender = $_SESSION['register_step4_gender'];
$height = $_SESSION['register_step4_height']; 
$weight = $_SESSION['register_step4_weight']; 
$age = $_SESSION['register_step4_age'];       
$activity = $_SESSION['register_step4_activity'];

$name_sql = $conn->real_escape_string($name);
$email_sql = $conn->real_escape_string($email);
$hashed_password_sql = $conn->real_escape_string($hashed_password); 
$diet_sql = $conn->real_escape_string($diet);
$food_exclusions_sql = $conn->real_escape_string($food_exclusions);
$gender_sql = $conn->real_escape_string($gender);
$activity_sql = $conn->real_escape_string($activity);
$age_sql = (int)$age;
$height_sql = (float)$height;
$weight_sql = (float)$weight;

$message = ''; 
$success = false; 

$sql = "INSERT INTO users (name, email, password, gender, age, weight, height, diet, food_exclusions, activity)
        VALUES ('{$name_sql}', '{$email_sql}', '{$hashed_password_sql}', '{$gender_sql}', 
                {$age_sql}, {$weight_sql}, {$height_sql}, 
                '{$diet_sql}', '{$food_exclusions_sql}', '{$activity_sql}')";

if ($conn->query($sql) === TRUE) {
    $message = "Registration Completed! You can now login."; 
    $success = true;
    session_destroy(); 
} else {
    if ($conn->errno == 1062) { 
         $message = "Error: This email address is already registered. Please use a different email or login.";
    } else {
         $message = "Error: Could not complete registration due to a database issue (" . $conn->errno.")"; // Added error number hint
    }
    $success = false;
}

$conn->close(); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmation</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container confirmation-container"> 

    <h2>Registration Status</h2>

    <?php 
    if (!empty($message)) {
        $message_class = $success ? 'success' : 'error'; 
        echo '<p class="message ' . $message_class . '">' . htmlspecialchars($message) . '</p>'; 

        if ($success) {
            echo '<div class="form-footer-link"><a href="login.php" class="btn btn-primary">Login Here</a></div>'; // Styled button
        } else {
             echo '<div class="form-footer-link"><a href="register.php">Try Registration Again</a></div>';
        }

    } else {
        echo '<p class="message error">An unexpected error occurred.</p>'; 
    }
    ?>

</div> 

</body>
</html>