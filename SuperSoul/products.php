<?php include('db_connect.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SuperSoul</title>
    <link rel="stylesheet" href="styles_prod1.css">
</head>
<header>
    <h1>OUR PRODUCTS</h1>
</header>
<body>
    <section>
        <div class="cart-button-container">
            <button onclick="window.location.href='cart.php'">Go to Shopping Cart</button>
        </div>

        <div class="product-list">
            <?php
            session_start();
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<h3>" . $row['name'] . "</h3>";
                    echo "<img src='" . $row['image_url'] . "' alt='" . $row['name'] . "' style='width:200px; height:auto;'>";
                    echo "<p>" . $row['description'] . "</p>";
                    echo "<p>$" . $row['price'] . "</p>";
                    echo "<form action='add_to_cart.php' method='POST' onsubmit='return checkLogin()'>";
                    echo "<input type='hidden' name='productID' value='" . $row['productID'] . "'>";
                    echo "<button type='submit'>Add to Cart</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>No products available.</p>";
            }
            ?>
        </div>
    </section>
    <script>
    function checkLogin() {
        <?php
        if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
            echo "alert('Please log-in to your account');";
            echo "window.location.href = 'login.php';";
            echo "return false;";
        }
        ?>
        return true;
    }
    </script>
</body>
</html>