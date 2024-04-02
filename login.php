<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_address = $_POST['email_address'];
    $password = $_POST['password'];

    // Validate user inputs (you should add more validation and sanitation)
    $email_address = htmlspecialchars($email_address);
    $password = htmlspecialchars($password);

    // Query to check if the user exists
    $sql = "SELECT * FROM users WHERE email_address = :email_address";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email_address', $email_address, PDO::PARAM_STR);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_data) {
        // User found, verify the password
        $hashed_password = $user_data['password'];

        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session and redirect to a dashboard or home page
            session_start();
            $_SESSION['logged_in'] = "yes";
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['email_address'] = $email_address;
            $_SESSION['type'] = $user_data['type'];
            $_SESSION['fullname'] = $user_data['fullname'];
            header("Location: index.php"); // Redirect to your dashboard page
            exit();
        } else {
            // Display login error message
            $login_error_message = "Wrong email address or password.";
        }
    } else {
        // Display login error message
        $login_error_message = "Wrong email address or password.";
    }
}
?>

<main>
    <h1>Login to your Account!</h1>
    <hr />
    <?php
    // Display login error message if set
    if (isset($login_error_message)) {
        echo '<div class="error-message">' . $login_error_message . '</div>';
    }
    ?>
    <form action="" method="post">
        <label>Email Address</label> <input type="text" name="email_address" />
        <label>Password</label> <input type="password" name="password" /> <!-- Use type="password" for password input -->
        <input type="submit" name="submit" value="Submit" />
    </form>
    <a href="signup.php">Sign Up</a>
    <hr />
</main>

<?php
include 'includes/footer.php';
?>
