<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = ''; 
$message_type = 'error'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    
    $sql_get_pfp = "SELECT profile_image_path FROM users WHERE id = {$user_id}";
    $pfp_result = $conn->query($sql_get_pfp);
    $pfp_path_to_delete = null;
    if ($pfp_result && $pfp_result->num_rows === 1) {
        $pfp_data = $pfp_result->fetch_assoc();
        if (!empty($pfp_data['profile_image_path'])) {
             $pfp_path_to_delete = $pfp_data['profile_image_path'];
        }
    }
     if($pfp_result) $pfp_result->free();

    $sql_delete = "DELETE FROM users WHERE id = {$user_id}";

    if ($conn->query($sql_delete)) {
        if ($pfp_path_to_delete && file_exists($pfp_path_to_delete)) {
            @unlink($pfp_path_to_delete); 
        }
        
        session_unset(); 
        session_destroy(); 

        header("Location: index.php?message=account_deleted"); 
        exit(); 

    } else {
        $message = "Error: Could not delete account. " . $conn->error;
        $message_type = 'error';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES['profile_pic']['type'];
    $file_size = $_FILES['profile_pic']['size'];
    $max_size = 2 * 1024 * 1024; 

    if (!in_array($file_type, $allowed_types)) {
        $message = "Error: Invalid file type. Only JPG, PNG, GIF allowed.";
    } elseif ($file_size > $max_size) {
        $message = "Error: File size exceeds the 2MB limit.";
    } else {
         $sql_get_pfp = "SELECT profile_image_path FROM users WHERE id = {$user_id}";
         $pfp_result = $conn->query($sql_get_pfp);
         $old_pfp_path = null;
         if ($pfp_result && $pfp_result->num_rows === 1) {
             $pfp_data = $pfp_result->fetch_assoc();
             if (!empty($pfp_data['profile_image_path'])) {
                  $old_pfp_path = $pfp_data['profile_image_path'];
             }
         }
          if($pfp_result) $pfp_result->free();

        $upload_dir = 'uploads/pfps/';
        $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $new_filename = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
        $target_path = $upload_dir . $new_filename;

        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0755, true); 
        }

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
            $image_path_sql = $conn->real_escape_string($target_path);
            $sql_update_pfp = "UPDATE users SET profile_image_path = '{$image_path_sql}' WHERE id = {$user_id}";
            
            if ($conn->query($sql_update_pfp)) {
                $message = "Profile picture updated successfully!";
                $message_type = 'success';
                 if ($old_pfp_path && file_exists($old_pfp_path)) {
                     @unlink($old_pfp_path);
                 }
            } else {
                $message = "Error updating database: " . $conn->error;
                @unlink($target_path); 
            }
        } else {
            $message = "Error uploading file. Check permissions on uploads folder.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
   
    if (empty($name) || empty($email)) {
        $message = "Please fill in both Name and Email fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        $email_sql = $conn->real_escape_string($email);
        $sql_check_email = "SELECT id FROM users WHERE email = '{$email_sql}' AND id != {$user_id}";
        $result_check_email = $conn->query($sql_check_email);

        if ($result_check_email && $result_check_email->num_rows > 0) {
            $message = "Error: This email address is already registered by another user.";
        } else {
            $name_sql = $conn->real_escape_string($name);
            
            $sql_update_profile = "UPDATE users SET name = '{$name_sql}', email = '{$email_sql}' WHERE id = {$user_id}";

            if ($conn->query($sql_update_profile)) {
                $message = "Profile updated successfully!";
                $message_type = 'success';
                $_SESSION['user_name'] = $name; 
            } else {
                 $message = "Error updating profile: " . $conn->error;
            }
        }
         if($result_check_email) $result_check_email->free();
    }
}

$user_data = null;
$sql_fetch = "SELECT name, email, profile_image_path FROM users WHERE id = {$user_id}"; 
$result = $conn->query($sql_fetch);

if ($result && $result->num_rows === 1) {
    $user_data = $result->fetch_assoc();
} else {
    session_unset();
    session_destroy();
    header("Location: login.php?error=user_not_found");
    exit();
}
if($result) $result->free();
$conn->close(); 

$pfp_path = (!empty($user_data['profile_image_path']) && file_exists($user_data['profile_image_path'])) 
            ? $user_data['profile_image_path'] 
            : 'images/default-pfp.png'; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - FitBite</title>
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
             <nav class="main-nav"><ul><li><a href="index.php">Home</a></li><li><a href="browse_foods.php">Browse Foods</a></li></ul></nav>
             <div class="account-nav">
                 <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu"> <button id="user-menu-button" class="user-menu-btn"><i class="fas fa-bars"></i></button> <div id="user-menu-dropdown" class="user-menu-dropdown"> <div class="user-info"><img src="<?php echo htmlspecialchars($pfp_path); ?>" alt="PFP" class="pfp"><span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span></div><ul><li><a href="account_settings.php"><i class="fas fa-cog"></i> Manage Account</a></li><li><a href="faq.php"><i class="fas fa-question-circle"></i> Help/FAQ</a></li><li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li></ul></div></div>
                 <?php else: ?>
                     <a href="register.php" class="btn btn-signup">Sign Up</a> <a href="login.php" class="signin-link">Sign in</a>
                 <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container form-container account-settings-container"> 
            
            <h2>Account Settings</h2>

            <?php 
            if (!empty($message)) {
                $message_class = ($message_type ?? 'error') === 'success' ? 'success' : 'error'; 
                echo '<p class="message ' . $message_class . '">' . htmlspecialchars($message) . '</p>'; 
            }
            ?>

            <div class="profile-picture-section">
                <img src="<?php echo htmlspecialchars($pfp_path); ?>" alt="Your Profile Picture" class="pfp-display">
                <form class="pfp-upload-form" method="POST" action="account_settings.php" enctype="multipart/form-data">
                     <label for="pfp" class="btn btn-secondary">Change Picture</label> 
                     <input type="file" name="profile_pic" id="pfp" accept="image/png, image/jpeg, image/gif" style="display: none;"> 
                     <button type="submit" name="upload_pfp" class="btn btn-primary">Upload</button> 
                </form>
            </div>

            <hr class="settings-divider">

            <form class="profile-update-form" method="POST" action="account_settings.php"> 
                <h3 style="margin-bottom: 15px; color: #555;">Update Profile Details</h3>
                <div class="form-group">
                     <label for="name"><i class="fas fa-user"></i> Full Name:</label>
                     <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
                </div>
                

                <button type="submit" name="update_profile" class="btn btn-primary btn-full-width">Save Profile Changes</button> 
            
            </form> 

            <hr class="settings-divider">

            <div class="delete-account-section">
                <h3>Delete Account</h3>
                <p>Warning: This action is permanent and cannot be undone. All your data will be lost.</p>
                <form method="POST" action="account_settings.php" onsubmit="return confirm('ARE YOU ABSOLUTELY SURE you want to delete your account? This cannot be undone!');">
                     <button type="submit" name="delete_account" class="btn btn-danger">Delete My Account Permanently</button>
                </form>
            </div>

        </div> 
    </main>

     <footer class="site-footer-main"><div class="container"><p>FitBite - Plan Your Meals</p></div></footer>

    <script src="script.js"></script> 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pfpLabel = document.querySelector('.pfp-upload-form label[for="pfp"]');
            const pfpInput = document.getElementById('pfp');
            if(pfpLabel && pfpInput) {
                pfpLabel.addEventListener('click', function() { pfpInput.click(); });
                pfpInput.addEventListener('change', function() { });
            }
        });
    </script>

</body>
</html>