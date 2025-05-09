<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit(); 
}
include 'config.php'; // Keep this if you plan DB operations on this page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitBite</title>
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
                <ul><li><a href="index.php">Main Site</a></li></ul>
             </nav>
             <div class="account-nav">
                 <a href="logout.php" class="btn btn-signup" style="background-color: #dc3545; border-color:#dc3545;">Admin Logout</a> 
             </div>
         </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h1>Admin Dashboard</h1>
            <p class="step-description">Welcome, Administrator!</p>
            
            <div class="admin-section form-container" style="max-width: 800px;"> 
                 <h2><i class="fas fa-users-cog"></i> User Management</h2>
                 <p>View, edit, or remove user accounts.</p>
                 
                 <a href="manage_users.php" class="btn btn-primary" style="margin-top: 15px; display: inline-block; width: auto;">Manage Users</a> 
            </div>

             <div class="admin-section form-container" style="max-width: 800px; margin-top: 30px;">
                 <h2><i class="fas fa-utensils"></i> Food Management</h2>
                 <p>Add, edit, or remove food items from the database.</p>
                 
                  <a href="manage_foods.php" class="btn btn-primary" style="margin-top: 15px; display: inline-block; width: auto;">Manage Foods</a> 
             </div>

             

        </div>
    </main>

     <footer class="site-footer-main">
        <div class="container">
             <p>FitBite Admin Panel</p>
        </div>
    </footer>

</body>
</html>