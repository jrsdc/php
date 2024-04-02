<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email_address']) || $_SESSION['type'] !== 'admin') {
    // Redirect to the login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Fetch admins from the database using PDO
$admins_query = "SELECT * FROM users WHERE type = 'admin'";
$admins_statement = $conn->prepare($admins_query);
$admins_statement->execute();
$admins = $admins_statement->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>
        View All Admins!
        <button><a href="create_admin_as_admin.php"> Create Admins</a></button>
    </h1>

    <hr />
    <table id="table">
        <tr>
            <th>FullName</th>
            <th>Email Address</th>
            <!-- <th>Actions</th> -->
        </tr>

        <?php
        // Loop through the result set and display each admin
        foreach ($admins as $admin_row) {
            echo "<tr>";
            echo "<td>{$admin_row['fullname']}</td>";
            echo "<td>{$admin_row['email_address']}</td>";
            // Add any additional columns or actions as needed
            echo "</tr>";
        }
        ?>
    </table>
    <hr />
</main>

<?php
include 'includes/footer.php';
?>
