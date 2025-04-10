<?php
session_start();

if (!isset($_SESSION['register_step1_email'])) { 
    header("Location: register.php");
    exit();
}

$allowed_diets = ['anything', 'keto', 'paleo', 'vegan', 'vegetarian']; 

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['diet']) && in_array($_POST['diet'], $allowed_diets)) {
        
        $_SESSION['register_step2_diet'] = $_POST['diet'];
        
        header("Location: register_exclusions.php"); 
        exit(); 

    } else {
        $message = "Please select a valid diet option.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Step 2: Diet Choice</title>
</head>
<body>

<h2>Step 2: Choose Your Diet</h2>

<form method="POST" action=""> 

    <p>Please select your dietary preference:</p> 
    
    <label><input type="radio" name="diet" value="anything" required> Anything (No restrictions)</label><br>
    <label><input type="radio" name="diet" value="keto"> Keto</label><br>
    <label><input type="radio" name="diet" value="paleo"> Paleo</label><br>
    <label><input type="radio" name="diet" value="vegan"> Vegan</label><br>
    <label><input type="radio" name="diet" value="vegetarian"> Vegetarian</label><br>
    <br> 
    
    <button type="submit">Next</button>
</form>

</body>
</html>