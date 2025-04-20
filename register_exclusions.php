<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['register_step1_email']) || !isset($_SESSION['register_step2_diet'])) { 
    header("Location: register.php");
    exit();
}

$allowed_exclusions = ['gluten', 'peanuts', 'eggs', 'fish', 'dairy', 'soy', 'shellfish'];
$submitted_exclusions_post = $_POST['exclusions'] ?? []; // Store POST data for repopulation

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $submitted_exclusions = $submitted_exclusions_post; 
    
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 3: Food Exclusions</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container form-container exclusions-step-container"> 

    <h2>Step 3: Select Foods to Avoid (Optional)</h2>
    <p class="step-description">Check any common allergens or foods you wish to exclude.</p>

    <form class="register-form" method="POST" action=""> 

        <div class="checkbox-options-grid">
            
            <div class="checkbox-option">
                <input type="checkbox" name="exclusions[]" value="gluten" id="excl-gluten" 
                       <?php echo (in_array('gluten', $submitted_exclusions_post)) ? 'checked' : ''; ?>>
                <label for="excl-gluten">
                    <span class="icon">🌾</span> 
                    Gluten
                </label>
            </div>

            <div class="checkbox-option">
                <input type="checkbox" name="exclusions[]" value="peanuts" id="excl-peanuts"
                       <?php echo (in_array('peanuts', $submitted_exclusions_post)) ? 'checked' : ''; ?>>
                <label for="excl-peanuts">
                    <span class="icon">🥜</span> 
                    Peanuts
                </label>
            </div>

            <div class="checkbox-option">
                <input type="checkbox" name="exclusions[]" value="eggs" id="excl-eggs"
                       <?php echo (in_array('eggs', $submitted_exclusions_post)) ? 'checked' : ''; ?>>
                <label for="excl-eggs">
                    <span class="icon">🥚</span> 
                    Eggs
                </label>
            </div>

            <div class="checkbox-option">
                <input type="checkbox" name="exclusions[]" value="fish" id="excl-fish"
                       <?php echo (in_array('fish', $submitted_exclusions_post)) ? 'checked' : ''; ?>>
                <label for="excl-fish">
                     <span class="icon">🐟</span> 
                    Fish
                </label>
            </div>

            <div class="checkbox-option">
                <input type="checkbox" name="exclusions[]" value="dairy" id="excl-dairy"
                       <?php echo (in_array('dairy', $submitted_exclusions_post)) ? 'checked' : ''; ?>>
                <label for="excl-dairy">
                     <span class="icon">🥛</span> 
                    Dairy
                </label>
            </div>

            <div class="checkbox-option">
                <input type="checkbox" name="exclusions[]" value="soy" id="excl-soy"
                       <?php echo (in_array('soy', $submitted_exclusions_post)) ? 'checked' : ''; ?>>
                <label for="excl-soy">
                    <span class="icon">🌱</span> 
                    Soy
                </label>
            </div>

            <div class="checkbox-option">
                <input type="checkbox" name="exclusions[]" value="shellfish" id="excl-shellfish"
                       <?php echo (in_array('shellfish', $submitted_exclusions_post)) ? 'checked' : ''; ?>>
                <label for="excl-shellfish">
                    <span class="icon">🦐</span> 
                    Shellfish
                </label>
            </div>

        </div> 


        <div class="form-section submit-section" style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary btn-full-width">Next Step <i class="fas fa-arrow-right"></i></button>
        </div>

    </form>

    <div class="form-footer-link">
        <a href="register_diet.php">Back to Step 2</a>
    </div>

</div> 

</body>
</html>