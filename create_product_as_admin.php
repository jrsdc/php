<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email_address']) || $_SESSION['type'] !== 'admin') {
    // Redirect to the login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Initialize variables for error messages and product creation message
$product_created = '';
$title_error = $price_error = $category_error = $description_error = $banner_image_error = $manufacturer_error = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $title = htmlspecialchars($_POST['title']);
    $price = htmlspecialchars($_POST['price']);
    $description = htmlspecialchars($_POST['description']);
    $category = $_POST['category'];
    $manufacturer = htmlspecialchars($_POST['manufacturer']); // Added manufacturer field

    // Validate product title
    if (empty($title)) {
        $title_error = 'Product title is required';
    }

    // Validate product price
    if (empty($price)) {
        $price_error = 'Product price is required';
    } elseif (!is_numeric($price) || $price <= 0) {
        $price_error = 'Invalid price';
    }

    // Validate selected category
    if ($category == 'Select Category') {
        $category_error = 'Please select a category';
    }

    // Validate product description
    if (empty($description)) {
        $description_error = 'Product description is required';
    }

    // Validate manufacturer
    if (empty($manufacturer)) {
        $manufacturer_error = 'Manufacturer is required';
    }

    // Validate banner image
    if (!isset($_FILES['banner_image']['name']) || empty($_FILES['banner_image']['name'])) {
        $banner_image_error = 'Banner image is required';
    } else {
        // File upload logic
        $target_dir = "assets/images/"; // Specify your upload directory
        $target_file = $target_dir . rand() . basename($_FILES["banner_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the image file is a real image or fake image
        $check = getimagesize($_FILES["banner_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $banner_image_error = "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $banner_image_error = "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["banner_image"]["size"] > 500000) {
            $banner_image_error = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            $banner_image_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $banner_image_error = "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
                // File uploaded successfully, proceed with product insertion
                $insert_query = "INSERT INTO products (title, price, description, category_id, manufacturer, banner_image) 
                                 VALUES (:title, :price, :description, :category, :manufacturer, :banner_image)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $insert_stmt->bindParam(':price', $price, PDO::PARAM_STR);
                $insert_stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $insert_stmt->bindParam(':category', $category, PDO::PARAM_INT);
                $insert_stmt->bindParam(':manufacturer', $manufacturer, PDO::PARAM_STR);
                $insert_stmt->bindParam(':banner_image', $target_file, PDO::PARAM_STR);

                if ($insert_stmt->execute()) {
                    // Product created successfully
                    $product_created = 'Product is created';
                } else {
                    // Product creation failed, display the error
                    echo "Error: " . $conn->error;
                }
            } else {
                $banner_image_error = "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// Fetch categories from the database
$category_query = "SELECT * FROM categories";
$category_stmt = $conn->prepare($category_query);
$category_stmt->execute();
$category_result = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h1>Create Product!</h1>
    <hr />
    <?php echo $product_created; ?>
    <form action="" method="post" enctype="multipart/form-data">
        
        <table id="table">   
            <tr>
                <td><label>Product Name <span class="error"><?php echo $title_error; ?></span></label></td>
                <td><input type="text" name="title" value="" /></td>
            </tr>

            <tr>
                <td><label>Product Price <span class="error"><?php echo $price_error; ?></span></label></td>
                <td><input type="text" name="price" value="" /></td>
            </tr>

            <tr>
                <td><label>Manufacturer <span class="error"><?php echo $manufacturer_error; ?></span></label></td>
                <td><input type="text" name="manufacturer" value="" /></td>
            </tr>
            
            <tr>
                <td><label>Banner Image <span class="error"><?php echo $banner_image_error; ?></span></label></td>
                <td><input type="file" name="banner_image" value="" /></td>
            </tr>

            <tr>
                <td><label>Select Category <span class="error"><?php echo $category_error; ?></span></label></td>
                <td>
                    <select name="category">
                        <option>Select Category</option>
                        <?php
                        // Loop through the fetched categories and populate the dropdown
                        foreach ($category_result as $category_row) {
                            echo "<option value='{$category_row['id']}'>{$category_row['name']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td><label>Description <span class="error"><?php echo $description_error; ?></span></label></td>
                <td><textarea name="description"></textarea></td>
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
