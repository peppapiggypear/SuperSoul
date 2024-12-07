<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['customerID'])) {
    header('Location: login.php');
    exit();
}

$customerID = $_SESSION['customerID'];
$cartItems = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($cartItems)) {
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];
    $contact = $_POST['contact'];
    
    $order_date = date('Y-m-d H:i:s');
    $order_total = 0;

    foreach ($cartItems as $item) {
        $order_total += $item['quantity'] * $item['price'];
    }

    $sql_order = "INSERT INTO orders (customerID, order_date, order_total, shipping_address, contact) 
                  VALUES (?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("isdss", $customerID, $order_date, $order_total, $shipping_address, $contact);

    if ($stmt_order->execute()) {
        $orderID = $stmt_order->insert_id;

        // Insert payment record
        $sql_payment = "INSERT INTO payments (orderID, payment_method, payment_status, amount) 
                        VALUES (?, ?, 'Completed', ?)";
        $stmt_payment = $conn->prepare($sql_payment);
        $stmt_payment->bind_param("isd", $orderID, $payment_method, $order_total);
        $stmt_payment->execute();

        // Insert each item in the orderitems table
        foreach ($cartItems as $item) {
            $productID = $item['productID'];
            $quantity = $item['quantity'];
            $unit_price = $item['price'];

            $sql_orderitem = "INSERT INTO orderitems (productID, orderID, quantity, unit_price) 
                              VALUES (?, ?, ?, ?)";
            $stmt_orderitem = $conn->prepare($sql_orderitem);
            $stmt_orderitem->bind_param("iiid", $productID, $orderID, $quantity, $unit_price);
            $stmt_orderitem->execute();

            // Update product stock
            $sql_update_stock = "UPDATE products SET stock_level = stock_level - ? WHERE productID = ?";
            $stmt_stock = $conn->prepare($sql_update_stock);
            $stmt_stock->bind_param("ii", $quantity, $productID);
            $stmt_stock->execute();
        }

        // Clear the session cart
        unset($_SESSION['cart']);

        session_destroy();

        echo "<script>
                alert('Your Order Is Completed! You have been logged out.');
                window.location.href = 'index.php';
              </script>";
        exit();
    } else {
        echo "<p>Error placing order: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles_co.css">
</head>
<header>
    <h1>CHECK OUT</h1>
</header>
<body>
    <h2>CHECK OUT</h2>
    <?php if (!empty($cartItems)) { ?>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            <?php
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $totalAmount += $itemTotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($itemTotal); ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="3">Total Amount</td>
                <td><?php echo htmlspecialchars($totalAmount); ?></td>
            </tr>
        </table>

        <form action="checkout.php" method="POST">
            <label for="shipping_address">Shipping Address:</label>
            <textarea id="shipping_address" name="shipping_address" required></textarea><br>

            <label for="contact">Contact Information:</label>
            <textarea id="contact" name="contact" required placeholder="Enter Your Phone Number or E-mail Here."></textarea><br>

            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="Credit Card">Credit Card</option>
                <option value="PayPal">PayPal</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select><br>

            <button type="submit">Place Order</button>
        </form>
        <button onclick="window.location.href='cart.php'">Back to Cart</button>
    <?php } else { ?>
        <p>Your cart is empty.</p>
    <?php } ?>
</body>
</html>

