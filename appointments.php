<?php
session_start();
include("database.php");

// Redirect if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['id'];

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_time'])) {
    $deleteTime = $_POST['delete_time'];

    $stmt = $conn->prepare("DELETE FROM appointments WHERE user_id = ? AND appointment_time = ?");
    $stmt->bind_param("is", $userId, $deleteTime);
    $stmt->execute();
    $stmt->close();

    header("Location: appointments.php");
    exit();
}

// Only get future appointments
$sql = "SELECT name, surname, telephone, area, price, appointment_time 
        FROM appointments 
        WHERE user_id = ? 
        AND appointment_time > NOW()
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
    <style>
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background: #333;
            color: white;
        }
    </style>
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
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['surname']) ?></td>
                    <td><?= htmlspecialchars($row['telephone']) ?></td>
                    <td><?= htmlspecialchars($row['area']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_time" value="<?= $row['appointment_time'] ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div class="buttons">
            <form action="add_appointment.php" method="get" style="display:inline-block;">
                <button type="submit" class="add">Add Appointment</button>
            </form>
            <form action="logout.php" method="post" style="display:inline-block;">
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
