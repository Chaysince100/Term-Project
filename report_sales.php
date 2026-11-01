<?php
include('connect_db.php');

// กำหนดช่วงวันที่เริ่มต้นและสิ้นสุด
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-01');
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date('Y-m-d');

// คำสั่ง SQL สรุปการขายระหว่างวันที่-วันที่ แสดงสินค้า ยอดขายรวม มูลค่าการขาย
$sql = "SELECT p.product_id, p.product_name, c.category_name, p.price,
               SUM(d.quantity) AS total_qty_sold,
               SUM(d.subtotal) AS total_sales_value
        FROM sale_details d
        INNER JOIN sales s ON d.sale_id = s.sale_id
        INNER JOIN product p ON d.product_id = p.product_id
        LEFT JOIN category c ON p.category_id = c.category_id
        WHERE s.payment_status = 'paid'
          AND DATE(s.sale_date) BETWEEN '" . mysqli_real_escape_string($conn, $date_start) . "' 
                                    AND '" . mysqli_real_escape_string($conn, $date_end) . "'
        GROUP BY p.product_id, p.product_name, c.category_name, p.price
        ORDER BY total_sales_value DESC, total_qty_sold DESC";

$record = mysqli_query($conn, $sql);
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>รายงานสรุปการขายระหว่างวันที่ - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<style>
		.filter-box {
			background: #fafafa;
			padding: 20px;
			border: 1px solid #ddd;
			border-radius: 6px;
			margin-bottom: 25px;
		}
		@media print {
			#sidebar, #header, .filter-box, .btn-print, footer {
				display: none !important;
			}
			#main {
				margin: 0 !important;
				padding: 0 !important;
			}
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="index.php" class="logo"><strong>รายงานผลประกอบการ</strong> &gt; สรุปการขายระหว่างวันที่</a>
				</header>

				<section>
					<header class="main">
						<h2>1. สรุปการขายระหว่างวันที่ - วันที่</h2>
						<p>แสดงรายการสินค้า ยอดขายรวม (จำนวน) มูลค่าการขาย และสรุปด้วยมูลค่าการขายรวมทั้งหมด</p>
					</header>

					<!-- กล่องเลือกช่วงวันที่ -->
					<div class="filter-box">
						<form method="get" action="report_sales.php">
							<div class="row uniform">
								<div class="4u 12u$(xsmall)">
									<label for="date_start">ตั้งแต่วันที่ *</label>
									<input type="date" name="date_start" id="date_start" value="<?php echo htmlspecialchars($date_start); ?>" required />
								</div>
								<div class="4u 12u$(xsmall)">
									<label for="date_end">ถึงวันที่ *</label>
									<input type="date" name="date_end" id="date_end" value="<?php echo htmlspecialchars($date_end); ?>" required />
								</div>
								<div class="4u$ 12u$(xsmall)" style="padding-top: 28px;">
									<input type="submit" value="แสดงรายงาน" class="special" />
									<button type="button" onclick="window.print()" class="button btn-print">🖨️ พิมพ์รายงาน</button>
								</div>
							</div>
						</form>
					</div>

					<div style="margin-bottom: 15px;">
						<h3>รายงานยอดขายระหว่างวันที่ <u><?php echo date('d/m/Y', strtotime($date_start)); ?></u> ถึง <u><?php echo date('d/m/Y', strtotime($date_end)); ?></u></h3>
					</div>

					<!-- ตารางแสดงผลสรุป -->
					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 60px; text-align: center;">ลำดับ</th>
									<th style="width: 120px;">รหัสสินค้า</th>
									<th>ชื่อสินค้า / เมนูอาหาร</th>
									<th>หมวดหมู่</th>
									<th style="text-align: right; width: 100px;">ราคาขาย (฿)</th>
									<th style="text-align: right; width: 140px;">ยอดขายรวม (จำนวน)</th>
									<th style="text-align: right; width: 160px;">มูลค่าการขายรวม (฿)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$grand_total_qty = 0;
								$grand_total_sales = 0;

								if (mysqli_num_rows($record) > 0) {
									while ($row = mysqli_fetch_assoc($record)) {
										$grand_total_qty += $row['total_qty_sold'];
										$grand_total_sales += $row['total_sales_value'];
								?>
								<tr>
									<td style="text-align: center;"><?php echo $i++; ?></td>
									<td>
										<a href="zoom_product.php?product_id=<?php echo urlencode($row['product_id']); ?>" title="Zoom สินค้า">
											<strong><?php echo htmlspecialchars($row['product_id']); ?></strong>
										</a>
									</td>
									<td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
									<td><?php echo htmlspecialchars($row['category_name']); ?></td>
									<td style="text-align: right;"><?php echo number_format($row['price'], 2); ?></td>
									<td style="text-align: right; font-weight: bold; color: #d84315;"><?php echo number_format($row['total_qty_sold']); ?> จาน/ชิ้น</td>
									<td style="text-align: right; font-weight: bold; color: #2e7d32;"><?php echo number_format($row['total_sales_value'], 2); ?></td>
								</tr>
								<?php
									}
								} else {
									echo "<tr><td colspan='7' style='text-align:center; padding: 25px;'>ไม่มีข้อมูลยอดขายในช่วงวันที่ระบุ</td></tr>";
								}
								?>
							</tbody>
							<tfoot>
								<tr style="background-color: #e8f5e9; font-weight: bold; font-size: 1.1em;">
									<td colspan="5" style="text-align: right;">สรุปด้วยมูลค่าการขายรวมทั้งสิ้น:</td>
									<td style="text-align: right; color: #d84315;"><?php echo number_format($grand_total_qty); ?> จาน/ชิ้น</td>
									<td style="text-align: right; color: #1b5e20; font-size: 1.25em;"><?php echo number_format($grand_total_sales, 2); ?> ฿</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</section>

				<footer id="footer">
					<p class="copyright">ร้านบูรพาสปาเก็ตตี้ &copy; Term Project</p>
				</footer>
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
