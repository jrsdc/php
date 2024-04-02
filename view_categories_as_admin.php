<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email_address']) || $_SESSION['type'] !== 'admin') {
    // Redirect to the login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Fetch categories from the database using PDO
$query = "SELECT * FROM categories";
$stmt = $conn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>
        View All Categories!
        <button><a href="create_category_as_admin.php"> Create Category</a></button>
    </h1>

    <hr />
    <table id="table">
        <tr>
            <th>Name</th>
        </tr>

        <?php
        // Loop through the fetched categories and display them in the table
        foreach ($categories as $row) {
            echo "<tr>";
            echo "<td>{$row['name']}</td>";
            echo "</tr>";
        }
        ?>

    </table>

    <hr />
</main>

<?php
include 'includes/footer.php';
?>
