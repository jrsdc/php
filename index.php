<?php
include 'includes/db_connection.php';
include 'includes/header.php';
?> 
   <section></section>
   <main>
      <h1>Welcome to Ed's Electronics</h1>
      <p>We stock a large variety of electrical goods including phones, TVs, computers, and games. Everything comes with at least a one-year guarantee and free next day delivery.</p>
      <hr>
      <h2>Product list</h2>
      <ul class="products">
         
         <?php
         // Fetch the last 10 products from the database
         $product_statement = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT 10");
         $product_statement->execute();
         $products = $product_statement->fetchAll(PDO::FETCH_ASSOC);

         foreach ($products as $product_row)
         {
            echo "<li>";
            echo '<img class="image" src="'.$product_row['banner_image'].'"/>';
            echo "<a href='products_details.php?product_id={$product_row['id']}'><h3>{$product_row['title']}</a></h3>";
            echo "<p>{$product_row['description']}</p>";
            echo "<div class='price'>£{$product_row['price']}</div>";
            echo "</li>";
         }
         ?>
      </ul>
      <hr />
   </main>
   <aside>
      <h1><a href="#">Featured Product</a></h1>
      <p><strong>Gaming PC</strong></p>
      <p>Brand new 8 core computer with an RTX 4080 </p>
   </aside>
 
<?php 
    include 'includes/footer.php';
?>
