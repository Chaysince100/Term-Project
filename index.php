<?php
include('connect_db.php');

// ดึงสถิติภาพรวม
$res_prod = mysqli_query($conn, "SELECT COUNT(*) AS total_prod FROM product");
$data_prod = mysqli_fetch_assoc($res_prod);

$res_sales = mysqli_query($conn, "SELECT COUNT(*) AS total_bills, IFNULL(SUM(total_amount), 0) AS total_revenue FROM sales WHERE payment_status = 'paid'");
$data_sales = mysqli_fetch_assoc($res_sales);

$res_tables = mysqli_query($conn, "SELECT COUNT(*) AS total_tables FROM restaurant_table");
$data_tables = mysqli_fetch_assoc($res_tables);
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>ร้านบูรพาสปาเก็ตตี้ (Black Sheep in the Garden)</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<style>
		.stat-box {
			background: #fdfdfd;
			border: 1px solid #e0e0e0;
			border-radius: 8px;
			padding: 20px;
			text-align: center;
			box-shadow: 0 2px 5px rgba(0,0,0,0.05);
			margin-bottom: 20px;
		}
		.stat-box h3 { margin-bottom: 5px; color: #f56a6a; font-size: 2em; }
		.stat-box p { font-weight: bold; color: #7f888f; margin: 0; }
		.btn-custom { margin-right: 8px; margin-bottom: 8px; }
	</style>
</head>
<body>
	<!-- Wrapper -->
	<div id="wrapper">
		<!-- Main -->
		<div id="main">
			<div class="inner">
				<!-- Header -->
				<header id="header">
					<a href="index.php" class="logo"><strong>บูรพาสปาเก็ตตี้</strong> (Black Sheep in the Garden)</a>
					<ul class="icons">
						<li><span>ระบบบันทึกการขายและรายงานผลประกอบการ</span></li>
					</ul>
				</header>

				<!-- Banner -->
				<section id="banner">
					<div class="content">
						<header>
							<h1>ร้านบูรพาสปาเก็ตตี้<br />
							<small style="font-size: 0.55em; color: #7f888f;">(Black Sheep in the Garden)</small></h1>
							<p>Happiness is homemade ~ Taste the difference</p>
						</header>
						<p>ยินดีต้อนรับสู่ระบบสารสนเทศร้านอาหารบูรพาสปาเก็ตตี้ ระบบช่วยบันทึกการขาย ออกใบเสร็จ จัดการข้อมูลสินค้า วัตถุดิบ และออกรายงานสรุปยอดขายพร้อมวิเคราะห์กำไรขาดทุนเบื้องต้น</p>
						<ul class="actions">
							<li><a href="sale_add.php" class="button special big">เปิดโต๊ะ / บันทึกการขาย</a></li>
							<li><a href="manage_product.php" class="button big">จัดการเมนูอาหาร</a></li>
							<li><a href="report_sales.php" class="button big">รายงานยอดขาย</a></li>
						</ul>
					</div>
					<span class="image object">
						<img src="images/buch-hr.gif" alt="Black Sheep Banner" style="max-height: 260px; object-fit: contain;" />
					</span>
				</section>

				<!-- Section: Stats -->
				<section>
					<header class="major">
						<h2>ภาพรวมระบบร้านอาหาร</h2>
					</header>
					<div class="row">
						<div class="3u 12u$(medium)">
							<div class="stat-box">
								<h3><?php echo number_format($data_prod['total_prod']); ?></h3>
								<p>รายการอาหารทั้งหมด</p>
							</div>
						</div>
						<div class="3u 12u$(medium)">
							<div class="stat-box">
								<h3><?php echo number_format($data_sales['total_bills']); ?></h3>
								<p>จำนวนบิลที่ชำระแล้ว</p>
							</div>
						</div>
						<div class="3u 12u$(medium)">
							<div class="stat-box">
								<h3><?php echo number_format($data_sales['total_revenue'], 2); ?> ฿</h3>
								<p>ยอดขายรวมสะสม</p>
							</div>
						</div>
						<div class="3u 12u$(medium)">
							<div class="stat-box">
								<h3><?php echo number_format($data_tables['total_tables']); ?></h3>
								<p>โต๊ะอาหารทั้งหมด</p>
							</div>
						</div>
					</div>
				</section>

				<!-- Section: Latest Sales & Popular Menu -->
				<section>
					<header class="major">
						<h2>รายการขายล่าสุด</h2>
					</header>
					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f6f6f6;">
									<th>เลขที่ใบเสร็จ</th>
									<th>วันที่-เวลา</th>
									<th>โต๊ะ</th>
									<th>ลูกค้า</th>
									<th>จำนวนลูกค้า</th>
									<th>การชำระเงิน</th>
									<th style="text-align: right;">ยอดรวม</th>
									<th style="text-align: center;">ดูใบเสร็จ</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$sql_recent = "SELECT s.*, c.customer_name, p.paytype_name 
								               FROM sales s 
								               LEFT JOIN customer c ON s.customer_id = c.customer_id 
								               LEFT JOIN paytype p ON s.paytype_id = p.paytype_id 
								               ORDER BY s.sale_date DESC LIMIT 5";
								$rec_recent = mysqli_query($conn, $sql_recent);
								if (mysqli_num_rows($rec_recent) > 0) {
									while ($row = mysqli_fetch_assoc($rec_recent)) {
								?>
								<tr>
									<td><strong><a href="zoom_receipt.php?sale_id=<?php echo $row['sale_id']; ?>"><?php echo $row['sale_id']; ?></a></strong></td>
									<td><?php echo $row['sale_date']; ?></td>
									<td><span class="tag"><?php echo $row['table_no']; ?></span></td>
									<td><?php echo htmlspecialchars($row['customer_name']); ?></td>
									<td><?php echo $row['customer_count']; ?> ท่าน</td>
									<td><?php echo $row['paytype_name']; ?></td>
									<td style="text-align: right; font-weight: bold; color: #2e7d32;"><?php echo number_format($row['total_amount'], 2); ?> ฿</td>
									<td style="text-align: center;">
										<a href="zoom_receipt.php?sale_id=<?php echo $row['sale_id']; ?>" class="button small">Zoom ใบเสร็จ</a>
									</td>
								</tr>
								<?php
									}
								} else {
									echo "<tr><td colspan='8' style='text-align:center;'>ยังไม่มีข้อมูลการขาย</td></tr>";
								}
								?>
							</tbody>
						</table>
					</div>
				</section>

				<!-- Footer -->
				<footer id="footer">
					<p class="copyright">
						กรณีศึกษา: ร้านบูรพาสปาเก็ตตี้ (Black Sheep in the Garden) &copy; Term Project.<br />
						Template: <a href="https://html5up.net/editorial">Editorial by HTML5 UP</a>.
					</p>
				</footer>
			</div>
		</div>

		<!-- Sidebar -->
		<div id="sidebar">
			<div class="inner">
				<nav id="menu">
					<?php include('menu.php'); ?>
				</nav>
			</div>
		</div>
	</div>

	<!-- Scripts -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/util.js"></script>
	<script src="assets/js/main.js"></script>
</body>
</html>
