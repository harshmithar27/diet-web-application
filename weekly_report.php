<?php
$conn = new mysqli("localhost", "root", "", "diet_db");
if ($conn->connect_error) die("DB Error");

$today = date('Y-m-d');

$meal_times = [
    "Early Morning",
    "Breakfast",
    "Mid Morning",
    "Lunch",
    "Evening Snack",
    "Dinner"
];

// YES & NO separate arrays
$yes_data = array_fill_keys($meal_times, 0);
$no_data  = array_fill_keys($meal_times, 0);

$sql = "
SELECT meal_time, consume_status, SUM(calories) AS total_calories
FROM user_food_intake
WHERE intake_date = '$today'
GROUP BY meal_time, consume_status
";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {

    if ($row['consume_status'] == 'yes') {
        $yes_data[$row['meal_time']] = (int)$row['total_calories'];
    }

    if ($row['consume_status'] == 'no') {
        $no_data[$row['meal_time']] = (int)$row['total_calories'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Daily Intake Report</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    background:#f4f6f8;
    font-family: Arial;
}

.card{
    width:600px;
    margin:60px auto;
    background:#fff;
    padding:30px;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.12);
}

.header{
    text-align:center;
    margin-bottom:20px;
}

.header h2{
    margin:0;
    font-size:22px;
}

.header span{
    font-size:15px;
    color:#555;
}

canvas{
    height:320px !important;
}

.back{
    text-align:center;
    margin-top:20px;
}

.back a{
    text-decoration:none;
    color:#1976d2;
    font-size:15px;
}
</style>
</head>

<body>

<div class="card">
    <div class="header">
        <h2>🍽 Daily Intake Report</h2>
        <span>Date: <?php echo $today; ?></span>
    </div>

    <canvas id="dailyChart"></canvas>

    <div class="back">
        <a href="../dashboard.php">← Back to Dashboard</a>
    </div>
</div>

<script>
new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($yes_data)); ?>,
        datasets: [
            {
                label: "Consumed (YES)",
                data: <?php echo json_encode(array_values($yes_data)); ?>,
                backgroundColor: '#42a5f5',
                borderRadius: 6
            },
            {
                label: "Not Consumed (NO)",
                data: <?php echo json_encode(array_values($no_data)); ?>,
                backgroundColor: '#ef5350',
                borderRadius: 6
            }
        ]
    },
    options: {
        scales: {
            y: {
                beginAtZero:true,
                max:1500,
                title:{ display:true, text:'Calories' }
            }
        }
    }
});
</script>

</body>
</html>