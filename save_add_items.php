<?php
include('connect_db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = trim($_POST['sale_id']);
    $prod_ids = isset($_POST['prod_id']) ? $_POST['prod_id'] : [];
    $qtys = isset($_POST['qty']) ? $_POST['qty'] : [];

    if (empty($sale_id) || empty($prod_ids)) {
        echo "<script>alert('กรุณาเลือกรายการอาหารที่ต้องการสั่งเพิ่ม'); history.back();</script>";
        exit;
    }

    $added_amount = 0;
    for ($i = 0; $i < count($prod_ids); $i++) {
        $p_id = trim($prod_ids[$i]);
        $qty = intval($qtys[$i]);
        if ($p_id != '' && $qty > 0) {
            $p_query = mysqli_query($conn, "SELECT price FROM product WHERE product_id = '" . mysqli_real_escape_string($conn, $p_id) . "'");
            if ($p_row = mysqli_fetch_assoc($p_query)) {
                $unit_price = floatval($p_row['price']);
                $subtotal = $unit_price * $qty;
                $added_amount += $subtotal;

                $sql_ins = "INSERT INTO sale_details (sale_id, product_id, quantity, unit_price, subtotal) 
                            VALUES (
                                '" . mysqli_real_escape_string($conn, $sale_id) . "',
                                '" . mysqli_real_escape_string($conn, $p_id) . "',
                                {$qty},
                                {$unit_price},
                                {$subtotal}
                            )";
                mysqli_query($conn, $sql_ins);
            }
        }
    }

    // อัปเดตยอดรวมในตาราง sales
    mysqli_query($conn, "UPDATE sales SET total_amount = total_amount + {$added_amount} WHERE sale_id = '" . mysqli_real_escape_string($conn, $sale_id) . "'");

    echo "<script>alert('สั่งอาหารเพิ่มเรียบร้อยแล้ว'); window.location='sale_list.php';</script>";
} else {
    header("Location: sale_list.php");
}
?>
