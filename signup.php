<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Initialize variables for form values
$fullname = $email_address = $password = '';

// Initialize variables for error messages
$fullname_error = $email_error = $password_error = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate full name
    $fullname = $_POST['fullname'];
    if (empty($fullname)) {
        $fullname_error = 'Full name is required';
    }

    // Validate email address
    $email_address = $_POST['email_address'];
    if (empty($email_address)) {
        $email_error = 'Email address is required';
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $email_error = 'Invalid email address';
    }

    // Validate password
    $password = $_POST['password'];
    if (empty($password)) {
        $password_error = 'Password is required';
    } elseif (strlen($password) < 6) {
        $password_error = 'Password must be at least 6 characters long';
    }

    // If there are no validation errors, proceed with registration
    if (empty($fullname_error) && empty($email_error) && empty($password_error)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $type = "customer";

        // Insert user into the database using PDO prepared statements
        $insert_query = "INSERT INTO users (fullname, email_address, password, type) VALUES (:fullname, :email_address, :password, :type)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email_address', $email_address);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':type', $type);

        if ($stmt->execute()) {
            // Registration successful, set session and redirect to a dashboard or home page
            $_SESSION['logged_in'] = "yes";
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['email_address'] = $email_address;
            $_SESSION['type'] = $type;
            $_SESSION['fullname'] = $fullname;
            header("Location: index.php"); // Redirect to your dashboard page
            exit();
        } else {
            // Registration failed, display the error
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}
?>

<main>
    <h1>Create your Account!</h1>
    <hr />
    <form action="" method="post">
        <label>Full Name <span class="error"><?php echo $fullname_error; ?></span></label>
        <input type="text" name="fullname" value="<?php echo $fullname; ?>" />
        

        <label>Email Address <span class="error"><?php echo $email_error; ?></span></label>
        <input type="text" name="email_address" value="<?php echo $email_address; ?>" />
        

        <label>Password <span class="error"><?php echo $password_error; ?></span></label>
        <input type="password" name="password" />
        

        <input type="submit" name="submit" value="Submit" />
    </form>
    <hr />
</main>

<?php
include 'includes/footer.php';
?>
