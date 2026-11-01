<?php
include('connect_db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = trim($_POST['product_id']);
    $product_name = trim($_POST['product_name']);
    $category_id = trim($_POST['category_id']);
    $price = floatval($_POST['price']);
    $cost = floatval($_POST['cost']);
    $status = trim($_POST['status']);
    $description = trim($_POST['description']);

    $sql = "UPDATE product SET 
                product_name = '" . mysqli_real_escape_string($conn, $product_name) . "',
                category_id = '" . mysqli_real_escape_string($conn, $category_id) . "',
                price = {$price},
                cost = {$cost},
                status = '" . mysqli_real_escape_string($conn, $status) . "',
                description = '" . mysqli_real_escape_string($conn, $description) . "'
            WHERE product_id = '" . mysqli_real_escape_string($conn, $product_id) . "'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('แก้ไขข้อมูลสินค้าสำเร็จ'); window.location='manage_product.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการแก้ไข: " . mysqli_error($conn) . "'); history.back();</script>";
    }
} else {
    header("Location: manage_product.php");
}
?>
