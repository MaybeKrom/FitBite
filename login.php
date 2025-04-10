<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $submitted_email = $email;

    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {
        $sql = "SELECT id, name, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);

            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id']; 
                    $_SESSION['user_name'] = $user['name']; 

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid email or password.";
                }
            } else {
                $message = "Invalid email or password."; 
            }
            
            $stmt->close();

        } 
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

</head>
<body>

<div class="container">
    <h2>Login</h2>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required 
               value="<?php echo htmlspecialchars($submitted_email);  ?>">
        <br>
        <input type="password" name="password" placeholder="Password" required>
        <br>
        <button type="submit">Login</button>
    </form>

    <div class="links">
        <a href="register.php">Don't have an account? Create one!</a>
    </div>
</div>

</body>
</html>