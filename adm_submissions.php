<?php
session_start();

// Check if the user is logged in and if isAdmin is set in the session
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: index.php");
    exit();
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

// Fetch questions
$sql_questions = "SELECT id, question_text FROM quiz_questions";
$result_questions = $conn->query($sql_questions);

// Fetch users' submissions grouped by question
$grouped_submissions = array();
while ($question = $result_questions->fetch_assoc()) {
    $question_id = $question['id'];
    $question_text = $question['question_text'];
    
    $sql_submissions = "SELECT CONCAT(u.firstname, ' ', u.lastname) AS full_name, ua.user_answer 
                        FROM quiz_user_answers ua 
                        JOIN quiz_users u ON ua.user_id = u.id 
                        WHERE ua.question_id = $question_id";
    $result_submissions = $conn->query($sql_submissions);
    
    $submissions = array();
    while ($submission = $result_submissions->fetch_assoc()) {
        $submissions[] = $submission;
    }

    $grouped_submissions[$question_text] = $submissions;
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
    <title><?php echo $page_title; ?> - Submissions</title>
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
<?php $pageID = 'submissions'; ?>
<!-- Include modals -->    
<?php include 'modals.php'; ?>

<?php include 'navigation.php' ?>

<div class="container">
    <h1 class="text-center"><?php echo $language['langSubmissionsPageTitle']; ?></h1>
    <?php foreach ($grouped_submissions as $question_text => $submissions): ?>
        <div class="question-box">
            <p class="question-text">&ldquo;<?php echo $question_text; ?>&rdquo;</p>
            <table>
                <thead>
                    <tr>
                        <th><?php echo $language['langSubmissionsUser']; ?></th>
                        <th><?php echo $language['langSubmissionsAnswer']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td><?php echo $submission['full_name']; ?></td>
                            <td><?php echo $submission['user_answer']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>

<!-- Load user information Modal -->            
<?php echo $userInfoModalContent; ?>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

