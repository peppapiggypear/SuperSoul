<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db_connect.php');

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $name = $conn->real_escape_string($_POST['name']);

    $hashed_password = hash('sha256', $password);

    // Begin transaction for atomic operations
    $conn->begin_transaction();

    try {
        $sql_customer = "INSERT INTO customers (name) VALUES (?)";
        $stmt_customer = $conn->prepare($sql_customer);
        $stmt_customer->bind_param("s", $name);
        $stmt_customer->execute();
        $customerID = $conn->insert_id;

        $sql_user = "INSERT INTO users (customerID, username, password_hash, name) VALUES (?, ?, ?, ?)";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("isss", $customerID, $username, $hashed_password, $name);
        $stmt_user->execute();

        $conn->commit();

        echo "<p>Signup successful! You can now <a href='login.php'>log in</a>.</p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p>Error during signup: " . $e->getMessage() . "</p>";
    }

    $stmt_customer->close();
    $stmt_user->close();

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Signup</title>
    <link rel="stylesheet" href="styles_signup.css">
</head>
<body>
    <h2>Sign Up</h2>
    <form action="signup.php" method="POST">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Sign Up</button>
    </form>
</body>
</html>