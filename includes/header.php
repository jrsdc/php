<?php 
   session_start();
   include 'includes/db_connection.php';
   // Fetch all categories from the database
   $category_query = "SELECT * FROM categories";
   $category_result = $conn->query($category_query);
?>

<!doctype html>
<html>
   <head>
      <title>Ed's Electronics</title>
      <meta charset="utf-8" />
      <link rel="stylesheet" href="assets/electronics.css" />
   </head>
   <body>
      <header>
         <h1>Ed's Electronics</h1>
         <ul>
            <li><a href="index.php">Home</a></li>
            <li>
               Products
               <ul>
                  <?php
                  // Display product products dynamically from the database
                  $category_statement = $conn->prepare("SELECT * FROM categories");
                  $category_statement->execute();
                  $categories = $category_statement->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($categories as $category_row) {
                     echo "<li><a href='products.php?category_id={$category_row['id']}'>{$category_row['name']}</a></li>";
                  }
                  ?>
               </ul>
            </li>
            <?php
            // Check if the user is logged in
            if(isset($_SESSION['logged_in'])) 
            {
            ?>
               <li>
                  My Account
                  <ul>
                     <?php 
                     if(isset($_SESSION['type']) && $_SESSION['type'] == "admin" ) 
                     {
                        $admin_menu = "<li><a href='view_categories_as_admin.php'>View All Categories</a></li>
                        <li><a href='view_products_as_admin.php'>View Products</a></li>
                        <li><a href='view_admins_as_admin.php'>View Admins</a></li>";
                        echo $admin_menu;
                     }
                     ?>
                        <li><a href="view_products_questions.php">Product Questions</a></li>
                     <li><a href="logout.php">Logout</a></li>
                  </ul>
               </li>
            <?php 
            }
            else 
            {
            ?>
               <li><a href="login.php">Login</a></li>
               <!-- <li><a href="signup.php">Signup</a></li> -->
            <?php 
            }
            ?>
         </ul>
         <address>
            <p>We are open 9-5, 7 days a week. Call us on
               <strong>01604 11111</strong>
            </p>
         </address>
      </header>
