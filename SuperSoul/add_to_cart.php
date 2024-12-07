<?php
session_start();
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productID'])) {
    $productID = $_POST['productID'];

    $sql = "SELECT * FROM products WHERE productID='$productID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Check if the cart session variable exists, if not, create it
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $itemExists = false;

        // Check if the item is already in the cart
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['productID'] == $productID) {
                $item['quantity'] += 1;
                $itemExists = true;
                break;
            }
        }

        if (!$itemExists) {
            $_SESSION['cart'][] = [
                'productID' => $product['productID'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
            ];
        }

        echo "<p>Product added to cart!</p>";
        header('Location: cart.php');
        exit();
    } else {
        echo "<p>Product not found.</p>";
    }
}
?>
