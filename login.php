<?php
session_start();
include 'config.php'; 

$message = ''; 
$submitted_email = ''; 

if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $submitted_email = $email; 

    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {
        
        $email_sql = $conn->real_escape_string($email);
        $sql = "SELECT id, name, password FROM users WHERE email = '{$email_sql}'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['user_name'] = $user['name']; 
                header("Location: index.php"); 
                exit();
            } else {
                $message = "Invalid email or password."; 
            }
        } else {
            $message = "Invalid email or password."; 
        }
         if($result) $result->free(); // Free result memory
         // Note: Prepared statement code removed as requested
    }
    // $conn->close(); // Optional: Close connection if script ends here
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitBite</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Pacifico&display=swap" rel="stylesheet">
</head>
<body>

<div class="form-container login-container"> 
    
    <h2>Welcome Back!</h2>
    <p class="step-description">Login to access your meal plans.</p>

    <?php 
    if (!empty($message)) {
        echo '<p class="message error">' . htmlspecialchars($message) . '</p>';
    }
    ?>

    <form class="login-form" method="POST" action="">
        
        <div class="form-group"> 
            <label for="email"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo htmlspecialchars($submitted_email); ?>">
        </div>
        
        <div class="form-group password-wrapper">
            <label for="password"><i class="fas fa-lock"></i> Password</label>
            <input type="password" id="password" name="password" required>
            <button type="button" id="togglePassword" class="password-toggle-btn">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <div class="form-options" style="text-align: right; margin-bottom: 15px;">
            <a href="forgot_password.php" class="forgot-password-link">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-full-width">Login</button> 
    </form> 

    <div class="form-footer-link">
        <p>Don't have an account? <a href="register.php">Sign up free!</a></p>
    </div>

</div> 

<script src="script.js"></script> 

</body>
</html>