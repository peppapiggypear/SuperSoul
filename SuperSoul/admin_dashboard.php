<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "retail";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles_dash.css">
</head>
<body>
    <h1>Supermarket Management</h1>
    <section>
        <h2>Current Products</h2>
        <div class="product-list">
            <?php
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<h3>" . $row['name'] . " (ID: " . $row['productID'] . ")</h3>";
                    echo "<p>Description: " . $row['description'] . "</p>";
                    echo "<p>Price: $" . $row['price'] . "</p>";
                    echo "<p>In Stock: " . $row['stock_level'] . " units</p>";
                    echo "<div class='button-container'>";
                    echo "<form action='edit_product.php' method='GET'>";
                    echo "<input type='hidden' name='productID' value='" . $row['productID'] . "'>";
                    echo "<button type='submit'>Edit</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No products available.</p>";
            }
            ?>
        </div>
    </section>

    <section>
        <h2>Others</h2>
        <nav>
            <a href="edit_product.php">Add New Products</a> |
            <a href="tracking_order.php">Track Orders</a> |
            <a href="logout.php">Log Out</a>
        </nav>
    </section>

    <footer>
        <p>&copy; 2024 Supermarket</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
