<?php 
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT name, email, age, weight, goal FROM users WHERE id = $user_id LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

/* -------------------------
   STEP 2.2 DIET STREAK COUNT
--------------------------*/

$email = $user['email'];

$streakQuery = mysqli_query($conn,
"SELECT COUNT(*) as total
 FROM diet_streak
 WHERE email='$email'");

$streakData = mysqli_fetch_assoc($streakQuery);

$streakCount = $streakData['total'];

/* existing STEP-2 include */
include("climate_craving_tips.php");

?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link rel="stylesheet" href="assets/css/dashboard.css">
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<script>
  window.OneSignalDeferred = window.OneSignalDeferred || [];
  OneSignalDeferred.push(async function(OneSignal) {
    await OneSignal.init({
      appId: "2acaf27b-23b1-4a6b-85fb-7a16532a5228",
    });
  });
</script>

</head>

<body class="dashboard-body">

<div class="dashboard-wrapper">

<h2 class="welcome">Welcome <?php echo $user['name']; ?> 🎉</h2>

<p class="goal">Goal: <?php echo $user['goal']; ?></p>

<!-- DASHBOARD GRID -->
<div class="dashboard-grid">

<a href="user/diet_plan.php" class="card">
<div class="icon">🥗</div>
<h3>Vegan Diet Plan</h3>
<p>Personalized daily meal plans</p>
</a>

<a href="user/weekly_report.php" class="card">
<div class="icon">📊</div>
<h3>Diet Weekly Report</h3>
<p>Track your food progress</p>
</a>

<a href="user/water_intake.php" class="card">
<div class="icon">💧</div>
<h3>Water Tracker</h3>
<p>Daily water recommendation</p>
</a>

<a href="user/water_weekly_report.php" class="card">
<div class="icon">📈</div>
<h3>Water Weekly Report</h3>
<p>Weekly hydration summary</p>
</a>

<a href="user/recipe_chatbot.php" class="card">
<div class="icon">🤖</div>
<h3>Recipe Chatbot</h3>
<p>Healthy recipe suggestions</p>
</a>

<a href="profile.php" class="card">
<div class="icon">👤</div>
<h3>View Profile</h3>
<p>Manage your personal info</p>
</a>

<!-- STEP 2.3 DIET STREAK CARD -->
<div class="card">
<div class="icon">🔥</div>
<h3>Diet Streak</h3>
<p><?php echo $streakCount; ?> Days</p>
</div>

</div>

</div>

</body>
</html>