<?php
session_start(); 
include 'config.php'; 

$search_term = $_GET['search'] ?? '';
$selected_diet = $_GET['diet'] ?? '';
$selected_meal = $_GET['meal'] ?? '';
$max_calories = $_GET['calories'] ?? 1500; 

$sql = "SELECT id, name, description, image_path, calories, protein, diet_category, meal_category FROM foods";
$conditions = []; 

if (!empty($search_term)) {
    $search_sql = $conn->real_escape_string($search_term);
    $conditions[] = "name LIKE '%{$search_sql}%'"; 
}

if (!empty($selected_diet) && $selected_diet !== 'All') {
    $diet_sql = $conn->real_escape_string($selected_diet);
    if ($diet_sql !== 'Anything') { 
        $conditions[] = "diet_category = '{$diet_sql}'";
    }
}

if (!empty($selected_meal) && $selected_meal !== 'All') {
    $meal_sql = $conn->real_escape_string($selected_meal);
     if ($meal_sql !== 'Any') {
        $conditions[] = "meal_category = '{$meal_sql}'";
     }
}

$calories_sql = (int)$max_calories; 
if ($calories_sql > 0 && $calories_sql <= 1500) { 
    $conditions[] = "calories <= {$calories_sql}";
} elseif ($calories_sql > 1500) {
     $conditions[] = "calories <= 1500"; 
} 

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY name ASC"; 

$foods = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $foods[] = $row;
    }
    $result->free();
} 
$conn->close(); 

$diet_categories = ['All', 'Anything', 'Keto', 'Vegan', 'Mediterranean']; 
$meal_categories = ['All', 'Breakfast', 'Lunch', 'Dinner', 'Snack', 'Any'];

