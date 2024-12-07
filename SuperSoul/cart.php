<?php
session_start();
include('db_connect.php');

// Handle updates to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_productID'])) {
        $productID = $_POST['update_productID'];
        $action = $_POST['action'];

        foreach ($_SESSION['cart'] as &$item) {
            if ($item['productID'] == $productID) {
                if ($action === 'increase') {
                    $item['quantity']++;
                } elseif ($action === 'decrease' && $item['quantity'] > 1) {
                    $item['quantity']--;
                }
                break;
            }
        }
        unset($item); // Break reference with the last element
    } elseif (isset($_POST['delete_productID'])) {
        $delete_productID = $_POST['delete_productID'];
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['productID'] == $delete_productID) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Shopping Cart</title>
    <link rel="stylesheet" href="styles_cart.css">
</head>
<header>
    <h1>Your Shopping Cart</h1>
</header>
<body>
    <h2>ffff</h2>
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php foreach ($_SESSION['cart'] as $item) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td>
                        <form action="cart.php" method="POST" style="display: inline-block;">
                            <input type="hidden" name="update_productID" value="<?php echo $item['productID']; ?>">
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>-</button>
                        </form>
                        <?php echo htmlspecialchars($item['quantity']); ?>
                        <form action="cart.php" method="POST" style="display: inline-block;">
                            <input type="hidden" name="update_productID" value="<?php echo $item['productID']; ?>">
                            <input type="hidden" name="action" value="increase">
                            <button type="submit">+</button>
                        </form>
                    </td>
                    <td><?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                    <td>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="delete_productID" value="<?php echo $item['productID']; ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div>
            <button onclick="window.location.href='products.php'">Back to Shopping</button>
            <button onclick="window.location.href='checkout.php'">Proceed to Check-out</button>
        </div>
    <?php } else { ?>
        <p>Your cart is empty.</p>
    <?php } ?>
</body>
</html>
