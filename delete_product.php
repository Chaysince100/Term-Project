<?php
include('connect_db.php');

$product_id = isset($_GET['product_id']) ? trim($_GET['product_id']) : '';

if ($product_id != '') {
    // ตรวจสอบว่าเคยมีการขายสินค้านี้หรือไม่
    $chk_sales = mysqli_query($conn, "SELECT detail_id FROM sale_details WHERE product_id = '" . mysqli_real_escape_string($conn, $product_id) . "' LIMIT 1");
    if (mysqli_num_rows($chk_sales) > 0) {
        // หากมีประวัติการขาย แนะนำให้เปลี่ยนสถานะเป็น 'ยกเลิก' แทนการลบเพื่อรักษาความถูกต้องของข้อมูล
        mysqli_query($conn, "UPDATE product SET status = 'ยกเลิก' WHERE product_id = '" . mysqli_real_escape_string($conn, $product_id) . "'");
        echo "<script>alert('สินค้านี้มีประวัติการขายแล้วในระบบ ระบบได้เปลี่ยนสถานะเป็น ยกเลิก ให้เรียบร้อยแล้ว'); window.location='manage_product.php';</script>";
        exit;
    }

    $sql = "DELETE FROM product WHERE product_id = '" . mysqli_real_escape_string($conn, $product_id) . "'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('ลบข้อมูลสินค้าเรียบร้อยแล้ว'); window.location='manage_product.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบ: " . mysqli_error($conn) . "'); window.location='manage_product.php';</script>";
    }
} else {
    header("Location: manage_product.php");
}
?>
