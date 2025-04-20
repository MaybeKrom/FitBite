<?php
session_start();

if (!isset($_SESSION['register_step1_email'])) { 
    header("Location: register.php");
    exit();
}

$allowed_diets = ['anything', 'keto', 'paleo', 'vegan', 'vegetarian', 'mediterranean']; 
$message = ''; 
$selected_diet = $_POST['diet'] ?? null; // For repopulating

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 2: Diet Choice</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container form-container diet-step-container"> 

    <h2>Step 2: Choose Your Diet</h2>
    <p class="step-description">Select the dietary preference that best suits you.</p>

    <?php 
    if (!empty($message)) {
        echo '<p class="message error">' . htmlspecialchars($message) . '</p>'; 
    }
    ?>

    <form class="register-form" method="POST" action=""> 

        <div class="diet-options-grid"> 
            
            <div class="diet-option">
                <input type="radio" name="diet" value="anything" id="diet-anything" required <?php echo ($selected_diet === 'anything' || $selected_diet === null) ? 'checked' : ''; ?>>
                <label for="diet-anything">
                    <span class="icon">🥪</span> 
                    <span class="diet-name">Anything</span>
                </label>
            </div>

            <div class="diet-option">
                <input type="radio" name="diet" value="keto" id="diet-keto" required <?php echo ($selected_diet === 'keto') ? 'checked' : ''; ?>>
                <label for="diet-keto">
                     <span class="icon">🥓</span> 
                     <span class="diet-name">Keto</span>
                </label>
            </div>

             <div class="diet-option">
                 <input type="radio" name="diet" value="mediterranean" id="diet-med" required <?php echo ($selected_diet === 'mediterranean') ? 'checked' : ''; ?>>
                 <label for="diet-med">
                     <span class="icon">🍇</span> 
                     <span class="diet-name">Mediterranean</span>
                 </label>
             </div>
              
            <div class="diet-option">
                <input type="radio" name="diet" value="paleo" id="diet-paleo" required <?php echo ($selected_diet === 'paleo') ? 'checked' : ''; ?>>
                <label for="diet-paleo">
                     <span class="icon">🥕</span> 
                     <span class="diet-name">Paleo</span>
                </label>
            </div>

            <div class="diet-option">
                <input type="radio" name="diet" value="vegan" id="diet-vegan" required <?php echo ($selected_diet === 'vegan') ? 'checked' : ''; ?>>
                <label for="diet-vegan">
                     <span class="icon">💚</span> 
                     <span class="diet-name">Vegan</span>
                </label>
            </div>

            <div class="diet-option">
                <input type="radio" name="diet" value="vegetarian" id="diet-vegetarian" required <?php echo ($selected_diet === 'vegetarian') ? 'checked' : ''; ?>>
                <label for="diet-vegetarian">
                    <span class="icon">🥦</span> 
                    <span class="diet-name">Vegetarian</span>
                </label>
            </div>

        </div> 

        <div class="form-section submit-section" style="margin-top: 30px;"> 
             <button type="submit" class="btn btn-primary btn-full-width">Next Step <i class="fas fa-arrow-right"></i></button> 
        </div>

    </form>

    <div class="form-footer-link">
        <a href="register.php">Back to Step 1</a>
    </div> 

</div> 

</body>
</html>