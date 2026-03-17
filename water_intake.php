<?php
session_start();
include("../config/db.php");

// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// assume weight already in users table
$weightQuery = mysqli_query($conn, "SELECT weight FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($weightQuery);
$weight = $user['weight'] ?? 0;

// recommended water (simple formula)
$recommended = round($weight * 0.033, 2); // litres/day

// save water intake
if (isset($_POST['save'])) {

    $water = $_POST['water'];

    // check today entry
    $check = mysqli_query($conn,
        "SELECT id FROM water_intake 
         WHERE user_id='$user_id' AND intake_date=CURDATE()"
    );

    if (mysqli_num_rows($check) > 0) {

        // update
        mysqli_query($conn,
            "UPDATE water_intake 
             SET water_litres='$water'
             WHERE user_id='$user_id' AND intake_date=CURDATE()"
        );

    } else {

        // insert
        mysqli_query($conn,
            "INSERT INTO water_intake (user_id, water_litres, intake_date)
             VALUES ('$user_id','$water',CURDATE())"
        );
    }

    echo "<script>alert('Water intake saved successfully');</script>";
}

// fetch today water
$todayWater = 0;
$res = mysqli_query($conn,
    "SELECT water_litres FROM water_intake 
     WHERE user_id='$user_id' AND intake_date=CURDATE()"
);
if ($row = mysqli_fetch_assoc($res)) {
    $todayWater = $row['water_litres'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Water Tracker</title>
    <style>
        body{
            margin:0;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#eef2f3;
            font-family: Arial, sans-serif;
        }
        .card{
            background:#fff;
            width:380px;
            padding:30px;
            border-radius:14px;
            box-shadow:0 15px 30px rgba(0,0,0,0.15);
            text-align:center;
        }
        h2{
            margin-bottom:15px;
        }
        input{
            width:100%;
            padding:12px;
            margin-top:10px;
            border-radius:8px;
            border:1px solid #ccc;
        }
        button{
            width:100%;
            padding:12px;
            margin-top:15px;
            background:#2196F3;
            color:#fff;
            border:none;
            border-radius:8px;
            font-size:16px;
            cursor:pointer;
        }
        button:hover{
            background:#1e88e5;
        }
        .back{
            display:block;
            margin-top:15px;
            text-decoration:none;
            color:#333;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>💧 Water Tracker</h2>

    <p><b>Your weight:</b> <?php echo $weight; ?> kg</p>
    <p><b>Recommended Water:</b> <?php echo $recommended; ?> litres/day</p>

    <form method="post">
        <input type="number" step="0.1" name="water"
               placeholder="Water consumed today (litres)"
               value="<?php echo $todayWater; ?>" required>

        <button type="submit" name="save">Save</button>
    </form>

    <a href="../dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
