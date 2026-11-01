<?php
include('connect_db.php');

$product_id = isset($_GET['product_id']) ? trim($_GET['product_id']) : '';

if ($product_id == '') {
    echo "<script>alert('ไม่พบรหัสสินค้าที่ต้องการ Zoom'); window.location='manage_product.php';</script>";
    exit;
}

// ข้อมูลสินค้า
$sql_prod = "SELECT p.*, c.category_name 
             FROM product p 
             LEFT JOIN category c ON p.category_id = c.category_id 
             WHERE p.product_id = '" . mysqli_real_escape_string($conn, $product_id) . "'";
$res_prod = mysqli_query($conn, $sql_prod);
$product = mysqli_fetch_assoc($res_prod);

if (!$product) {
    echo "<script>alert('ไม่พบสินค้าในระบบ'); window.location='manage_product.php';</script>";
    exit;
}

// ประวัติการขายสินค้า (Zoom)
$sql_history = "SELECT d.quantity, d.unit_price, d.subtotal, 
                       s.sale_id, s.sale_date, s.table_no, s.customer_count,
                       c.customer_id, c.customer_name, c.phone,
                       pt.paytype_name
                FROM sale_details d
                INNER JOIN sales s ON d.sale_id = s.sale_id
                LEFT JOIN customer c ON s.customer_id = c.customer_id
                LEFT JOIN paytype pt ON s.paytype_id = pt.paytype_id
                WHERE d.product_id = '" . mysqli_real_escape_string($conn, $product_id) . "'
                ORDER BY s.sale_date DESC";
$res_history = mysqli_query($conn, $sql_history);
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>Zoom ประวัติการขายสินค้า - <?php echo htmlspecialchars($product['product_name']); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<style>
		.info-box {
			background: #fdfdfd;
			border-left: 5px solid #f56a6a;
			padding: 15px 20px;
			margin-bottom: 25px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.08);
		}
		.info-title {
			font-size: 1.3em;
			font-weight: bold;
			color: #3d4449;
			margin-bottom: 8px;
		}
		.info-detail span {
			margin-right: 20px;
			display: inline-block;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="manage_product.php" class="logo"><strong>ข้อมูลพื้นฐาน</strong> &gt; <a href="manage_product.php">จัดการข้อมูลสินค้า</a> &gt; Zoom ประวัติการขาย</a>
				</header>

				<section>
					<header class="main">
						<h2>🔍 Zoom: ประวัติการขายสินค้า</h2>
						<p>แสดงรายละเอียดประวัติว่าสินค้านี้ <strong>ขายให้ใคร เลขที่การขายใด วันที่เท่าไร</strong></p>
					</header>

					<!-- รายละเอียดสินค้า -->
					<div class="info-box">
						<div class="info-title">
							[<?php echo htmlspecialchars($product['product_id']); ?>] <?php echo htmlspecialchars($product['product_name']); ?>
						</div>
						<div class="info-detail">
							<span><strong>หมวดหมู่:</strong> <?php echo htmlspecialchars($product['category_name']); ?></span>
							<span><strong>ราคาขาย:</strong> <font color="#1565c0"><?php echo number_format($product['price'], 2); ?> ฿</font></span>
							<span><strong>ต้นทุน:</strong> <font color="#c62828"><?php echo number_format($product['cost'], 2); ?> ฿</font></span>
							<span><strong>สถานะ:</strong> <?php echo htmlspecialchars($product['status']); ?></span>
						</div>
						<?php if (!empty($product['description'])) { ?>
						<p style="margin-top: 10px; margin-bottom: 0; color: #666;">
							<strong>คำอธิบาย:</strong> <?php echo htmlspecialchars($product['description']); ?>
						</p>
						<?php } ?>
					</div>

					<div class="table-wrapper">
						<h3>รายการการขายที่พบ</h3>
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 50px; text-align: center;">ลำดับ</th>
									<th>วันที่-เวลาขาย</th>
									<th>เลขที่การขาย (บิล)</th>
									<th>ขายให้ใคร (ชื่อลูกค้า)</th>
									<th style="text-align: center;">โต๊ะ</th>
									<th style="text-align: center;">การชำระเงิน</th>
									<th style="text-align: right;">จำนวนที่ซื้อ</th>
									<th style="text-align: right;">ราคา/หน่วย</th>
									<th style="text-align: right;">มูลค่ารวม (฿)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$total_qty = 0;
								$total_revenue = 0;

								if (mysqli_num_rows($res_history) > 0) {
									while ($hist = mysqli_fetch_assoc($res_history)) {
										$total_qty += $hist['quantity'];
										$total_revenue += $hist['subtotal'];
								?>
								<tr>
									<td style="text-align: center;"><?php echo $i++; ?></td>
									<td><?php echo $hist['sale_date']; ?></td>
									<td>
										<a href="zoom_receipt.php?sale_id=<?php echo urlencode($hist['sale_id']); ?>" title="คลิกดูใบเสร็จ">
											<strong><?php echo htmlspecialchars($hist['sale_id']); ?></strong>
										</a>
									</td>
									<td>
										<strong><?php echo htmlspecialchars($hist['customer_name'] ? $hist['customer_name'] : 'ลูกค้าทั่วไป'); ?></strong>
										<?php if ($hist['phone'] && $hist['phone'] != '-') echo " <small>({$hist['phone']})</small>"; ?>
									</td>
									<td style="text-align: center;"><span class="tag"><?php echo htmlspecialchars($hist['table_no']); ?></span></td>
									<td style="text-align: center;"><?php echo htmlspecialchars($hist['paytype_name']); ?></td>
									<td style="text-align: right; font-weight: bold;"><?php echo number_format($hist['quantity']); ?></td>
									<td style="text-align: right;"><?php echo number_format($hist['unit_price'], 2); ?></td>
									<td style="text-align: right; font-weight: bold; color: #2e7d32;"><?php echo number_format($hist['subtotal'], 2); ?></td>
								</tr>
								<?php
									}
								} else {
									echo "<tr><td colspan='9' style='text-align:center; padding: 25px;'>ยังไม่มีประวัติการขายสำหรับสินค้านี้</td></tr>";
								}
								?>
							</tbody>
							<?php if (mysqli_num_rows($res_history) > 0) { ?>
							<tfoot>
								<tr style="background-color: #fff8e1; font-weight: bold;">
									<td colspan="6" style="text-align: right;">สรุปรวมยอดขายสินค้านี้ทั้งหมด:</td>
									<td style="text-align: right; color: #d84315;"><?php echo number_format($total_qty); ?> จาน/ชิ้น</td>
									<td></td>
									<td style="text-align: right; color: #2e7d32; font-size: 1.15em;"><?php echo number_format($total_revenue, 2); ?> ฿</td>
								</tr>
							</tfoot>
							<?php } ?>
						</table>
					</div>

					<ul class="actions">
						<li><a href="manage_product.php" class="button special">กลับหน้ารายการสินค้า</a></li>
						<li><a href="edit_product.php?product_id=<?php echo urlencode($product['product_id']); ?>" class="button">แก้ไขสินค้านี้</a></li>
					</ul>
				</section>
			</div>
		</div>

		<div id="sidebar">
			<div class="inner">
				<nav id="menu">
					<?php include('menu.php'); ?>
				</nav>
			</div>
		</div>
	</div>

	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/util.js"></script>
	<script src="assets/js/main.js"></script>
</body>
</html>
