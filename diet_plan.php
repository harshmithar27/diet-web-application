<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

/* check login */
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","diet_db");

if(!$conn){
    die("DB Connection Failed");
}

/* logged user */
$user_id = $_SESSION['user_id'];
$email   = $_SESSION['email'];

$today = date("Y-m-d");

/* -------------------------
   AUTO DAY
--------------------------*/

$u = mysqli_query($conn,"SELECT diet_start_date FROM users WHERE id='$user_id'");
$urow = mysqli_fetch_assoc($u);

$start_date = $urow['diet_start_date'] ?? date("Y-m-d");

$diff = (strtotime($today) - strtotime($start_date)) / 86400;

$day = floor($diff) + 1;

if($day < 1) $day = 1;
if($day > 30) $day = 30;


/* -------------------------
   GET DIET PLAN
--------------------------*/

$dietPlan = [];

$q = mysqli_query($conn,
"SELECT meal_time, food_items
 FROM diet_30_days
 WHERE day_number='$day'
 ORDER BY id ASC");

while($r = mysqli_fetch_assoc($q)){

$dietPlan[$r['meal_time']] = $r['food_items'];

}


/* -------------------------
   DAILY CALORIES
--------------------------*/

$calculated = false;
$userCalories = 0;

$cq = mysqli_query($conn,
"SELECT calories
 FROM user_daily_calories
 WHERE email='$email'
 AND calorie_date='$today'");

if(mysqli_num_rows($cq) > 0){

$cr = mysqli_fetch_assoc($cq);
$userCalories = $cr['calories'];
$calculated = true;

}


/* calculate calories */

if(isset($_POST['action']) && $_POST['action']=="calculate" && !$calculated){

$w = $_POST['weight'];
$h = $_POST['height'];
$a = $_POST['age'];
$g = $_POST['gender'];
$activity = $_POST['activity'];
$goal = $_POST['goal'];

/* BMR calculation */

if($g=="female"){
$bmr = (10*$w)+(6.25*$h)-(5*$a)-161;
}else{
$bmr = (10*$w)+(6.25*$h)-(5*$a)+5;
}

/* Activity multiplier */

if($activity=="sedentary"){
$tdee = $bmr * 1.2;
}
elseif($activity=="light"){
$tdee = $bmr * 1.375;
}
elseif($activity=="moderate"){
$tdee = $bmr * 1.55;
}
else{
$tdee = $bmr * 1.725;
}

/* Goal adjustment */

if($goal=="weight_loss"){
$userCalories = $tdee - 500;
}
elseif($goal=="weight_gain"){
$userCalories = $tdee + 500;
}
else{
$userCalories = $tdee;
}

/* Age group calorie limits */

if($a >= 2 && $a <= 8){

if($g=="male"){
$min = 1000;
$max = 2000;
}else{
$min = 1000;
$max = 1800;
}

}

elseif($a >= 9 && $a <= 18){

if($g=="male"){
$min = 1600;
$max = 3200;
}else{
$min = 1400;
$max = 2400;
}

}

elseif($a >= 19 && $a <= 60){

if($g=="male"){
$min = 2200;
$max = 3000;
}else{
$min = 1600;
$max = 2400;
}

}

else{

if($g=="male"){
$min = 2000;
$max = 2600;
}else{
$min = 1600;
$max = 2000;
}

}

/* Minimum calories protection */

if($userCalories < $min){
$userCalories = $min;
}

$userCalories = round($userCalories);

/* Save calories */

mysqli_query($conn,
"INSERT INTO user_daily_calories(email, calorie_date, calories)
VALUES('$email','$today','$userCalories')");

$calculated = true;

}


/* -------------------------
   SAVE FOOD INTAKE
--------------------------*/

if(isset($_POST['action']) && $_POST['action']=="save"){

$meal = $_POST['meal'];
$food = $_POST['food'];
$cal  = $_POST['cal'];
$consume = $_POST['consume'];

$chk = mysqli_query($conn,
"SELECT id
 FROM user_food_intake
 WHERE user_id='$user_id'
 AND intake_date='$today'
 AND meal_time='$meal'");

if(mysqli_num_rows($chk) == 0){

$insertFood = mysqli_query($conn,
"INSERT INTO user_food_intake
(user_id, food_item, calories, intake_date, meal_time, consume_status)
VALUES
('$user_id','$food','$cal','$today','$meal','$consume')");

/* save streak only if consumed */

if($insertFood && ($consume)=="yes"){

$checkStreak = mysqli_query($conn,
"SELECT id FROM diet_streak
WHERE email='$email'
AND streak_date='$today'");

if(mysqli_num_rows($checkStreak)==0){

mysqli_query($conn,
"INSERT INTO diet_streak(email, streak_date, status)
VALUES('$email','$today','followed')");

}

}

echo "<script>alert('Meal saved ($consume)');</script>";

}else{

echo "<script>alert('Already saved for this meal today');</script>";

}

}


/* -------------------------
   CALORIE DISTRIBUTION
--------------------------*/

$distribution = [

"Early Morning" => 0.10,
"Breakfast" => 0.25,
"Mid Morning" => 0.10,
"Lunch" => 0.30,
"Evening Snack" => 0.10,
"Dinner" => 0.15

];

?>
<!DOCTYPE html>
<html>
<head>

<title>Day <?php echo $day; ?> – Diet Plan</title>

<style>

body{
font-family:Arial;
background:#eef2f3;
padding:20px;
}

.container{
max-width:1000px;
margin:auto;
}

h2{
text-align:center;
color:#2e7d32;
}

.card{
background:#fff;
padding:20px;
border-radius:10px;
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#4CAF50;
color:#fff;
padding:10px;
}

td{
padding:10px;
border-bottom:1px solid #ddd;
text-align:center;
}

.total{
background:#2e7d32;
color:#fff;
font-weight:bold;
}

.btn{
background:#4CAF50;
color:#fff;
border:none;
padding:6px 12px;
border-radius:4px;
}

.back-btn{
display:inline-block;
margin-top:20px;
background:#1976d2;
color:#fff;
padding:10px 16px;
text-decoration:none;
border-radius:5px;
}

</style>
</head>

<body>

<div class="container">

<h2>🥗 Day <?php echo $day; ?> – Diet Plan</h2>

<?php if(!$calculated){ ?>

<div class="card">

<form method="post">

<input type="hidden" name="action" value="calculate">

Weight
<input type="number" name="weight" required><br><br>

Height
<input type="number" name="height" required><br><br>

Age
<input type="number" name="age" required><br><br>

Gender

<select name="gender">
<option value="female">Female</option>
<option value="male">Male</option>
</select>

<br><br>

Activity Level

<select name="activity">
<option value="sedentary">Sedentary (No exercise)</option>
<option value="light">Lightly Active (1-3 days exercise)</option>
<option value="moderate">Moderately Active (3-5 days exercise)</option>
<option value="very">Very Active (Hard exercise)</option>
</select>

<br><br>

Goal

<select name="goal">
<option value="maintain">Maintain Weight</option>
<option value="weight_loss">Weight Loss</option>
<option value="weight_gain">Weight Gain</option>
</select>

<br><br>

<button class="btn">Calculate Calories</button>

</form>

</div>

<?php } ?>

<?php if($calculated){ ?>

<div class="card">

<b>Your Daily Calorie Requirement:</b>

<?php echo $userCalories; ?> kcal

</div>

<div class="card">

<table>

<tr>
<th>Meal Time</th>
<th>Food Item</th>
<th>Calories</th>
<th>Consumed?</th>
</tr>

<?php

$total = 0;

foreach($dietPlan as $meal=>$food){

$percent = $distribution[$meal] ?? 0.15;

$cal = round($userCalories * $percent);

$total += $cal;

?>

<tr>

<td><?php echo $meal; ?></td>
<td><?php echo $food; ?></td>
<td><?php echo $cal; ?></td>

<td>

<form method="post">

<input type="hidden" name="action" value="save">
<input type="hidden" name="meal" value="<?php echo $meal; ?>">
<input type="hidden" name="food" value="<?php echo $food; ?>">
<input type="hidden" name="cal" value="<?php echo $cal; ?>">

<select name="consume">
<option value="yes">YES</option>
<option value="no">NO</option>
</select>

<button class="btn">Save</button>

</form>

</td>

</tr>

<?php } ?>

<tr class="total">
<td colspan="2">Total Calories</td>
<td><?php echo $total; ?></td>
<td></td>
</tr>

</table>

<div style="text-align:center;">

<a href="../dashboard.php" class="back-btn">
⬅ Back to Dashboard
</a>

</div>

</div>

<?php } ?>

</div>

</body>
</html>

