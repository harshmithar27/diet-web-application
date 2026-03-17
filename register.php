<?php
// DB connection
$conn = mysqli_connect("localhost", "root", "", "diet_db");

if (!$conn) {
    die("DB Connection Failed: " . mysqli_connect_error());
}

// Form submit aana apram
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $age      = $_POST['age'];
    $weight   = $_POST['weight'];
    $goal     = $_POST['goal'];

    // STATE completely removed
    $sql = "INSERT INTO users (name, email, password, age, weight, goal)
            VALUES ('$name', '$email', '$password', '$age', '$weight', '$goal')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration Successful');</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <!-- CSS split panniyachu -->
    <link rel="stylesheet" href="../assets/css/register.css">
</head>

<body>

<div class="register-card">
    <h2>📝 Register</h2>

    <form method="post">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="number" name="age" placeholder="Age" required>
        <input type="number" step="0.1" name="weight" placeholder="Weight (kg)" required>
        <input type="text" name="goal" placeholder="Goal" required>
        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        Already have an account?
        <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>