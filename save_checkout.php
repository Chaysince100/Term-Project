<?php
include('connect_db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = trim($_POST['sale_id']);
    $table_no = trim($_POST['table_no']);
    $paytype_id = trim($_POST['paytype_id']);

    if (empty($sale_id)) {
        echo "<script>alert('ข้อมูลไม่ถูกต้อง'); history.back();</script>";
        exit;
    }

    // อัปเดตสถานะบิลเป็น paid และบันทึกประเภทการจ่ายเงิน
    $sql_update_sale = "UPDATE sales SET 
                            payment_status = 'paid', 
                            paytype_id = '" . mysqli_real_escape_string($conn, $paytype_id) . "' 
                        WHERE sale_id = '" . mysqli_real_escape_string($conn, $sale_id) . "'";
    mysqli_query($conn, $sql_update_sale);

    // ปลดสถานะโต๊ะให้เป็น ว่าง
    if (!empty($table_no)) {
        mysqli_query($conn, "UPDATE restaurant_table SET status = 'ว่าง' WHERE table_no = '" . mysqli_real_escape_string($conn, $table_no) . "'");
    }

    echo "<script>alert('รับชำระเงินเรียบร้อยแล้ว'); window.location='zoom_receipt.php?sale_id=" . urlencode($sale_id) . "';</script>";
} else {
    header("Location: sale_list.php");
}
?>
