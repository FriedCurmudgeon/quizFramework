<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
include ('./config/connect.php');

// Fetch user information
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT firstname, lastname, username FROM quiz_users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_info = $result_user->fetch_assoc();
$stmt_user->close();

// Fetch the welcome message from quiz_settings table
$sql_welcome_message = "SELECT setting_value FROM quiz_settings WHERE setting_key = 'welcome_message'";
$result_welcome_message = $conn->query($sql_welcome_message);

$welcome_message = "";
if ($result_welcome_message->num_rows > 0) {
    $row = $result_welcome_message->fetch_assoc();
    $welcome_message = $row['setting_value'];
}

// Fetch active questions from the database within the given dates
$currentDate = date('Y-m-d');
$sql_active = "SELECT id, question_text, question_intro, question_start_date, question_end_date FROM quiz_questions 
        WHERE isActive = 1 
        AND question_start_date <= ? 
        AND (question_end_date >= ? OR question_end_date IS NULL)";
$stmt_active = $conn->prepare($sql_active);
$stmt_active->bind_param("ss", $currentDate, $currentDate);
$stmt_active->execute();
$result_active = $stmt_active->get_result();

$active_questions = [];
if ($result_active->num_rows > 0) {
    $active_questions = $result_active->fetch_all(MYSQLI_ASSOC);
}
$stmt_active->close();

// Fetch old questions from the database whose end date has passed
$sql_old = "SELECT id, question_text, question_intro, question_start_date, question_end_date FROM quiz_questions 
        WHERE isActive = 1 
        AND question_end_date IS NOT NULL 
        AND question_end_date < ?";
$stmt_old = $conn->prepare($sql_old);
$stmt_old->bind_param("s", $currentDate);
$stmt_old->execute();
$result_old = $stmt_old->get_result();

$old_questions = [];
if ($result_old->num_rows > 0) {
    $old_questions = $result_old->fetch_all(MYSQLI_ASSOC);
}
$stmt_old->close();

// Fetch user's previously submitted answers
$sql_answers = "SELECT question_id, user_answer FROM quiz_user_answers WHERE user_id = ?";
$stmt_answers = $conn->prepare($sql_answers);
$stmt_answers->bind_param("i", $user_id);
$stmt_answers->execute();
$result_answers = $stmt_answers->get_result();

$previous_answers = [];
if ($result_answers->num_rows > 0) {
    while ($row = $result_answers->fetch_assoc()) {
        $previous_answers[$row['question_id']] = $row['user_answer'];
    }
}
$stmt_answers->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process submitted answers
    $answers = $_POST['answers'];

    // Insert or update user answers into the database
    $sql_insert = "INSERT INTO quiz_user_answers (user_id, question_id, user_answer) VALUES (?, ?, ?)";
    $sql_update = "UPDATE quiz_user_answers SET user_answer = ? WHERE user_id = ? AND question_id = ?";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_update = $conn->prepare($sql_update);

    foreach ($answers as $question_id => $answer) {
        // Check if the user has already submitted an answer for this question
        $sql_check = "SELECT * FROM quiz_user_answers WHERE user_id = ? AND question_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $user_id, $question_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Update the existing answer
            $stmt_update->bind_param("sii", $answer, $user_id, $question_id);
            $stmt_update->execute();
        } else {
            // Insert a new answer
            $stmt_insert->bind_param("iis", $user_id, $question_id, $answer);
            $stmt_insert->execute();
        }
        $stmt_check->close();
    }

    $stmt_insert->close();
    $stmt_update->close();

    // Set session variable for success message
    $_SESSION['quiz_success_message'] = "Answers submitted successfully!";
    header("Location: quiz.php"); // Redirect to clear POST data
    exit();
}

// fetch page title and favicon from db
include ('./config/fetch_sitewide_content_db.php');

// Fetch the quiz name from quiz_settings table
$quiz_name = "Default Title"; // Default title in case setting is not found
$sql_quiz_name = "SELECT setting_value FROM quiz_settings WHERE setting_key = 'quiz_name'";
$result_quiz_name = $conn->query($sql_quiz_name);

if ($result_quiz_name->num_rows > 0) {
    $row = $result_quiz_name->fetch_assoc();
    $quiz_name = $row['setting_value'];
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="icon" href="data:image/svg+xml,<?php echo htmlspecialchars($favicon_url); ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<?php $pageID = 'quiz'; ?>
<!-- Include modals -->    
<?php include 'modals.php'; ?>

<!-- Navigation -->
<?php include 'navigation.php' ?>

<div class="container">
    <h1 class="text-center"><?php echo $quiz_name; ?></h1>

    <!-- Display welcome message -->
    <?php if (!empty($welcome_message)): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $welcome_message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <!-- Display active questions -->
        <?php foreach ($active_questions as $question): ?>
            <div class="question-box">
                <p><strong><?php echo $question['question_intro']; ?></strong></p>
                <p><?php echo $question['question_text']; ?></p>
                <textarea name="answers[<?php echo $question['id']; ?>]" class="form-control" rows="4" cols="50"><?php echo isset($previous_answers[$question['id']]) ? $previous_answers[$question['id']] : ''; ?></textarea>
                <!-- Question expiration notification -->
                <p class="question-exp-date-notice"><?php echo $language['langQuizEndDateMessage']; ?> 
                <?php 
        // Format the date for display
        $endDate = $question['question_end_date'];
        if ($endDate !== null) {
            $formattedEndDate = date($language['langQuizDateFormat'], strtotime($endDate));
            echo $formattedEndDate;
        } else {
            echo $language['langQuizNoEndDate']; // Message for questions without end date
        }
    ?>.</p>
            </div>
        <?php endforeach; ?>

        <!-- Display old questions with input fields deactivated -->
        <?php foreach ($old_questions as $question): ?>
            <div class="question-box">
                <p><strong><?php echo $question['question_intro']; ?></strong></p>
                <p><?php echo $question['question_text']; ?></p>
                <textarea name="answers[<?php echo $question['id']; ?>]" class="form-control" rows="4" cols="50" readonly><?php echo isset($previous_answers[$question['id']]) ? $previous_answers[$question['id']] : ''; ?></textarea>
                <!-- Question expiration notification -->
                <p class="question-exp-date-notice"><?php echo $language['langQuizPastEndDateMessage']; ?> 
                <?php 
        // Format the date for display
        $endDate = $question['question_end_date'];
        if ($endDate !== null) {
            $formattedEndDate = date($language['langQuizDateFormat'], strtotime($endDate));
            echo $formattedEndDate;
        } else {
            echo $language['langQuizNoEndDate']; // Message for questions without end date
        }
    ?>.</p>
            </div>
        <?php endforeach; ?>
        
        <button type="submit" class="btn btn-primary btn-block"><?php echo $language['langQuizSubmit']; ?></button>
    </form>
</div>

<?php echo $successModal; ?>

<!-- Load user information Modal -->            
<?php echo $userInfoModalContent; ?>


<script>
    $(document).ready(function(){
        <?php if (isset($_SESSION['quiz_success_message'])): ?>
            $('#successModal').modal('show');
            <?php // Unset the session variable to prevent the modal from appearing on page reload ?>
            <?php unset($_SESSION['quiz_success_message']); ?>
        <?php endif; ?>
    });
</script>

<!-- Cookie information bar -->
<?php include ('cookieInfo.php'); ?>
</body>
</html>
