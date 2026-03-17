<?php
session_start();
include("../config/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = mysqli_prepare(
$conn,
"SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt,"s",$email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) === 1){

$user = mysqli_fetch_assoc($result);

if(password_verify($password,$user['password'])){

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['email'] = $user['email'];

header("Location: ../dashboard.php");
exit;

}else{
$error = "Wrong password";
}

}else{
$error = "User not found";
}

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body class="login-body">

<div class="login-box">
    <h2>Login</h2>

    <?php if ($error != "") { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p class="link">
        New user? <a href="register.php">Register here</a>
    </p>
</div>

</body>
</html>