$pfp_path = 'images/default-pfp.png'; 
if(isset($_SESSION['user_id'])) {
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Foods - FitBite</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Pacifico&display=swap" rel="stylesheet"> 
    
    <style>
        body, h1, h2, p, ul, li, form, input, select, button, label, span, div, section, header, main, footer, nav, a, img, i, small, table, thead, tbody, tr, th, td, textarea, hr, output {
            margin: 0;
            padding: 0;
            box-sizing: border-box; 
            font-family: 'Poppins', Arial, sans-serif; 
        }

        body {
            background-color: #f4f7f6; 
            color: #333;
            line-height: 1.6;
            padding-top: 70px; 
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto; 
        }

        .site-header {
            background-color: #ffffff; 
            padding: 10px 0; 
            border-bottom: 1px solid #e7e7e7;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05); 
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000; 
        }
        .header-content { display: flex; align-items: center; justify-content: space-between; }
        .logo a { font-family: 'Pacifico', cursive; font-weight: 400; color: #4CAF50; font-size: 1.8em; text-decoration: none; }
        .main-nav ul { list-style: none; display: flex; padding: 0; margin: 0; }
        .main-nav li { margin-left: 15px; margin-right: 15px; }
        .main-nav a { text-decoration: none; color: #555; font-weight: 500; transition: color 0.3s ease; }
        .main-nav a:hover { color: #28a745; }
        .account-nav { position: relative; display: flex; align-items: center; }
        .account-nav a, .account-nav button { margin-left: 15px; text-decoration: none; }
        .btn { padding: 8px 18px; border-radius: 20px; text-decoration: none; display: inline-block; text-align: center; border: none; cursor: pointer; font-weight: 600; transition: all 0.2s ease; }
        .btn-signup { background-color: #28a745; color: #fff; border: 1px solid #28a745; }
        .btn-signup:hover { background-color: #218838; border-color: #218838; transform: translateY(-1px); }
        .signin-link { color: #555; font-size: 0.9em; font-weight: 500; }
        .signin-link:hover { color: #28a745; }
         .user-menu-btn { background: none; border: none; padding: 5px; font-size: 1.4em; color: #555; cursor: pointer; }
         .user-menu-btn:hover { color: #28a745; }
         .user-menu-dropdown { display: none; position: absolute; right: 0; top: 120%; background-color: white; border: 1px solid #eee; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); min-width: 200px; z-index: 1001; } 
         .user-menu-dropdown.show-menu { display: block; }
         .user-menu-dropdown .user-info { display: flex; align-items: center; padding: 12px 15px; border-bottom: 1px solid #f0f0f0; }
         .user-menu-dropdown .pfp { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; object-fit: cover; background-color: #eee; }
         .user-menu-dropdown .user-info span { font-weight: 600; color: #333; font-size: 0.95em; }
         .user-menu-dropdown ul { list-style: none; padding: 5px 0; margin: 0; }
         .user-menu-dropdown li a { display: flex; align-items: center; padding: 10px 15px; color: #555; text-decoration: none; font-size: 0.9em; transition: background-color 0.2s ease, color 0.2s ease; }
         .user-menu-dropdown li a i { margin-right: 10px; width: 16px; text-align: center; color: #999; }
         .user-menu-dropdown li a:hover { background-color: #e8f5e9; color: #28a745; }
         .user-menu-dropdown li a:hover i { color: #28a745; }

        .main-content { padding: 40px 0; min-height: calc(100vh - 150px); }
        .main-content > .container > h1 { text-align: center; font-size: 2.2em; margin-bottom: 20px; color: #333; font-weight: 600; }
        .step-description { text-align: center; margin-bottom: 30px; color: #555; font-size: 1em; }

        .filter-form { background-color: #ffffff; padding: 20px 25px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .filter-controls { display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; flex-grow: 1; }
        .filter-group.search-group { flex-basis: 250px; }
        .filter-group label { font-size: 0.9em; font-weight: 600; color: #555; margin-bottom: 5px; display: flex; align-items: center; }
        .filter-group label i { margin-right: 5px; color: #999; }
        .filter-group input[type="search"], .filter-group input[type="range"], .filter-group select { padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.95em; width: 100%; }
        .filter-group select { background-color: white; cursor: pointer; }
        .calorie-group { display: flex; flex-direction: row; align-items: center; flex-wrap: nowrap; flex-basis: 300px; }
        .calorie-group label { margin-bottom: 0; }
        .calorie-group input[type="range"] { flex-grow: 1; height: 5px; cursor: pointer; margin: 0 10px; width: auto; }
        .calorie-group output { font-weight: 600; color: #28a745; min-width: 30px; text-align: right; }
        .filter-group button.btn-filter { padding: 9px 20px; font-size: 0.95em; background-color: #28a745; color: white; white-space: nowrap; border-radius: 4px; width: auto; flex-grow: 0;}
        .filter-group button.btn-filter:hover { background-color: #218838;}
        .filter-divider { border: none; border-top: 1px solid #eee; margin: 30px 0; }

        .browse-food-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 25px; }
        .food-card { background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06); transition: transform 0.2s ease, box-shadow 0.2s ease; border: 1px solid #f0f0f0; display: flex; flex-direction: column; }
        .food-card:hover { transform: translateY(-4px); box-shadow: 0 6px 15px rgba(40, 167, 69, 0.12); }
        .food-card-image { display: block; width: 100%; height: 180px; object-fit: cover; background-color: #eee; flex-shrink: 0; }
        .food-card-content { padding: 15px; display: flex; flex-direction: column; flex-grow: 1; }
        .food-card-name { font-size: 1.1em; font-weight: 600; color: #333; margin-bottom: 8px; }
        .food-card-description { font-size: 0.85em; color: #555; line-height: 1.5; margin-bottom: 10px; flex-grow: 1; word-wrap: break-word; }
        .food-card-description p { margin: 0 0 5px 0; }
        .food-card-description strong { color: #333; }
        .food-card-info { display: flex; justify-content: space-between; font-size: 0.9em; color: #555; border-top: 1px solid #f0f0f0; padding-top: 10px; margin-top: auto; flex-shrink: 0; }
        .food-card-info span { display: flex; align-items: center; }
        .food-card-info i { margin-right: 5px; color: #999; }
        .no-results { grid-column: 1 / -1; text-align: center; padding: 40px; color: #777; font-size: 1.1em; }
        
        .site-footer-main { background-color: #f8f9fa; text-align: center; padding: 20px 0; margin-top: 50px; font-size: 0.9em; color: #777; border-top: 1px solid #e7e7e7; }

    </style>
</head>
<body>

    <header class="site-header">
        <div class="container header-content">
            <div class="logo"><a href="index.php">FitBite</a></div>
             <nav class="main-nav"><ul><li><a href="index.php">Home</a></li><li><a href="browse_foods.php">Browse Foods</a></li></ul></nav>
             <div class="account-nav">
                 <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu"> <button id="user-menu-button" class="user-menu-btn"><i class="fas fa-bars"></i></button> <div id="user-menu-dropdown" class="user-menu-dropdown"> <div class="user-info"><img src="<?php echo isset($pfp_path) ? htmlspecialchars($pfp_path) : 'images/default-pfp.png'; ?>" alt="PFP" class="pfp"><span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span></div><ul><li><a href="account_settings.php"><i class="fas fa-cog"></i> Manage Account</a></li><li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li></ul></div></div>
                 <?php else: ?>
                     <a href="register.php" class="btn btn-signup">Sign Up</a> <a href="login.php" class="signin-link">Sign in</a>
                 <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h1>Browse Foods</h1>
            <p class="step-description">Find foods based on your preferences.</p>

            <form method="GET" action="browse_foods.php" class="filter-form">
                <div class="filter-controls">
                    <div class="filter-group search-group">
                        <label for="search"><i class="fas fa-search"></i> Search Name:</label>
                        <input type="search" id="search" name="search" placeholder="e.g., Chicken" value="<?php echo htmlspecialchars($search_term); ?>">
                    </div>
                     <div class="filter-group">
                        <label for="diet"><i class="fas fa-leaf"></i> Diet Type:</label>
                        <select id="diet" name="diet">
                            <?php foreach($diet_categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo ($selected_diet == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                     <div class="filter-group">
                         <label for="meal"><i class="fas fa-clock"></i> Meal Type:</label>
                         <select id="meal" name="meal">
                            <?php foreach($meal_categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo ($selected_meal == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                     <div class="filter-group calorie-group">
                        <label for="calories"><i class="fas fa-fire"></i> Max Calories (1-1500):</label>
                        <input type="range" id="calories" name="calories" min="0" max="1500" step="50" value="<?php echo htmlspecialchars($max_calories); ?>" oninput="this.nextElementSibling.value = this.value">
                        <output><?php echo htmlspecialchars($max_calories); ?></output> kcal
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary btn-filter">Apply Filters</button>
                    </div>
                </div>
            </form>

            <hr class="filter-divider">

            <div class="food-grid browse-food-grid">
                <?php if (count($foods) > 0): ?>
                    <?php foreach ($foods as $food): ?>
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
                    <p class="no-results">No foods found matching your criteria.</p>
                <?php endif; ?>
            </div>

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