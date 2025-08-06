<?php
include("database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup_style.css" />
</head>
<body>

<div class="container">
    <h3>Sign Up</h3>
    <form method="post" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required />

        <label for="surname">Surname:</label>
        <input type="text" id="surname" name="surname" required />

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required />

        <label for="pwd">Password:</label>
        <input type="password" id="pwd" name="pwd" required />

        <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="index.php">Login here</a></p>
</div>

</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $username = trim($_POST['username']);
    $password = $_POST['pwd'];

    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, surname, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $surname, $username, $hashedPwd);

    if ($stmt->execute()) {
        echo "<p class='success'>Signup successful! Redirecting to login...</p>";
        header("Refresh:2; url=index.php");
        exit();
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
