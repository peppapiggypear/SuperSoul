<?php
include('db_connect.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productID'])) {
    $productID = $_POST['productID'];
    $sql = "DELETE FROM products WHERE productID='$productID'";

    if ($conn->query($sql) === TRUE) {
        header('Location: admin_dashboard.php');
        exit();
    } else {
        echo "<p>Error deleting product: " . $conn->error . "</p>";
    }
}
?>
