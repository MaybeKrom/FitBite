<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['register_step1_email']) || !isset($_SESSION['register_step2_diet'])) { 

    header("Location: register.php");
    exit();
}

$allowed_exclusions = ['gluten', 'peanuts', 'eggs', 'fish', 'dairy', 'soy', 'shellfish'];

$exclusions_string = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $submitted_exclusions = $_POST['exclusions'] ?? [];
    
    $valid_exclusions = []; 
    foreach ($submitted_exclusions as $exclusion) {

        if (in_array($exclusion, $allowed_exclusions)) {
            $valid_exclusions[] = $exclusion;
        }
    }
    
    $exclusions_string = implode(", ", $valid_exclusions); 
    
    $_SESSION['register_step3_exclusions'] = $exclusions_string;
    
    header("Location: register_body.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Step 3: Food Exclusions</title>
</head>
<body>

<h2>Step 3: Select Foods to Avoid (Optional)</h2>

<p>Check any foods you need to exclude from your diet:</p>

<form method="POST" action=""> 

    <label><input type="checkbox" name="exclusions[]" value="gluten"> Gluten</label><br>
    <label><input type="checkbox" name="exclusions[]" value="peanuts"> Peanuts</label><br>
    <label><input type="checkbox" name="exclusions[]" value="eggs"> Eggs</label><br>
    <label><input type="checkbox" name="exclusions[]" value="fish"> Fish</label><br>
    <label><input type="checkbox" name="exclusions[]" value="dairy"> Dairy</label><br>
    <label><input type="checkbox" name="exclusions[]" value="soy"> Soy</label><br>
    <label><input type="checkbox" name="exclusions[]" value="shellfish"> Shellfish</label><br>
    <br>
    
    <button type="submit">Next</button>
</form>

</body>
</html>