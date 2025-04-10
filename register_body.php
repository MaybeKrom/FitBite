<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['register_step1_email']) || 
    !isset($_SESSION['register_step2_diet']) || 
    !isset($_SESSION['register_step3_exclusions'])) { 
    
    header("Location: register.php");
    exit();
}

$allowed_genders = ['male', 'female'];
$allowed_activities = ['low', 'medium', 'high'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $gender = $_POST['gender'] ?? null;
    $height = $_POST['height'] ?? ''; 
    $weight = $_POST['weight'] ?? '';
    $age = $_POST['age'] ?? '';
    $activity = $_POST['activity'] ?? null;


    if (!isset($gender) || !in_array($gender, $allowed_genders)) {
        $message = "Please select a valid gender.";
    } elseif (empty($height) || !is_numeric($height) || $height <= 0) {
        $message = "Please enter a valid positive height (cm).";
    } elseif (empty($weight) || !is_numeric($weight) || $weight <= 0) {
        $message = "Please enter a valid positive weight (kg).";
    } elseif (empty($age) || !is_numeric($age) || $age <= 0 || $age > 120) { 
        $message = "Please enter a valid age (e.g., 1-120).";
    } elseif (!isset($activity) || !in_array($activity, $allowed_activities)) {
        $message = "Please select a valid activity level.";
    } else {
        

        $_SESSION['register_step4_gender'] = $gender;
        $_SESSION['register_step4_height'] = (float)$height;
        $_SESSION['register_step4_weight'] = (float)$weight;
        $_SESSION['register_step4_age'] = (int)$age;
        $_SESSION['register_step4_activity'] = $activity;

        header("Location: register_confirm.php"); 
        exit(); 
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Step 4: About You</title>
</head>
<body>

<h2>Step 4: Tell Us About Yourself</h2>

<?php 
if (!empty($message)) {
    echo '<p class="message">' . htmlspecialchars($message) . '</p>';
}
?>

<form method="POST" action=""> 

    <label for="gender">Gender:</label>
    <select id="gender" name="gender">
        <option value="male">Male</option>
        <option value="female">Female</option>
    </select>
    
    <label for="height">Height (cm):</label>
    <input type="number" id="height" name="height" placeholder="e.g., 175" required><br><br>
    
    <label for="weight">Weight (kg):</label>
    <input type="number" id="weight" name="weight" placeholder="e.g., 70" required>
    
    <label for="age">Age:</label>
    <input type="number" id="age" name="age" placeholder="e.g., 30" required><br><br>

    <label for="activity">Activity Level:</label>
    <select id="activity" name="activity">
        <option value="low">Low (No exercise)</option>
        <option value="medium">Medium (Light exercise)</option>
        <option value="high">High (Active lifestyle)</option>
    </select>

    <button type="submit">Next</button>
</form>

</body>
</html>