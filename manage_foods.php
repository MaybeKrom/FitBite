<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php"); 
    exit();
}

$message = ''; 
$message_type = 'error'; 
$admin_id = $_SESSION['user_id'];

$allowed_diet_categories = ['Anything', 'Keto', 'Paleo', 'Vegan', 'Vegetarian', 'Mediterranean'];
$allowed_meal_categories = ['Breakfast', 'Lunch', 'Dinner', 'Snack', 'Any'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_food'])) {
    
    $food_name = $_POST['food_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $calories = $_POST['calories'] ?? '';
    $protein = $_POST['protein'] ?? '';
    $diet_category = $_POST['diet_category'] ?? '';
    $meal_category = $_POST['meal_category'] ?? '';
    $image_path_sql = 'NULL'; 
    $image_upload_error = false; 

    if (empty($food_name) || $calories === '' || $protein === '' || empty($diet_category) || empty($meal_category)) {
        $message = "Please fill in all required fields (Name, Calories, Protein, Categories).";
    } elseif (!is_numeric($calories) || $calories < 0 || !is_numeric($protein) || $protein < 0) {
         $message = "Calories and Protein must be positive numbers.";
    } elseif (!in_array($diet_category, $allowed_diet_categories)) {
         $message = "Invalid Diet Category selected.";
    } elseif (!in_array($meal_category, $allowed_meal_categories)) {
         $message = "Invalid Meal Category selected.";
    } else {
        if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['food_image']['type'];
            $file_size = $_FILES['food_image']['size'];
            $max_size = 2 * 1024 * 1024; 

            if (!in_array($file_type, $allowed_types)) {
                $message = "Error: Invalid image file type. Only JPG, PNG, GIF allowed.";
                $image_upload_error = true;
            } elseif ($file_size > $max_size) {
                $message = "Error: Image file size exceeds the 2MB limit.";
                $image_upload_error = true;
            } else {
                $upload_dir = 'uploads/foods/';
                $file_extension = pathinfo($_FILES['food_image']['name'], PATHINFO_EXTENSION);
                $safe_filename_base = preg_replace("/[^a-zA-Z0-9_-]/", "_", pathinfo($_FILES['food_image']['name'], PATHINFO_FILENAME));
                $new_filename = 'food_' . time() . '_' . $safe_filename_base . '.' . $file_extension;
                $target_path = $upload_dir . $new_filename;

                if (!is_dir($upload_dir)) {
                   @mkdir($upload_dir, 0755, true); 
                }

                if (move_uploaded_file($_FILES['food_image']['tmp_name'], $target_path)) {
                    $image_path_sql = "'" . $conn->real_escape_string($target_path) . "'"; 
                } else {
                     $message = "Error uploading image file. Check permissions on uploads folder.";
                     $image_upload_error = true;
                     $image_path_sql = 'NULL'; 
                }
            }
        } elseif (isset($_FILES['food_image']) && $_FILES['food_image']['error'] != UPLOAD_ERR_NO_FILE) {
             $message = "Error uploading image: Error code " . $_FILES['food_image']['error'];
             $image_upload_error = true;
        }
        
        if (!$image_upload_error) {
            $name_sql = $conn->real_escape_string($food_name);
            $description_sql = $conn->real_escape_string($description);
            $calories_sql = (int)$calories;
            $protein_sql = (int)$protein;
            $diet_category_sql = $conn->real_escape_string($diet_category);
            $meal_category_sql = $conn->real_escape_string($meal_category);

            // --- MODIFIED SQL INSERT: Removed 'added_by_admin_id' ---
            $sql_insert = "INSERT INTO foods 
                          (name, description, image_path, calories, protein, diet_category, meal_category) 
                          VALUES 
                          ('{$name_sql}', '{$description_sql}', {$image_path_sql}, {$calories_sql}, {$protein_sql}, '{$diet_category_sql}', '{$meal_category_sql}')";

            if ($conn->query($sql_insert)) {
                $message = "Food item '{$food_name}' added successfully!";
                $message_type = 'success';
                // Clear form fields on success maybe? Optional.
                // $_POST = array(); 
            } else {
                $message = "Database Error: Could not add food item. (" . $conn->errno . ") " . $conn->error;
                $message_type = 'error';
                 if ($image_path_sql !== 'NULL' && isset($target_path) && file_exists($target_path)) {
                     @unlink($target_path);
                 }
            }
        } 
    } 
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Foods - FitBite Admin</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Pacifico&display=swap" rel="stylesheet"> 
</head>
<body>
    
    <header class="site-header">
         <div class="container header-content">
             <div class="logo"><a href="index.php">FitBite</a></div>
             <div style="color: #dc3545; font-weight: bold; margin: 0 auto;">ADMIN AREA</div> 
             <nav class="main-nav" style="flex-grow: 0;"> 
                <ul><li><a href="admin_dashboard.php">Admin Home</a></li><li><a href="index.php">Main Site</a></li></ul>
             </nav>
             <div class="account-nav">
                 <a href="logout.php" class="btn btn-signup" style="background-color: #dc3545; border-color:#dc3545;">Admin Logout</a> 
             </div>
         </div>
    </header>

    <main class="main-content">
        <div class="container form-container manage-food-container"> 
            
            <h2>Add New Food Item</h2>
            <p class="step-description">Fill in the details below to add a food to the database.</p>

            <?php 
            if (!empty($message)) {
                echo '<p class="message ' . ($message_type ?? 'error') . '">' . htmlspecialchars($message) . '</p>'; 
            }
            ?>

            <form class="add-food-form" method="POST" action="manage_foods.php" enctype="multipart/form-data"> 
                
                <div class="form-group">
                     <label for="food_name"><i class="fas fa-tag"></i> Food Name:</label>
                     <input type="text" id="food_name" name="food_name" required value="<?php echo isset($_POST['food_name']) ? htmlspecialchars($_POST['food_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description:</label>
                    <textarea id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                     <label for="food_image"><i class="fas fa-image"></i> Food Image:</label>
                     <input type="file" id="food_image" name="food_image" accept="image/png, image/jpeg, image/gif">
                     <small>Optional. Max 2MB. JPG, PNG, GIF only.</small>
                </div>

                 <div class="form-group">
                    <label for="calories"><i class="fas fa-fire"></i> Calories (kcal):</label>
                    <input type="number" id="calories" name="calories" required min="0" value="<?php echo isset($_POST['calories']) ? htmlspecialchars($_POST['calories']) : ''; ?>">
                </div>

                 <div class="form-group">
                    <label for="protein"><i class="fas fa-drumstick-bite"></i> Protein (g):</label>
                    <input type="number" id="protein" name="protein" required min="0" value="<?php echo isset($_POST['protein']) ? htmlspecialchars($_POST['protein']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="diet_category"><i class="fas fa-leaf"></i> Diet Category:</label> 
                    <select id="diet_category" name="diet_category" required> 
                        <option value="" disabled <?php echo empty($_POST['diet_category']) ? 'selected' : ''; ?>>-- Select Diet Type --</option> 
                        <?php foreach ($allowed_diet_categories as $diet_cat): ?>
                            <option value="<?php echo $diet_cat; ?>" <?php echo (isset($_POST['diet_category']) && $_POST['diet_category'] == $diet_cat) ? 'selected' : ''; ?>><?php echo $diet_cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="meal_category"><i class="fas fa-clock"></i> Suitable Meal:</label> 
                    <select id="meal_category" name="meal_category" required> 
                        <option value="" disabled <?php echo empty($_POST['meal_category']) ? 'selected' : ''; ?>>-- Select Meal Type --</option> 
                         <?php foreach ($allowed_meal_categories as $meal_cat): ?>
                            <option value="<?php echo $meal_cat; ?>" <?php echo (isset($_POST['meal_category']) && $_POST['meal_category'] == $meal_cat) ? 'selected' : ''; ?>><?php echo $meal_cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" name="add_food" class="btn btn-primary btn-full-width"><i class="fas fa-plus-circle"></i> Add Food Item</button> 
            
            </form> 

             <div class="form-footer-link" style="margin-top: 30px;">
                <a href="admin_dashboard.php">Back to Admin Dashboard</a> 
            </div>

        </div> 
    </main>

     <footer class="site-footer-main">
        <div class="container">
             <p>FitBite Admin Panel</p>
        </div>
    </footer>

    <script src="script.js"></script> 

</body>
</html>