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
$message = ''; 

$submitted_gender = $_POST['gender'] ?? null;
$submitted_height = $_POST['height'] ?? '';
$submitted_weight = $_POST['weight'] ?? '';
$submitted_age = $_POST['age'] ?? '';
$submitted_activity = $_POST['activity'] ?? null;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $gender = $submitted_gender; 
    $height = $submitted_height;
    $weight = $submitted_weight;
    $age = $submitted_age;
    $activity = $submitted_activity;

    if (!isset($gender) || !in_array($gender, $allowed_genders)) {
        $message = "Please select a valid gender.";
    } elseif (empty($height) || !is_numeric($height) || $height <= 0 || $height > 250) { 
        $message = "Please enter a valid positive height (cm, max 250).";
    } elseif (empty($weight) || !is_numeric($weight) || $weight <= 0 || $weight > 500) { 
        $message = "Please enter a valid positive weight (kg, max 500).";
    } elseif (empty($age) || !is_numeric($age) || $age <= 10 || $age > 120) { 
        $message = "Please enter a valid age (e.g., 10-120).";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 4: About You</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container form-container body-step-container"> 

    <h2>Step 4: Tell Us About Yourself</h2>
    <p class="step-description">This helps us tailor recommendations later!</p>

    <?php 
    if (!empty($message)) {
        echo '<p class="message error">' . htmlspecialchars($message) . '</p>'; 
    }
    ?>

    <form class="register-form" method="POST" action=""> 
        
        <div class="form-group">
            <label for="gender"><i class="fas fa-venus-mars"></i> Gender:</label> 
            <select id="gender" name="gender" required> 
                <option value="" disabled <?php echo ($submitted_gender === null) ? 'selected' : ''; ?>>-- Select --</option> 
                <option value="male" <?php echo ($submitted_gender === 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($submitted_gender === 'female') ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="height"><i class="fas fa-ruler-vertical"></i> Height (cm):</label> 
            <input type="number" id="height" name="height" placeholder="e.g., 175" required min="50" max="250" 
                   value="<?php echo htmlspecialchars($submitted_height); ?>">
        </div>
        
        <div class="form-group">
            <label for="weight"><i class="fas fa-weight-hanging"></i> Weight (kg):</label> 
            <input type="number" id="weight" name="weight" placeholder="e.g., 70" required min="20" max="500" step="0.1" 
                   value="<?php echo htmlspecialchars($submitted_weight); ?>">
        </div>
        
        <div class="form-group">
            <label for="age"><i class="fas fa-birthday-cake"></i> Age:</label> 
            <input type="number" id="age" name="age" placeholder="e.g., 30" required min="10" max="120" 
                   value="<?php echo htmlspecialchars($submitted_age); ?>">
        </div>

        <div class="form-group">
            <label for="activity"><i class="fas fa-running"></i> Activity Level:</label> 
            <select id="activity" name="activity" required> 
                 <option value="" disabled <?php echo ($submitted_activity === null) ? 'selected' : ''; ?>>-- Select --</option> 
                <option value="low" <?php echo ($submitted_activity === 'low') ? 'selected' : ''; ?>>Low (Sedentary, little/no exercise)</option>
                <option value="medium" <?php echo ($submitted_activity === 'medium') ? 'selected' : ''; ?>>Medium (Light exercise 1-3 days/wk)</option>
                <option value="high" <?php echo ($submitted_activity === 'high') ? 'selected' : ''; ?>>High (Moderate/Heavy exercise 3-7 days/wk)</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-full-width">Next: Confirm Details <i class="fas fa-arrow-right"></i></button> 
    </form>

    <div class="form-footer-link">
        <a href="register_exclusions.php">Back to Step 3</a> 
    </div>

</div> 

</body>
</html>