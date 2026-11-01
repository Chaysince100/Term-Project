<?php
include('connect_db.php');

// สุ่มสร้างรหัสสินค้าเริ่มต้นอัตโนมัติ
$res_max = mysqli_query($conn, "SELECT product_id FROM product WHERE product_id LIKE 'P%' ORDER BY product_id DESC LIMIT 1");
$next_id = 'P056';
if ($row_max = mysqli_fetch_assoc($res_max)) {
    $num = intval(substr($row_max['product_id'], 1)) + 1;
    $next_id = 'P' . str_pad($num, 3, '0', STR_PAD_LEFT);
}
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>เพิ่มสินค้าใหม่ - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="manage_product.php" class="logo"><strong>ข้อมูลพื้นฐาน</strong> &gt; เพิ่มสินค้าใหม่</a>
				</header>

				<section>
					<header class="main">
						<h2>เพิ่มข้อมูลสินค้า / เมนูอาหารใหม่</h2>
					</header>

					<form method="post" action="save_product.php">
						<div class="row uniform">
							<div class="6u 12u$(xsmall)">
								<label for="product_id">รหัสสินค้า *</label>
								<input type="text" name="product_id" id="product_id" value="<?php echo $next_id; ?>" required />
							</div>

							<div class="6u$ 12u$(xsmall)">
								<label for="category_id">หมวดหมู่สินค้า *</label>
								<div class="select-wrapper">
									<select name="category_id" id="category_id" required>
										<option value="">-- เลือกหมวดหมู่ --</option>
										<?php
										$cats = mysqli_query($conn, "SELECT * FROM category ORDER BY category_id");
										while ($c = mysqli_fetch_assoc($cats)) {
											echo "<option value='{$c['category_id']}'>{$c['category_name']}</option>";
										}
										?>
									</select>
								</div>
							</div>

							<div class="12u$">
								<label for="product_name">ชื่อสินค้า / เมนูอาหาร *</label>
								<input type="text" name="product_name" id="product_name" placeholder="ระบุชื่อเมนู" required />
							</div>

							<div class="6u 12u$(xsmall)">
								<label for="price">ราคาขาย (บาท) *</label>
								<input type="number" step="0.01" name="price" id="price" placeholder="0.00" required />
							</div>

							<div class="6u$ 12u$(xsmall)">
								<label for="cost">ต้นทุนสินค้า (บาท) *</label>
								<input type="number" step="0.01" name="cost" id="cost" placeholder="0.00" required />
							</div>

							<div class="6u 12u$(xsmall)">
								<label for="status">สถานะสินค้า</label>
								<div class="select-wrapper">
									<select name="status" id="status">
										<option value="พร้อมขาย">พร้อมขาย</option>
										<option value="สินค้าหมด">สินค้าหมด</option>
										<option value="ยกเลิก">ยกเลิก</option>
									</select>
								</div>
							</div>

							<div class="12u$">
								<label for="description">รายละเอียดเพิ่มเติม</label>
								<textarea name="description" id="description" placeholder="รายละเอียดหรือส่วนผสม..." rows="4"></textarea>
							</div>

							<div class="12u$">
								<ul class="actions">
									<li><input type="submit" value="บันทึกข้อมูล" class="special" /></li>
									<li><a href="manage_product.php" class="button">ยกเลิก / กลับ</a></li>
								</ul>
							</div>
						</div>
					</form>
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
