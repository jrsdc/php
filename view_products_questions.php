<?php
include 'includes/db_connection.php';
include 'includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['email_address'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Initialize variable for error message
$answer_error = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_approved'])) {
    // Mark the question as approved
    $question_id = $_POST['question_id'];
    $approve_question_query = "UPDATE questions SET is_approved_by_admin = 'yes' WHERE id = :question_id";

    $stmt = $conn->prepare($approve_question_query);
    $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Question marked as approved!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validate form inputs
    $question_id = $_POST['question_id'];
    $admin_answer = $_POST['admin_answer'];

    // Validate admin answer
    if (empty($admin_answer)) {
        $answer_error = 'Admin answer is required';
    }

    // If there are no validation errors, update the question in the database
    if (empty($answer_error)) {
        $logged_in_user_id = $_SESSION['user_id'];

        $update_question_query = "UPDATE questions SET admin_answer = :admin_answer, answered_by_admin_id = :logged_in_user_id WHERE id = :question_id";

        $stmt = $conn->prepare($update_question_query);
        $stmt->bindParam(':admin_answer', $admin_answer, PDO::PARAM_STR);
        $stmt->bindParam(':logged_in_user_id', $logged_in_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "Answer submitted successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Determine the user type
$user_type = isset($_SESSION['type']) ? $_SESSION['type'] : '';

// Fetch questions based on the user type
$filter_condition = '';
if ($user_type === 'customer') {
    $customer_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $unanswered_condition = isset($_GET['filter']) && $_GET['filter'] == 'unanswered' ? " AND admin_answer IS NULL" : "";
    $filter_condition = "WHERE asked_by_user_id = :customer_id $unanswered_condition";
} elseif ($user_type === 'admin') {
    $filter_unanswered = isset($_GET['filter']) && $_GET['filter'] == 'unanswered';
    $filter_condition = $filter_unanswered ? "WHERE admin_answer IS NULL" : "";
}

// Fetch questions from the database based on the filter condition and join with users table
$questions_query = "SELECT questions.*, users.fullname AS answered_by_user_name
                    FROM questions
                    LEFT JOIN users ON questions.answered_by_admin_id = users.id
                    $filter_condition";
$stmt = $conn->prepare($questions_query);
if ($user_type === 'customer') {
    $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
}
$stmt->execute();
$questions_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
    <h1>View Questions</h1>
    <hr>

    <!-- Filter Form -->
    <form action="" method="get">
        <label>Filter Unanswered Questions: </label>
        <input type="checkbox" name="filter" value="unanswered" <?php echo isset($_GET['filter']) && $_GET['filter'] == 'unanswered' ? 'checked' : ''; ?>>
        <input type="submit" value="Apply Filter">
    </form>

    <h2>Answer Questions</h2>

    <?php
    echo "<table id='table'>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>User Name</th>";
    echo "<th>Question</th>";
    echo "<th>Answer</th>";
    echo "<th>Answered By</th>";
        
    if ($user_type === 'admin')
    {
        echo "<th>Post Answer</th>";
    }

    echo "</tr>";

    foreach ($questions_result as $question_row) {
        echo "<tr>";
        echo "<td>{$question_row['id']}</td>";
        echo "<td>{$question_row['user_name']}</td>";
        echo "<td>{$question_row['user_question']}</td>";
        echo "<td>{$question_row['admin_answer']}</td>";
        echo "<td>{$question_row['answered_by_user_name']}</td>";

        // Display the answer form only for admin users
        if ($user_type === 'admin') {
            echo "<td>";
            echo "<form action='' method='post'>";
            echo "<input type='text' name='admin_answer' value='{$question_row['admin_answer']}' />";
            echo "<input type='hidden' name='question_id' value='{$question_row['id']}' />";
            echo "<span class='error'>{$answer_error}</span>";
            echo "<input type='submit' name='submit' value='Submit Answer' />";
            echo "</form>";
            
            // Add button to mark question as approved
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='question_id' value='{$question_row['id']}' />";
            echo "<input type='submit' name='mark_approved' value='Mark as Approved' />";
            echo "</form>";

            echo "</td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
    ?>
</main>

<?php
include 'includes/footer.php';
?>
