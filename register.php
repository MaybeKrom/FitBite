<?php
session_start();
include 'config.php'; 

$message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = $_POST['name'] ;
    $email = $_POST['email'] ;
    $password = $_POST['password'] ;
    $confirm_password = $_POST['confirm_password'] ;

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
<html>
<head> 
    <title>Register - Step 1</title>
</head>
<body>

<h2>Step 1: Create Your Account</h2>

<?php 
if (!empty($message)) {
    echo '<p class="message">' . htmlspecialchars($message) . '</p>';
}
?>

<form method="POST" action=""> 
    
    <label for="name">Full Name:</label><br>
    <input type="text" id="name" name="name" placeholder="Full Name" required><br><br>
    
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" placeholder="Email" required><br><br>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="Password" required>
    
    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter Password" required><br><br>
    
    <button type="submit">Next</button>
</form> 

</body>
</html>