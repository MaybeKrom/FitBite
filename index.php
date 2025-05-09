<?php
session_start(); 
include 'config.php'; 

$submitted_diet = $_POST['diet'] ?? 'anything'; 
$submitted_calories = $_POST['calories'] ?? 1800; 
$submitted_meals = $_POST['meals'] ?? 3; 
$suggested_foods = null; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_plan'])) {
    
    $sql = "SELECT id, name, description, image_path, calories, protein, diet_category, meal_category 
            FROM foods";
    
    if ($submitted_diet !== 'Anything') {
         $diet_sql = $conn->real_escape_string($submitted_diet);
         $sql .= " WHERE diet_category = '{$diet_sql}' OR diet_category = 'Anything'"; 
    }
    
    $sql .= " ORDER BY RAND() LIMIT 9"; 

    $foods_result = [];
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $foods_result[] = $row;
        }
        $result->free();
        $suggested_foods = $foods_result; 
    } 
    $conn->close(); 
}

$pfp_path = 'images/default-pfp.png'; 
if(isset($_SESSION['user_id'])) {
   
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitBite - Your Personal Meal Planner</title> 
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Pacifico&display=swap" rel="stylesheet"> 
</head>
<body>

    <header class="site-header">
        <div class="container header-content">
            <div class="logo">
                <a href="index.php">FitBite</a> 
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#">How It Works</a></li>
                    <li><a href="browse_foods.php">Browse Foods</a></li>
                </ul>
            </nav>
            <div class="account-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <button id="user-menu-button" class="user-menu-btn">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div id="user-menu-dropdown" class="user-menu-dropdown">
                             <div class="user-info">
                                <img src="<?php echo isset($pfp_path) ? htmlspecialchars($pfp_path) : 'images/default-pfp.png'; ?>" alt="PFP" class="pfp">
                                <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            </div>
                            <ul>
                                <li><a href="account_settings.php"><i class="fas fa-cog"></i> Manage Account</a></li>
                                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="register.php" class="btn btn-signup">Sign Up</a>
                    <a href="login.php" class="signin-link">Sign in</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Your Personal Meal Plan <span class="highlight">in Seconds</span></h1>
            <p class="hero-subtitle">Tell us your goals and preferences, we'll handle the planning!</p>
        </div>
    </section>

    <main class="main-content">
        <div class="container">
             <form class="meal-planner-form" method="POST" action="index.php"> 
                
                <div class="form-section diet-selection">
                    <h2 class="form-section-title"><i class="fas fa-leaf"></i> Choose Your Preferred Diet</h2>
                    <div class="diet-options enhanced-diet-options"> 
                        
                        <div class="diet-option">
                            <input type="radio" name="diet" value="anything" id="diet-anything" <?php echo ($submitted_diet === 'anything' || $submitted_diet === null) ? 'checked' : ''; ?>>
                            <label for="diet-anything">
                                <span class="icon">🥪</span>
                                <span class="diet-name">Anything</span>
                            </label>
                        </div>
                        <div class="diet-option">
                            <input type="radio" name="diet" value="keto" id="diet-keto" <?php echo ($submitted_diet === 'keto') ? 'checked' : ''; ?>>
                            <label for="diet-keto">
                                <span class="icon">🥓</span>
                                <span class="diet-name">Keto</span>
                            </label>
                        </div>
                         <div class="diet-option">
                             <input type="radio" name="diet" value="mediterranean" id="diet-med" <?php echo ($submitted_diet === 'mediterranean') ? 'checked' : ''; ?>>
                             <label for="diet-med">
                                 <span class="icon">🍇</span>
                                 <span class="diet-name">Mediterranean</span>
                             </label>
                         </div>
                        <div class="diet-option">
                            <input type="radio" name="diet" value="vegan" id="diet-vegan" <?php echo ($submitted_diet === 'vegan') ? 'checked' : ''; ?>>
                            <label for="diet-vegan">
                                <span class="icon">💚</span>
                                <span class="diet-name">Vegan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-section goal-inputs-enhanced"> 
                    <h2 class="form-section-title"><i class="fas fa-bullseye"></i> Set Your Goals</h2>
                    <div class="input-row">
                        <div class="input-group">
                            <label for="calories">I want to eat</label>
                            <input type="number" id="calories" name="calories" value="<?php echo htmlspecialchars($submitted_calories); ?>"> 
                            <label for="calories">calories</label>
                        </div>
                        <div class="input-group">
                            <label for="meals">in</label>
                            <select id="meals" name="meals">
                                <option value="1" <?php echo ($submitted_meals == 1) ? 'selected' : ''; ?>>1</option>
                                <option value="2" <?php echo ($submitted_meals == 2) ? 'selected' : ''; ?>>2</option>
                                <option value="3" <?php echo ($submitted_meals == 3) ? 'selected' : ''; ?>>3</option>
                                <option value="4" <?php echo ($submitted_meals == 4) ? 'selected' : ''; ?>>4</option>
                                <option value="5" <?php echo ($submitted_meals == 5) ? 'selected' : ''; ?>>5</option>
                            </select>
                            <label for="meals">meals</label>
                        </div>
                    </div>
                </div>

                <div class="form-section submit-section">
                    <button type="submit" name="generate_plan" class="btn btn-generate btn-generate-enhanced">
                        <i class="fas fa-magic"></i> Generate My Plan!
                    </button>
                </div>

            </form>

            <?php if ($suggested_foods !== null): ?>
                <div class="meal-plan-results">
                    <hr class="filter-divider">
                    <h2>Suggested Foods for '<?php echo htmlspecialchars(ucfirst($submitted_diet)); ?>' Diet</h2>
                    
                    <div class="food-grid browse-food-grid">
                        <?php if (count($suggested_foods) > 0): ?>
                             <?php foreach ($suggested_foods as $food): ?>
                                <?php 
                                    $food_pfp = (!empty($food['image_path']) && file_exists($food['image_path'])) 
                                                ? $food['image_path'] 
                                                : 'images/placeholder-food.png'; 
                                ?>
                                <div class="food-card">
                                    <img src="<?php echo htmlspecialchars($food_pfp); ?>" 
                                         alt="<?php echo htmlspecialchars($food['name']); ?>" 
                                         class="food-card-image" 
                                         onerror="this.onerror=null; this.src='images/placeholder-food.png';"> 
                                    <div class="food-card-content">
                                        <h3 class="food-card-name"><?php echo htmlspecialchars($food['name']); ?></h3>
                                        <div class="food-card-description"> 
                                            <p><?php echo nl2br(htmlspecialchars($food['description'] ?? 'No description available.')); ?></p>
                                            
                                        </div>
                                        <div class="food-card-info">
                                            <span><i class="fas fa-fire"></i> <?php echo htmlspecialchars($food['calories'] ?? 'N/A'); ?> kcal</span>
                                            <span><i class="fas fa-drumstick-bite"></i> <?php echo htmlspecialchars($food['protein'] ?? 'N/A'); ?> g P</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-results">No specific foods found matching the '<?php echo htmlspecialchars(ucfirst($submitted_diet)); ?>' diet in our database currently.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>
    
    <footer class="site-footer-main">
        <div class="container">
             <p>FitBite - Plan Your Meals</p>
        </div>
    </footer>

    <script src="script.js"></script> 

</body>
</html>