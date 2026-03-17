<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$answer = "";

if (isset($_POST['question'])) {

    $question = strtolower(trim($_POST['question']));
    $found = false;

    $sql = "SELECT food_name, ingredients, preparation FROM recipes";
    $res = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($res)) {
        $food = strtolower($row['food_name']);

        if (strpos($question, $food) !== false) {
            $answer = "
                <b>🍽 Food:</b> {$row['food_name']}<br><br>
                <b>🧂 Ingredients:</b><br>
                {$row['ingredients']}<br><br>
                <b>👩‍🍳 Preparation:</b><br>
                {$row['preparation']}
            ";
            $found = true;
            break;
        }
    }

    if (!$found) {
        $answer = "😕 Sorry! Indha food recipe en database-la illa.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recipe Chatbot</title>
    <link rel="stylesheet" href="../assets/css/chatbot.css">
</head>
<body>

<div class="chat-box">
    <h2>🤖 Recipe Help Chatbot</h2>

    <form method="POST">
        <input type="text" name="question"
               placeholder="Eg: Idli seiya epdi? / Rasam recipe"
               required>
        <button type="submit">Ask</button>
    </form>

    <?php if ($answer != "") { ?>
        <div class="reply"><?php echo $answer; ?></div>
    <?php } ?>

    <a class="back" href="../dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>