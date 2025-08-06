<?php
session_start();
include("database.php");

// Redirect if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $surname = htmlspecialchars(trim($_POST['surname']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $area = floatval($_POST['area']);
    $appointment_time = $_POST['appointment_time'];
    $userId = $_SESSION['id'];

    $price = $area * 5;

    if ($name && $surname && $telephone && $area > 0 && $appointment_time) {
        // Extract date part from appointment_time (YYYY-MM-DD)
        $appointmentDate = date('Y-m-d', strtotime($appointment_time));

        // Check if any appointment exists on the same day
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE DATE(appointment_time) = ?");
        $checkStmt->bind_param("s", $appointmentDate);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            // Appointment exists on the same day
            $errorMessage = "There is already an appointment on the same day.";
        } else {
            // No appointment on the same day, insert new appointment
            $stmt = $conn->prepare("INSERT INTO appointments (user_id, name, surname, telephone, area, price, appointment_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssds", $userId, $name, $surname, $telephone, $area, $price, $appointment_time);

            if ($stmt->execute()) {
                header("Location: appointments.php");
                exit();
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        $errorMessage = "Please fill in all required fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Appointment</title>
    <link rel="stylesheet" href="add_app_style.css">
    <script>
        function updatePrice() {
            const area = parseFloat(document.getElementById('area').value);
            const price = area > 0 ? area * 5 : 0;
            document.getElementById('priceDisplay').textContent = 'Calculated Price: $' + price.toFixed(2);
        }
    </script>
</head>
<body>



<?php if ($errorMessage): ?>
    <p class="message" style="color:red; text-align:center;"><?= $errorMessage ?></p>
<?php endif; ?>

<form method="post">
    <h2 style="text-align: center;">Add Appointment</h2>
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Surname:</label>
    <input type="text" name="surname" required>

    <label>Telephone:</label>
    <input type="text" name="telephone" required>

    <label>Area (in m²):</label>
    <input type="number" step="0.1" min="0" name="area" id="area" required oninput="updatePrice()">

    <p id="priceDisplay">Calculated Price: $0.00</p>

    <label>Appointment Time:</label>
    <input type="datetime-local" name="appointment_time" required>

    <button type="submit">Save Appointment</button><br><br>
    <div style="text-align:center;">
    <a href="appointments.php">← Back to Appointments</a>
</div>
</form>



</body>
</html>

<?php $conn->close(); ?>
