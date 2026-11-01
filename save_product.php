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

    // ตรวจสอบว่ารหัสซ้ำหรือไม่
    $chk = mysqli_query($conn, "SELECT product_id FROM product WHERE product_id = '" . mysqli_real_escape_string($conn, $product_id) . "'");
    if (mysqli_num_rows($chk) > 0) {
        echo "<script>alert('รหัสสินค้านี้มีอยู่แล้วในระบบ กรุณาใช้รหัสอื่น'); history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO product (product_id, product_name, category_id, price, cost, description, status) 
            VALUES (
                '" . mysqli_real_escape_string($conn, $product_id) . "',
                '" . mysqli_real_escape_string($conn, $product_name) . "',
                '" . mysqli_real_escape_string($conn, $category_id) . "',
                {$price},
                {$cost},
                '" . mysqli_real_escape_string($conn, $description) . "',
                '" . mysqli_real_escape_string($conn, $status) . "'
            )";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('เพิ่มข้อมูลสินค้าเรียบร้อยแล้ว'); window.location='manage_product.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก: " . mysqli_error($conn) . "'); history.back();</script>";
    }
} else {
    header("Location: manage_product.php");
}
?>
