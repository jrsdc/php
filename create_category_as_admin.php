<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email_address']) || $_SESSION['type'] !== 'admin') {
    // Redirect to the login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Initialize variables for error messages
$category_error = '';
$category_created = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);

    // Validate category name (you should add more validation and sanitation)
    if (empty($name)) {
        $category_error = 'Category name is required';
    } else {
        // Check if the category already exists
        $check_query = "SELECT * FROM categories WHERE name = :name";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            // Category already exists, display an error message
            $category_error = 'Category already exists';
        } else {
            // Insert category into the database
            $insert_query = "INSERT INTO categories (name) VALUES (:name)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bindParam(':name', $name, PDO::PARAM_STR);

            if ($insert_stmt->execute()) {
                // Category created successfully
                $category_created = 'Category is created';
            } else {
                // Category creation failed, display the error
                echo "Error: " . $conn->error;
            }
        }
    }
}
?>

<main>
    <h1>Create Category!</h1>
    <hr />
    <?php echo $category_created; ?>
    <form action="" method="post">
        <label>Category Name <span class="error"><?php echo $category_error; ?></span></label>
        <input type="text" name="name" value="" />
        <input type="submit" name="submit" value="Submit" />
    </form>
    <hr />
</main>

<?php
include 'includes/footer.php';
?>
