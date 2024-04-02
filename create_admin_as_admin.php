<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email_address']) || $_SESSION['type'] !== 'admin') {
    // Redirect to the login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Initialize variables for form inputs and errors
$fullname = $email_address = $password = "";
$fullname_error = $email_address_error = $password_error = "";
$product_created = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate full name
    if (empty($_POST['fullname'])) {
        $fullname_error = 'Full Name is required';
    } else {
        $fullname = htmlspecialchars($_POST['fullname']);
    }

    // Validate email address
    if (empty($_POST['email_address'])) {
        $email_address_error = 'Email Address is required';
    } elseif (!filter_var($_POST['email_address'], FILTER_VALIDATE_EMAIL)) {
        $email_address_error = 'Invalid email format';
    } else {
        $email_address = htmlspecialchars($_POST['email_address']);

        // Check if the email address is already taken
        $check_email_query = "SELECT * FROM users WHERE email_address = :email_address";
        $check_email_stmt = $conn->prepare($check_email_query);
        $check_email_stmt->bindParam(':email_address', $email_address, PDO::PARAM_STR);
        $check_email_stmt->execute();

        if ($check_email_stmt->rowCount() > 0) {
            $email_address_error = 'Email Address is already taken';
        }
    }

    // Validate password
    if (empty($_POST['password'])) {
        $password_error = 'Password is required';
    } else {
        $password = password_hash(htmlspecialchars($_POST['password']), PASSWORD_DEFAULT);
    }

    // If there are no validation errors, insert data into the database
    if (empty($fullname_error) && empty($email_address_error) && empty($password_error)) {
        $insert_admin_query = "INSERT INTO users (fullname, email_address, password, type) 
                               VALUES (:fullname, :email_address, :password, 'admin')";
        $insert_admin_stmt = $conn->prepare($insert_admin_query);
        $insert_admin_stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $insert_admin_stmt->bindParam(':email_address', $email_address, PDO::PARAM_STR);
        $insert_admin_stmt->bindParam(':password', $password, PDO::PARAM_STR);

        if ($insert_admin_stmt->execute()) {
            $product_created = "Admin created successfully!";
        } else {
            $errorInfo = $conn->errorInfo();
            echo "Error: " . $errorInfo[2];
        }
    }
}
?>

<main>
    <h1>Create Admin!</h1>
    <hr />
    <?php echo $product_created; ?>
    <form action="" method="post" enctype="multipart/form-data">
        
        <table id="table">   
            <tr>
                <td><label>Full Name <span class="error"><?php echo $fullname_error; ?></span></label></td>
                <td><input type="text" name="fullname" value="<?php echo $fullname; ?>" /></td>
            </tr>

            <tr>
                <td><label>Email Address <span class="error"><?php echo $email_address_error; ?></span></label></td>
                <td><input type="text" name="email_address" value="<?php echo $email_address; ?>" /></td>
            </tr>

            <tr>
                <td><label>Password <span class="error"><?php echo $password_error; ?></span></label></td>
                <td><input type="password" name="password" value="" /></td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="submit" name="submit" value="Submit" />
                </td>
            </tr>
        </table>      
 
    </form>
    <hr />
</main>

<?php
include 'includes/footer.php';
?>
