<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if category_id is provided in the URL
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Fetch products related to the specified category
    $product_statement = $conn->prepare("SELECT * FROM products WHERE category_id = :category_id ORDER BY id DESC");
    $product_statement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $product_statement->execute();
    $products = $product_statement->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Handle if category_id is not provided in the URL
    echo "Category ID not provided!";
    include 'includes/footer.php';
    exit();
}
?>

<main>
    <h2>Product list</h2>
    <ul class="products">
        <?php
        // Display products dynamically from the database
        foreach ($products as $product_row) {
            echo "<li>";
            echo '<img class="image" src="' . htmlspecialchars($product_row['banner_image']) . '"/>';

            echo "<a href='products_details.php?product_id=" . htmlspecialchars($product_row['id']) . "'><h3>" . htmlspecialchars($product_row['title']) . "</a></h3>";
            echo "<p>" . htmlspecialchars($product_row['description']) . "</p>";
            echo "<div class='price'>£" . htmlspecialchars($product_row['price']) . "</div>";
            echo "</li>";
        }
        ?>
    </ul>
    <hr />
</main>

<?php
include 'includes/footer.php';
?>
