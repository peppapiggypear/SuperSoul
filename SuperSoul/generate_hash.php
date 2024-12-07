<?php
include('db_connect.php');
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$sql = "UPDATE admins SET password_hash = ? WHERE username = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashed_password);
$stmt->execute();

echo "Password updated successfully.";
?>

