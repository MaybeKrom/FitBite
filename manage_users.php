<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php"); 
    exit();
}

$current_admin_id = $_SESSION['user_id']; 
$message = ''; 
$message_type = 'error'; 

if (isset($_SESSION['manage_users_message'])) {
    $message = $_SESSION['manage_users_message'];
    $message_type = $_SESSION['manage_users_message_type'] ?? 'error';
    unset($_SESSION['manage_users_message']);
    unset($_SESSION['manage_users_message_type']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user_id'])) {
    $user_id_to_delete = (int)$_POST['delete_user_id'];

    if ($user_id_to_delete === $current_admin_id) {
        $_SESSION['manage_users_message'] = 'Error: Admins cannot delete their own account from this panel.';
        $_SESSION['manage_users_message_type'] = 'error';
    } else {
        $sql_get_pfp = "SELECT profile_image_path FROM users WHERE id = {$user_id_to_delete}";
        $pfp_result = $conn->query($sql_get_pfp);
        $pfp_path_to_delete = null;
        if ($pfp_result && $pfp_result->num_rows === 1) {
            $pfp_data = $pfp_result->fetch_assoc();
            if (!empty($pfp_data['profile_image_path'])) {
                 $pfp_path_to_delete = $pfp_data['profile_image_path'];
            }
        }
        if($pfp_result) $pfp_result->free();

        $sql_delete = "DELETE FROM users WHERE id = {$user_id_to_delete}";
        if ($conn->query($sql_delete)) {
            if ($conn->affected_rows > 0) {
                 if ($pfp_path_to_delete && file_exists($pfp_path_to_delete)) {
                    @unlink($pfp_path_to_delete); 
                }
                $_SESSION['manage_users_message'] = 'User deleted successfully.';
                $_SESSION['manage_users_message_type'] = 'success';
            } else {
                 $_SESSION['manage_users_message'] = 'Error: User not found or already deleted.';
                 $_SESSION['manage_users_message_type'] = 'error';
            }
        } else {
            $_SESSION['manage_users_message'] = 'Error deleting user: ' . $conn->error;
            $_SESSION['manage_users_message_type'] = 'error';
        }
    }
    header('Location: manage_users.php');
    exit();
}

$users = [];
$sql_fetch_users = "SELECT id, name, email, role, profile_image_path FROM users ORDER BY id ASC"; 
$result = $conn->query($sql_fetch_users);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
} else {
    if(empty($message)) { 
         $message = "Error fetching users: " . $conn->error;
         $message_type = 'error';
    }
}

$conn->close(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - FitBite Admin</title>
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
        <div class="container form-container user-management-container"> 
            
            <h2>Manage Users</h2>

            <?php 
            if (!empty($message)) {
                echo '<p class="message ' . $message_type . '">' . htmlspecialchars($message) . '</p>'; 
            }
            ?>

            <div class="user-list-table-wrapper">
                <table class="user-list-table">
                    <thead>
                        <tr>
                            <th>PFP</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $pfp = (!empty($user['profile_image_path']) && file_exists($user['profile_image_path'])) 
                                                ? $user['profile_image_path'] 
                                                : 'images/default-pfp.png'; 
                                        ?>
                                        <img src="<?php echo htmlspecialchars($pfp); ?>" alt="PFP" class="pfp-small">
                                    </td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td>
                                        <?php if ($user['id'] !== $current_admin_id): ?>
                                            <form method="POST" action="manage_users.php" class="delete-form" onsubmit="return confirm('Delete user \'<?php echo htmlspecialchars(addslashes($user['name'])); ?>\'? This cannot be undone!');">
                                                <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-small">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            (Current Admin)
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px;">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

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