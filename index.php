<?php
session_start(); 
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
                <a href="#">FitBite</a> 
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#">How It Works</a></li>
                    <li><a href="#">Browse Foods</a></li>
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
                                <img src="placeholder-pfp.png" alt="PFP" class="pfp">
                                <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            </div>
                            <ul>
                                <li><a href="account_settings.php"><i class="fas fa-cog"></i> Manage Account</a></li>
                                <li><a href="faq.php"><i class="fas fa-question-circle"></i> Help/FAQ</a></li>
                                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="register.php" class="btn btn-signup">Sign Up</a>
                    <a href="login.php" class="signin-link">Already remember? Sign in</a>
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
             <form class="meal-planner-form" method="POST" action="#"> 
                
                <div class="form-section diet-selection">
                    <h2 class="form-section-title"><i class="fas fa-leaf"></i> Choose Your Preferred Diet</h2>
                    <div class="diet-options enhanced-diet-options"> 
                        
                        <div class="diet-option">
                            <input type="radio" name="diet" value="anything" id="diet-anything" checked>
                            <label for="diet-anything">
                                <span class="icon">🥪</span>
                                <span class="diet-name">Anything</span>
                            </label>
                        </div>
                        <div class="diet-option">
                            <input type="radio" name="diet" value="keto" id="diet-keto">
                            <label for="diet-keto">
                                <span class="icon">🥓</span>
                                <span class="diet-name">Keto</span>
                            </label>
                        </div>
                         <div class="diet-option">
                             <input type="radio" name="diet" value="mediterranean" id="diet-med">
                             <label for="diet-med">
                                 <span class="icon">🍇</span>
                                 <span class="diet-name">Mediterranean</span>
                             </label>
                         </div>
                        <div class="diet-option">
                            <input type="radio" name="diet" value="paleo" id="diet-paleo">
                            <label for="diet-paleo">
                                <span class="icon">🥕</span>
                                <span class="diet-name">Paleo</span>
                            </label>
                        </div>
                        <div class="diet-option">
                            <input type="radio" name="diet" value="vegan" id="diet-vegan">
                            <label for="diet-vegan">
                                <span class="icon">💚</span>
                                <span class="diet-name">Vegan</span>
                            </label>
                        </div>
                        <div class="diet-option">
                            <input type="radio" name="diet" value="vegetarian" id="diet-vegetarian">
                            <label for="diet-vegetarian">
                                <span class="icon">🥦</span>
                                <span class="diet-name">Vegetarian</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-section goal-inputs-enhanced"> 
                    <h2 class="form-section-title"><i class="fas fa-bullseye"></i> Set Your Goals</h2>
                    <div class="input-row">
                        <div class="input-group">
                            <label for="calories">I want to eat</label>
                            <input type="number" id="calories" name="calories" value="1800"> 
                            <label for="calories">calories</label>
                        </div>
                        <div class="input-group">
                            <label for="meals">in</label>
                            <select id="meals" name="meals">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3" selected>3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                            <label for="meals">meals</label>
                        </div>
                    </div>
                </div>

                <div class="form-section submit-section">
                    <button type="submit" class="btn btn-generate btn-generate-enhanced">
                        <i class="fas fa-magic"></i> Generate My Plan!
                    </button>
                </div>

            </form>
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