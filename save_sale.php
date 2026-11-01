<?php
include('connect_db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = trim($_POST['sale_id']);
    $sale_date = trim($_POST['sale_date']);
    $table_no = trim($_POST['table_no']);
    $customer_count = intval($_POST['customer_count']);
    $customer_id = trim($_POST['customer_id']);
    $paytype_id = trim($_POST['paytype_id']);
    $payment_status = trim($_POST['payment_status']);

    $prod_ids = isset($_POST['prod_id']) ? $_POST['prod_id'] : [];
    $qtys = isset($_POST['qty']) ? $_POST['qty'] : [];
    $prices = isset($_POST['price']) ? $_POST['price'] : [];

    if (empty($sale_id) || empty($table_no) || empty($prod_ids)) {
        echo "<script>alert('กรุณากรอกข้อมูลและเลือกรายการอาหารให้ครบถ้วน'); history.back();</script>";
        exit;
    }

    // คำนวณยอดรวมและเตรียมข้อมูลรายการ
    $total_amount = 0;
    $valid_items = [];

    for ($i = 0; $i < count($prod_ids); $i++) {
        $p_id = trim($prod_ids[$i]);
        $qty = intval($qtys[$i]);
        if ($p_id != '' && $qty > 0) {
            // ดึงราคาจริงจากฐานข้อมูลเพื่อความปลอดภัย
            $p_query = mysqli_query($conn, "SELECT price FROM product WHERE product_id = '" . mysqli_real_escape_string($conn, $p_id) . "'");
            if ($p_row = mysqli_fetch_assoc($p_query)) {
                $unit_price = floatval($p_row['price']);
                $subtotal = $unit_price * $qty;
                $total_amount += $subtotal;
                $valid_items[] = [
                    'product_id' => $p_id,
                    'qty' => $qty,
                    'unit_price' => $unit_price,
                    'subtotal' => $subtotal
                ];
            }
        }
    }

    if (empty($valid_items)) {
        echo "<script>alert('กรุณาเลือกรายการอาหารที่ถูกต้องอย่างน้อย 1 รายการ'); history.back();</script>";
        exit;
    }

    // 1. บันทึกลงตาราง sales
    $sql_sale = "INSERT INTO sales (sale_id, sale_date, table_no, customer_count, customer_id, paytype_id, total_amount, payment_status) 
                 VALUES (
                    '" . mysqli_real_escape_string($conn, $sale_id) . "',
                    '" . mysqli_real_escape_string($conn, $sale_date) . "',
                    '" . mysqli_real_escape_string($conn, $table_no) . "',
                    {$customer_count},
                    '" . mysqli_real_escape_string($conn, $customer_id) . "',
                    '" . mysqli_real_escape_string($conn, $paytype_id) . "',
                    {$total_amount},
                    '" . mysqli_real_escape_string($conn, $payment_status) . "'
                 )";

    if (!mysqli_query($conn, $sql_sale)) {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกบิล: " . mysqli_error($conn) . "'); history.back();</script>";
        exit;
    }

    // 2. บันทึกลงตาราง sale_details
    foreach ($valid_items as $item) {
        $sql_detail = "INSERT INTO sale_details (sale_id, product_id, quantity, unit_price, subtotal) 
                       VALUES (
                          '" . mysqli_real_escape_string($conn, $sale_id) . "',
                          '" . mysqli_real_escape_string($conn, $item['product_id']) . "',
                          {$item['qty']},
                          {$item['unit_price']},
                          {$item['subtotal']}
                       )";
        mysqli_query($conn, $sql_detail);
    }

    // 3. อัปเดตสถานะโต๊ะ
    if ($payment_status == 'pending') {
        mysqli_query($conn, "UPDATE restaurant_table SET status = 'มีลูกค้า' WHERE table_no = '" . mysqli_real_escape_string($conn, $table_no) . "'");
    } else {
        mysqli_query($conn, "UPDATE restaurant_table SET status = 'ว่าง' WHERE table_no = '" . mysqli_real_escape_string($conn, $table_no) . "'");
    }

    // 4. แสดงผลและนำทาง
    if ($payment_status == 'paid') {
        echo "<script>alert('บันทึกการขายและรับชำระเงินเรียบร้อยแล้ว'); window.location='zoom_receipt.php?sale_id=" . urlencode($sale_id) . "';</script>";
    } else {
        echo "<script>alert('เปิดโต๊ะและบันทึกรายการอาหารเรียบร้อยแล้ว'); window.location='sale_list.php';</script>";
    }
} else {
    header("Location: sale_list.php");
}
?>
