<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email_address']) || $_SESSION['type'] !== 'admin') {
    // Redirect to the login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Fetch product data with category details using PDO
$product_query = "SELECT products.*, categories.name as category_name FROM products
                  INNER JOIN categories ON products.category_id = categories.id";
$product_stmt = $conn->prepare($product_query);
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>
        View All Products!
        <button><a href="create_product_as_admin.php"> Create Products</a></button>
    </h1>

    <hr />
    <table id="table">
        <tr>
            <th>Image</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <!-- <th>Actions</th> -->
        </tr>
        <?php
        // Loop through the fetched products and populate the table
        foreach ($products as $product_row) {
            echo "<tr>";
            echo "<td><img src='{$product_row['banner_image']}' alt='{$product_row['title']}' style='width: 50px; height: 50px;'></td>";
            echo "<td>{$product_row['title']}</td>";
            echo "<td>{$product_row['category_name']}</td>";
            echo "<td>£{$product_row['price']}</td>";
            // echo "<td><a href='view_products_questions_as_admin.php?product_id={$product_row['id']}'><button>Questions</button></a></td>"; // Replace with actual actions, e.g., Edit, Delete, etc.
            echo "</tr>";
        }
        ?>
    </table>
    <hr />
</main>

<?php
include 'includes/footer.php';
?>
