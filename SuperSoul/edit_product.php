<?php
include('db_connect.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Check if we are editing an existing product
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['productID'])) {
    $productID = $_GET['productID'];
    $sql = "SELECT * FROM products WHERE productID='$productID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "<p>Product not found.</p>";
        exit();
    }
}

// Handle form submission for adding or updating a product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productID = $_POST['productID'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image_url = $_POST['image_url'];

    if ($productID) {
        // Update existing product
        $sql = "UPDATE products SET name='$name', description='$description', price='$price', stock_level='$stock', image_url='$image_url' WHERE productID='$productID'";
    } else {
        // Add new product
        $sql = "INSERT INTO products (name, description, price, stock_level, image_url) VALUES ('$name', '$description', '$price', '$stock', '$image_url')";
    }

    if ($conn->query($sql) === TRUE) {
        header('Location: admin_dashboard.php');
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($product) ? 'Edit Product' : 'Add Product'; ?></title>
    <link rel="stylesheet" href="styles_editprod.css">
</head>
<body>
    <h2><?php echo isset($product) ? 'Edit Product' : 'Add New Product'; ?></h2>
    <form action="edit_product.php" method="POST">
        <?php if (isset($product)) { ?>
            <input type="hidden" name="productID" value="<?php echo $product['productID']; ?>">
        <?php } ?>
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $product['name'] ?? ''; ?>" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo $product['description'] ?? ''; ?></textarea><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price'] ?? ''; ?>" required><br>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" value="<?php echo $product['stock_level'] ?? ''; ?>" required><br>

        <label for="image_url">Image URL:</label>
        <input type="url" id="image_url" name="image_url" value="<?php echo $product['image_url'] ?? ''; ?>"><br>

        <button type="submit"><?php echo isset($product) ? 'Update Product' : 'Add Product'; ?></button>
    </form>
</body>
</html>
