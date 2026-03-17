<?php
session_start();
include("../config/db.php");

// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// get user weight
$wq = mysqli_query($conn, "SELECT weight FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($wq);
$weight = $user['weight'] ?? 0;

// recommended water (litres/day)
$recommended = round($weight * 0.033, 2);

// today water intake
$todayWater = 0;
$q = mysqli_query($conn,
    "SELECT water_litres FROM water_intake
     WHERE user_id='$user_id' AND intake_date = CURDATE()"
);

if ($row = mysqli_fetch_assoc($q)) {
    $todayWater = (float)$row['water_litres'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daily Water Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            width:520px;
            padding:30px;
            border-radius:14px;
            box-shadow:0 15px 30px rgba(0,0,0,0.15);
            text-align:center;
        }
        a{
            display:block;
            margin-top:15px;
            text-decoration:none;
            color:#333;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>💧 Daily Water Intake Report</h2>

    <p><b>Date:</b> <?php echo date("d-m-Y"); ?></p>
    <p><b>Recommended:</b> <?php echo $recommended; ?> litres</p>
    <p><b>Consumed:</b> <?php echo $todayWater; ?> litres</p>

    <canvas id="dailyLineChart"></canvas>

    <a href="../dashboard.php">← Back to Dashboard</a>
</div>

<script>
new Chart(document.getElementById('dailyLineChart'), {
    type: 'line',
    data: {
        labels: ['Recommended', 'Consumed'],
        datasets: [{
            label: 'Water (litres)',
            data: [<?php echo $recommended; ?>, <?php echo $todayWater; ?>],
            borderColor: '#2196F3',
            backgroundColor: 'rgba(33,150,243,0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 6
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Litres'
                }
            }
        }
    }
});
</script>

</body>
</html>