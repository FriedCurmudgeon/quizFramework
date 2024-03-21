<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: index.php");
    exit();
}

// Include language file
include ('./config/lang.php');

// Set language to English by default
$language = $english;

// Check if the selected language is Norwegian
if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'norwegian') {
    $language = $norwegian;
}

// Check if the user is an admin
$isAdmin = $_SESSION['isAdmin'];

// Database connection
include ('./config/connect.php');

// Fetch user information
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT firstname, lastname, username FROM quiz_users WHERE id = $user_id";
$result_user = $conn->query($sql_user);
$user_info = $result_user->fetch_assoc();

// Handle form submission for adding a question
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    // Process submitted question details
    $question_intro = isset($_POST['question_intro']) ? $_POST['question_intro'] : '';
    $question_text = $_POST['question_text'];
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $isActive = isset($_POST['is_active']) && $_POST['is_active'] == 1 ? 1 : 0; // Check if the checkbox is checked

    // Insert question with start and end dates into the database
    $sql = "INSERT INTO quiz_questions (question_intro, question_text, question_start_date, question_end_date, isActive) 
            VALUES ('$question_intro', '$question_text', '$start_date', '$end_date', '$isActive')";
    if ($conn->query($sql) === TRUE) {
        echo "Question added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Set session variable for success message
    $_SESSION['add_quiz_success_message'] = $language['langAddQuestionSuccess'];
    header("Location: adm_addQuestion.php"); // Redirect to clear POST data
    exit();
}

// Handle form submission for deleting a question
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    // Process submitted question ID for deletion
    $question_id = $_POST['question_id'];

    // Delete question from the database
    $sql = "DELETE FROM quiz_questions WHERE id = $question_id";
    if ($conn->query($sql) === TRUE) {
        echo "Question deleted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Set session variable for success message
    $_SESSION['delete_quiz_success_message'] = $language['langDeleteQuestionSuccess'];
    header("Location: adm_addQuestion.php"); // Redirect to clear POST data
    exit();
}

// Handle form submission for editing a question
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    // Process submitted question ID, intro, text, start date, and end date for editing
    $question_id = $_POST['question_id'];
    $question_intro = isset($_POST['question_intro']) ? $_POST['question_intro'] : '';
    $question_text = $_POST['question_text'];
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $isActive = isset($_POST['is_active']) && $_POST['is_active'] == 1 ? 1 : 0;

    // Update question in the database
    $sql = "UPDATE quiz_questions 
            SET question_intro = '$question_intro', question_text = '$question_text', 
                question_start_date = '$start_date', question_end_date = '$end_date', isActive = '$isActive'
            WHERE id = $question_id";
    if ($conn->query($sql) === TRUE) {
        echo "Question edited successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Set session variable for success message
    $_SESSION['edit_quiz_success_message'] = $language['langEditQuestionSuccess'];
    header("Location: adm_addQuestion.php"); // Redirect to clear POST data
    exit();
}

// Fetch questions from the database
$sql = "SELECT id, question_intro, question_text, question_start_date, question_end_date, isActive FROM quiz_questions";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $questions = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $questions = [];
}

