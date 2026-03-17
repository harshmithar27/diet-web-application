<?php
session_start();

/* ✅ correct include path */
include __DIR__ . "/config/db.php";

/* security */
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* fetch user data */
$sql = "SELECT name, email, age, weight, goal FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>

<div class="profile-box">
    <h2>My Profile</h2>

    <p><b>Name:</b> <?php echo $user['name']; ?></p>
    <p><b>Email:</b> <?php echo $user['email']; ?></p>
    <p><b>Age:</b> <?php echo $user['age']; ?></p>
    <p><b>Weight:</b> <?php echo $user['weight']; ?> kg</p>
    <p><b>Goal:</b> <?php echo $user['goal']; ?></p>
    <br>
    <a href="logout.php" class="logout">Logout</a>
    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>