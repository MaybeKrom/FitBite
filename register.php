<?php
session_start();
include 'config.php'; 

$message = ''; 
$submitted_name = ''; 
$submitted_email = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = $_POST['name'] ?? ''; 
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $submitted_name = $name;
    $submitted_email = $email;

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "Please fill in all fields.";
    } 
    elseif ($password !== $confirm_password) { 
        $message = "Passwords do not match.";
    } 
    else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $_SESSION['register_step1_name'] = $name;
        $_SESSION['register_step1_email'] = $email;
        $_SESSION['register_step1_hashed_password'] = $hashed_password; 

        header("Location: register_diet.php");
        exit(); 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Register - Create Your FitBite Account</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="form-container register-container"> 
    
    <h2>Step 1: Create Your Account</h2>
     <p class="step-description">Start your personalized meal planning journey.</p>


    <?php 
    if (!empty($message)) {
        echo '<p class="message error">' . htmlspecialchars($message) . '</p>'; 
    }
    ?>

    <form class="register-form" method="POST" action=""> 
        
        <div class="form-group">
             <label for="name"><i class="fas fa-user"></i> Full Name:</label>
             <input type="text" id="name" name="name" required 
                   value="<?php echo htmlspecialchars($submitted_name); ?>">
        </div>
        
        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($submitted_email); ?>">
        </div>
        
        <div class="form-group password-wrapper">
            <label for="password"><i class="fas fa-lock"></i> Password:</label>
            <input type="password" id="password" name="password" required>
             <button type="button" id="togglePassword" class="password-toggle-btn">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        
        <div class="form-group password-wrapper">
             <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password:</label>
             <input type="password" id="confirm_password" name="confirm_password" required>
             <button type="button" id="toggleConfirmPassword" class="password-toggle-btn">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        
        <button type="submit" class="btn btn-primary btn-full-width">Next Step <i class="fas fa-arrow-right"></i></button> 
    
    </form> 

    <div class="form-footer-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

</div> 

<script src="script.js"></script> 

</body>
</html>