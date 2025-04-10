<?php
session_start();
include 'config.php'; 

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

$sql = "INSERT INTO users (name, email, password, gender, age, weight, height, diet, food_exclusions, activity)
        VALUES ('{$name_sql}', '{$email_sql}', '{$hashed_password_sql}', '{$gender_sql}', 
                {$age_sql}, {$weight_sql}, {$height_sql}, 
                '{$diet_sql}', '{$food_exclusions_sql}', '{$activity_sql}')";

if ($conn->query($sql) === TRUE) {
    $message = "Registration Completed! <a href='login.php'>Login Here</a>";

    session_destroy(); 
} else {
    $message = "Error: " . $conn->error; 
    if ($conn->errno == 1062) {
         $message = "Error: This email address is already registered. Please <a href='login.php'>login</a> or use a different email.";
    }
}
$conn->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Confirmation</title>
</head>
<body>

<h2>Registration Status</h2>

<?php 
if (!empty($message)) {
    echo "<p>" . $message . "</p>";
} else {
    echo "<p>An unexpected error occurred processing your registration.</p>"; 
}
?>

</body>
</html>
