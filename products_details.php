<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if product_id is provided in the URL
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product details from the database
    $product_query = "SELECT * FROM products WHERE id = :product_id";
    $stmt = $conn->prepare($product_query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    $product_row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product_row) {
        // Handle if the product with the specified ID is not found
        echo "Product not found!";
        include 'includes/footer.php';
        exit();
    }
} else {
    // Handle if product_id is not provided in the URL
    echo "Product ID not provided!";
    include 'includes/footer.php';
    exit();
}

// Initialize variable for error message
$name_error = '';
$question_error = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $user_name = htmlspecialchars($_POST['user_name']);
    $user_question = htmlspecialchars($_POST['user_question']);
    $userid = NULL;

    // Validate user name only if the user is not logged in or is not a customer
    if (isset($_SESSION['logged_in']) && $_SESSION['type'] == 'customer') {
        $user_name = $_SESSION['fullname'];
        $userid = $_SESSION['user_id'];
    } elseif (empty($_POST['user_name'])) {
        $name_error = 'Your Name is required';
    }

    // Validate user question
    if (empty($user_question)) {
        $question_error = 'Your Question is required';
    }

    $submitted_on = date('Y-m-d H:i:s');

    // If there are no validation errors, insert the question into the database
    if (empty($question_error) && empty($name_error)) {
        if ($userid != null) {
            $insert_question_query = "INSERT INTO questions (product_id, user_name, user_question, submitted_on, asked_by_user_id) 
                                      VALUES (:product_id, :user_name, :user_question, :submitted_on, :userid)";
        } else {
            $insert_question_query = "INSERT INTO questions (product_id, user_name, user_question, submitted_on) 
                                      VALUES (:product_id, :user_name, :user_question, :submitted_on)";
        }

        $stmt = $conn->prepare($insert_question_query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':user_question', $user_question);
        $stmt->bindParam(':submitted_on', $submitted_on);
        if ($userid != null) {
            $stmt->bindParam(':userid', $userid);
        }
        if ($stmt->execute()) {
            echo "Question submitted successfully!";
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}

// Fetch latest questions related to the product
$latest_questions_query = "SELECT * FROM questions WHERE product_id = :product_id AND is_approved_by_admin = 'yes' ORDER BY id DESC ";
$stmt = $conn->prepare($latest_questions_query);
$stmt->bindParam(':product_id', $product_id);
$stmt->execute();
$latest_questions_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h2>Product Page</h2>
    <h3><?php echo $product_row['title']; ?> </h3>

    <div class="price">£<?php echo $product_row['price'] ?></div>

    <img class="image" src="<?php echo $product_row['banner_image'] ?>"/>

    <h4>Product details</h4>
    <p><b>Manufacturer:</b> <?php echo $product_row['manufacturer']; ?></p>
    <br>
    <p><?php echo $product_row['description']; ?></p>
    <hr>

    <h4>Product reviews</h4>

    <ul class="reviews">
        <?php
        foreach ($latest_questions_result as $question_row) {
            echo "<li>";
            echo "<p>{$question_row['user_question']}</p>";
            echo "<br>";
            echo "<p class='answer'>{$question_row['admin_answer']}</p>";
            echo "<div class='details'>";
            echo "<strong>{$question_row['user_name']} </strong>";
            echo "<em>" . $question_row['submitted_on'] . "</em>";
            echo "</div>";
            echo "</li>";
        }
        ?>
    </ul>

    <h2>Ask your Question</h2>

    <form action="" method="post" enctype="multipart/form-data">
        <table id="table">
            <tr>
                <td><label>Your Name <span class="error"><?php echo $name_error; ?></span></label></td>
                <td>
                    <?php
                    if (isset($_SESSION['logged_in']) && $_SESSION['type'] === 'customer') {
                        // If the user is logged in as a customer, make the field readonly
                        ?>
                        <input readonly style="width: 100%;" type="text" name="user_name" value="<?php echo $_SESSION['fullname']; ?>" />
                        <?php
                    } else {
                        // If the user is not logged in or not a customer, display a regular input field
                        ?>
                        <input style="width: 100%;" type="text" name="user_name" value="" />
                        <?php
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <td><label>Your Question <span class="error"><?php echo $question_error; ?></span></label></td>
                <td><input style="width: 100%;" type="text" name="user_question" value="" /></td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="submit" name="submit" value="Submit" />
                </td>
            </tr>
        </table>
    </form>

</main>

<?php
include 'includes/footer.php';
?>
