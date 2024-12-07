<?php
include('db_connect.php');
session_start();

// Ensure the admin is logged in (adjust as needed for your setup)
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_orderID'])) {
    $delete_orderID = $_POST['delete_orderID'];

    // Delete related records in orderitems
    $sql_delete_items = "DELETE FROM orderitems WHERE orderID = '$delete_orderID'";
    $conn->query($sql_delete_items);

    $sql_delete_pm = "DELETE FROM payments WHERE orderID = '$delete_orderID'";
    $conn->query($sql_delete_pm);

    // Delete the order record
    $sql_delete_order = "DELETE FROM orders WHERE orderID = '$delete_orderID'";
    /*if ($conn->query($sql_delete_order) === TRUE) {
        echo "<p>Order deleted successfully.</p>";
    } else {
        echo "<p>Error deleting order: " . $conn->error . "</p>";
    }*/
}

// Fetch all orders along with customer and payment details
$sql_orders = "SELECT o.orderID, o.customerID, o.order_date, o.order_total, o.shipping_address, 
                      p.payment_method, p.payment_status, p.amount 
               FROM orders o 
               JOIN payments p ON o.orderID = p.orderID 
               ORDER BY o.order_date DESC";
$result_orders = $conn->query($sql_orders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Tracking</title>
    <link rel="stylesheet" href="styles_track1.css">
</head>
<header>
    <h1>Order Tracking</h1>
</header>
<body>
    <h2>Order Tracking</h2>

    <?php if ($result_orders && $result_orders->num_rows > 0) { ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Order Date</th>
                <th>Total Amount</th>
                <th>Shipping Address</th>
                <th>Payment Method</th>
                <th>Payment Status</th>
                <th>Order Items</th>
            </tr>
            <?php while ($order = $result_orders->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['orderID']); ?></td>
                    <td><?php echo htmlspecialchars($order['customerID']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_total']); ?></td>
                    <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                    <td>
                        <table>
                            <tr>
                                <th>Product ID</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                            </tr>
                            <?php
                            $orderID = $order['orderID'];
                            $sql_items = "SELECT productID, quantity, unit_price 
                                          FROM orderitems 
                                          WHERE orderID = '$orderID'";
                            $result_items = $conn->query($sql_items);

                            if ($result_items && $result_items->num_rows > 0) {
                                while ($item = $result_items->fetch_assoc()) {
                                    $total_price = $item['quantity'] * $item['unit_price'];
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['productID']); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($item['unit_price']); ?></td>
                                        <td><?php echo htmlspecialchars($total_price); ?></td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='4'>No items found for this order.</td></tr>";
                            }
                            ?>

                        </table>
                    </td>
                    <td>
                        <form action="tracking_order.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                            <input type="hidden" name="delete_orderID" value="<?php echo htmlspecialchars($order['orderID']); ?>">
                            <button type="submit">Clear</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No orders found.</p>
    <?php } ?>
    <section>
        <h2>Others</h2>
        <nav>
            <a href="edit_product.php">Add New Products</a> |
            <a href="admin_dashboard.php">Dashboard</a> |
            <a href="logout.php">Log Out</a> 
        </nav>
    </section>
</body>
</html>
