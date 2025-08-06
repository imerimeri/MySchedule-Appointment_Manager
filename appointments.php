<?php
session_start();
include("database.php");

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['id'];

$sql = "SELECT name, surname, telephone, area, price, appointment_time 
        FROM appointments 
        WHERE user_id = ? 
        ORDER BY appointment_time ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Appointments</title>
    <link rel="stylesheet" href="appointments_style.css">
</head>
<body>
    <div class="container">
        <h2>Your Appointments</h2>

        <table>
            <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>Telephone</th>
                <th>Area</th>
                <th>Price</th>
                <th>Appointment Time</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['surname']) ?></td>
                    <td><?= htmlspecialchars($row['telephone']) ?></td>
                    <td><?= htmlspecialchars($row['area']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div class="buttons">
            
            <form action="add_appointment.php" method="get">
                <button type="submit" class="add">Add Appointment</button>
            </form>
            <form action="logout.php" method="post">
                <button type="submit" class="logout">Log Out</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
