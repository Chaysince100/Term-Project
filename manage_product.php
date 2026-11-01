<?php
include('connect_db.php');

$s_prod_id = isset($_POST['s_prod_id']) ? trim($_POST['s_prod_id']) : '';
$s_prod_name = isset($_POST['s_prod_name']) ? trim($_POST['s_prod_name']) : '';
$s_cat_id = isset($_POST['s_cat_id']) ? trim($_POST['s_cat_id']) : '';

// สร้างคำสั่ง SQL สำหรับค้นหา
$sql = "SELECT p.*, c.category_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        WHERE 1=1 ";

if ($s_prod_id != '') {
    $sql .= " AND p.product_id LIKE '%" . mysqli_real_escape_string($conn, $s_prod_id) . "%' ";
}
if ($s_prod_name != '') {
    $sql .= " AND p.product_name LIKE '%" . mysqli_real_escape_string($conn, $s_prod_name) . "%' ";
}
if ($s_cat_id != '') {
    $sql .= " AND p.category_id = '" . mysqli_real_escape_string($conn, $s_cat_id) . "' ";
}

$sql .= " ORDER BY p.category_id, p.product_id ";
$record = mysqli_query($conn, $sql);
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>จัดการข้อมูลสินค้า - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<style>
		.search-panel {
			background: #fdfdfd;
			padding: 20px;
			border: 1px solid #e5e5e5;
			border-radius: 6px;
			margin-bottom: 25px;
		}
		.zoom-link {
			color: #f56a6a;
			font-weight: bold;
			text-decoration: underline;
		}
		.zoom-link:hover {
			color: #a00;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="manage_product.php" class="logo"><strong>ข้อมูลพื้นฐาน</strong> &gt; จัดการข้อมูลสินค้า</a>
				</header>

				<section>
					<header class="main">
						<h2>การจัดการข้อมูลสินค้า (Product Management)</h2>
						<p>ระบบค้นหา เพิ่ม แก้ไข และ <strong>Zoom ดูประวัติการขายสินค้า</strong></p>
					</header>

					<!-- Search Form -->
					<div class="search-panel">
						<form method="post" action="manage_product.php">
							<div class="row uniform">
								<div class="3u 12u$(xsmall)">
									<label for="s_prod_id">รหัสสินค้า</label>
									<input type="text" name="s_prod_id" id="s_prod_id" value="<?php echo htmlspecialchars($s_prod_id); ?>" placeholder="เช่น P001" />
								</div>
								<div class="3u 12u$(xsmall)">
									<label for="s_prod_name">ชื่อสินค้า / เมนู</label>
									<input type="text" name="s_prod_name" id="s_prod_name" value="<?php echo htmlspecialchars($s_prod_name); ?>" placeholder="เช่น สปาเก็ตตี้, สเต็ก" />
								</div>
								<div class="3u 12u$(xsmall)">
									<label for="s_cat_id">หมวดหมู่</label>
									<div class="select-wrapper">
										<select name="s_cat_id" id="s_cat_id">
											<option value="">-- ทุกหมวดหมู่ --</option>
											<?php
											$cat_query = mysqli_query($conn, "SELECT * FROM category ORDER BY category_id");
											while ($cat = mysqli_fetch_assoc($cat_query)) {
												$sel = ($s_cat_id == $cat['category_id']) ? "selected" : "";
												echo "<option value='{$cat['category_id']}' {$sel}>{$cat['category_name']}</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="3u$ 12u$(xsmall)" style="padding-top: 28px;">
									<input type="submit" name="btnSearch" value="ค้นหา" class="special" />
									<a href="manage_product.php" class="button">ล้างค่า</a>
									<a href="add_product.php" class="button special" style="background-color: #2e7d32;">+ เพิ่มสินค้าใหม่</a>
								</div>
							</div>
						</form>
					</div>

					<!-- Product Table -->
					<div class="table-wrapper">
						<p><strong>คำแนะนำ:</strong> คลิกที่ <span style="color: #f56a6a; font-weight: bold;">[รหัสสินค้า]</span> เพื่อ <strong>Zoom ดูประวัติการขายสินค้า</strong> ว่าขายให้ใคร เลขที่บิลใด และวันที่เท่าไร</p>
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 120px; text-align: center;">รหัสสินค้า (Zoom)</th>
									<th>ชื่อสินค้า / เมนู</th>
									<th>หมวดหมู่</th>
									<th style="text-align: right;">ราคาขาย (฿)</th>
									<th style="text-align: right;">ต้นทุน (฿)</th>
									<th style="text-align: center;">สถานะ</th>
									<th style="text-align: center; width: 100px;">จัดการ</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if (mysqli_num_rows($record) > 0) {
									while ($row = mysqli_fetch_assoc($record)) {
								?>
								<tr>
									<td style="text-align: center;">
										<a class="zoom-link" href="zoom_product.php?product_id=<?php echo urlencode($row['product_id']); ?>" title="คลิกเพื่อ Zoom ดูประวัติการขาย">
											🔍 <?php echo htmlspecialchars($row['product_id']); ?>
										</a>
									</td>
									<td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
									<td><?php echo htmlspecialchars($row['category_name']); ?></td>
									<td style="text-align: right; color: #1565c0; font-weight: bold;"><?php echo number_format($row['price'], 2); ?></td>
									<td style="text-align: right; color: #c62828;"><?php echo number_format($row['cost'], 2); ?></td>
									<td style="text-align: center;">
										<span class="tag"><?php echo htmlspecialchars($row['status']); ?></span>
									</td>
									<td style="text-align: center;">
										<a href="edit_product.php?product_id=<?php echo urlencode($row['product_id']); ?>" title="แก้ไขข้อมูล">
											<img src="images/edit.gif" alt="แก้ไข" style="vertical-align: middle;" />
										</a>
										&nbsp;
										<a href="delete_product.php?product_id=<?php echo urlencode($row['product_id']); ?>" onclick="return confirm('ยืนยันลบสินค้านี้?');" title="ลบข้อมูล" style="color: #e53935; font-weight: bold; text-decoration: none;">
											✕
										</a>
									</td>
								</tr>
								<?php
									}
								} else {
									echo "<tr><td colspan='7' style='text-align:center;'>ไม่พบรายการสินค้าตามเงื่อนไขที่ค้นหา</td></tr>";
								}
								?>
							</tbody>
						</table>
					</div>
				</section>

				<footer id="footer">
					<p class="copyright">ร้านบูรพาสปาเก็ตตี้ &copy; Term Project</p>
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