// fetch page title and favicon from db
include ('./config/fetch_sitewide_content_db.php');

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Manage questions</title>
    <link rel="icon" href="data:image/svg+xml,<?php echo htmlspecialchars($favicon_url); ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .question-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .question-text {
            font-style: italic;
            color: #6c757d; /* Text color similar to Bootstrap's muted color */
        }
        .question-intro {
            font-style: italic;
            font-weight: bold;
            color: #6c757d; /* Text color similar to Bootstrap's muted color */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<?php $pageID = 'manageQuestions'; ?>
<!-- Include modals -->    
<?php include 'modals.php'; ?>

<?php include 'navigation.php' ?>

<div class="container">
    <h1 class="text-center"><?php echo $language['langAddQuestionPageTitle']; ?></h1>
    <!-- Add Question Form -->
<h3><?php echo $language['langAddQuestionFormTitle']; ?>:</h3>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="action" value="add">
    <div class="form-group">
        <label for="question_intro"><?php echo $language['langAddQuestionIntroLabel']; ?></label>
        <textarea class="form-control" id="question_intro" name="question_intro" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="question_text"><?php echo $language['langAddQuestionFormLabel']; ?></label>
        <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
    </div>
    <div class="form-group">
        <label for="start_date"><?php echo $language['langAddQuestionStartDateLabel']; ?></label>
        <input type="date" class="form-control" id="start_date" name="start_date">
    </div>
    <div class="form-group">
        <label for="end_date"><?php echo $language['langAddQuestionEndDateLabel']; ?></label>
        <input type="date" class="form-control" id="end_date" name="end_date">
    </div>
    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1">
        <label class="form-check-label" for="is_active"><?php echo $language['langQuestionActiveCheckboxLabel']; ?></label>
    </div>
    <button type="submit" class="btn btn-primary"><?php echo $language['langAddQuestionFormButton']; ?></button>
</form>


    <hr>

    <!-- List of Questions -->
    <?php foreach ($questions as $question): ?>
        <div class="question-box">
        <h3><?php echo $language['langEditQuestionFormTitle']; ?>:</h3>
            <p class="question-intro"><?php echo $question['question_intro']; ?></p>
            <p class="question-text"><?php echo $question['question_text']; ?></p>
            <!-- Form for editing a question -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                <div class="form-group">
                    <label for="edited_question_intro"><?php echo $language['langEditQuestionIntroLabel']; ?></label>
                    <textarea class="form-control" id="edited_question_intro" name="question_intro" rows="3"><?php echo $question['question_intro']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="edited_question_text"><?php echo $language['langEditQuestionFormLabel']; ?></label>
                    <textarea class="form-control" id="edited_question_text" name="question_text" rows="3" required><?php echo $question['question_text']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="edited_start_date"><?php echo $language['langAddQuestionStartDateLabel']; ?></label>
                    <input type="date" class="form-control" id="edited_start_date" name="start_date" value="<?php echo $question['question_start_date']; ?>">
                </div>
                <div class="form-group">
                    <label for="edited_end_date"><?php echo $language['langAddQuestionEndDateLabel']; ?></label>
                    <input type="date" class="form-control" id="edited_end_date" name="end_date" value="<?php echo $question['question_end_date']; ?>">
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="edited_is_active" name="is_active" value="1" <?php echo $question['isActive'] == 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="edited_is_active"><?php echo $language['langQuestionActiveCheckboxLabel']; ?></label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><?php echo $language['langEditQuestionFormButton']; ?></button>
            </form>

            <!-- Form for deleting a question -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                <button type="submit" class="btn btn-danger btn-sm"><?php echo $language['langDeleteQuestionButton']; ?></button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?php echo $editSuccessModal; ?>
<?php echo $addSuccessModal; ?>
<?php echo $deleteSuccessModal; ?>

<!-- Load user information Modal -->            
<?php echo $userInfoModalContent; ?>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Show success modal if session variable is set
    <?php if (isset($_SESSION['edit_quiz_success_message'])): ?>
    $('#editSuccessModal').modal('show');
    <?php unset($_SESSION['edit_quiz_success_message']); // Clear session variable ?>
    <?php endif; ?>
});

$(document).ready(function() {
    // Show success modal if session variable is set
    <?php if (isset($_SESSION['add_quiz_success_message'])): ?>
    $('#addSuccessModal').modal('show');
    <?php unset($_SESSION['add_quiz_success_message']); // Clear session variable ?>
    <?php endif; ?>
});

$(document).ready(function() {
    // Show success modal if session variable is set
    <?php if (isset($_SESSION['delete_quiz_success_message'])): ?>
    $('#deleteSuccessModal').modal('show');
    <?php unset($_SESSION['delete_quiz_success_message']); // Clear session variable ?>
    <?php endif; ?>
});
</script>

</body>
</html>
