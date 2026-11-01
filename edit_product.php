<?php
include('connect_db.php');

$product_id = isset($_GET['product_id']) ? trim($_GET['product_id']) : '';

if ($product_id == '') {
    echo "<script>alert('ไม่พบรหัสสินค้า'); window.location='manage_product.php';</script>";
    exit;
}

$sql = "SELECT * FROM product WHERE product_id = '" . mysqli_real_escape_string($conn, $product_id) . "'";
$res = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($res);

if (!$product) {
    echo "<script>alert('ไม่พบข้อมูลสินค้าที่ต้องการแก้ไข'); window.location='manage_product.php';</script>";
    exit;
}
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>แก้ไขข้อมูลสินค้า - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="manage_product.php" class="logo"><strong>ข้อมูลพื้นฐาน</strong> &gt; แก้ไขข้อมูลสินค้า</a>
				</header>

				<section>
					<header class="main">
						<h2>แก้ไขข้อมูลสินค้า: <?php echo htmlspecialchars($product['product_name']); ?></h2>
					</header>

					<form method="post" action="update_product.php">
						<div class="row uniform">
							<div class="6u 12u$(xsmall)">
								<label for="product_id">รหัสสินค้า</label>
								<input type="text" name="product_id" id="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>" readonly style="background-color: #f1f1f1;" />
							</div>

							<div class="6u$ 12u$(xsmall)">
								<label for="category_id">หมวดหมู่สินค้า *</label>
								<div class="select-wrapper">
									<select name="category_id" id="category_id" required>
										<?php
										$cats = mysqli_query($conn, "SELECT * FROM category ORDER BY category_id");
										while ($c = mysqli_fetch_assoc($cats)) {
											$sel = ($c['category_id'] == $product['category_id']) ? "selected" : "";
											echo "<option value='{$c['category_id']}' {$sel}>{$c['category_name']}</option>";
										}
										?>
									</select>
								</div>
							</div>

							<div class="12u$">
								<label for="product_name">ชื่อสินค้า / เมนูอาหาร *</label>
								<input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required />
							</div>

							<div class="6u 12u$(xsmall)">
								<label for="price">ราคาขาย (บาท) *</label>
								<input type="number" step="0.01" name="price" id="price" value="<?php echo $product['price']; ?>" required />
							</div>

							<div class="6u$ 12u$(xsmall)">
								<label for="cost">ต้นทุนสินค้า (บาท) *</label>
								<input type="number" step="0.01" name="cost" id="cost" value="<?php echo $product['cost']; ?>" required />
							</div>

							<div class="6u 12u$(xsmall)">
								<label for="status">สถานะสินค้า</label>
								<div class="select-wrapper">
									<select name="status" id="status">
										<option value="พร้อมขาย" <?php if ($product['status'] == 'พร้อมขาย') echo 'selected'; ?>>พร้อมขาย</option>
										<option value="สินค้าหมด" <?php if ($product['status'] == 'สินค้าหมด') echo 'selected'; ?>>สินค้าหมด</option>
										<option value="ยกเลิก" <?php if ($product['status'] == 'ยกเลิก') echo 'selected'; ?>>ยกเลิก</option>
									</select>
								</div>
							</div>

							<div class="12u$">
								<label for="description">รายละเอียดเพิ่มเติม</label>
								<textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
							</div>

							<div class="12u$">
								<ul class="actions">
									<li><input type="submit" value="บันทึกการแก้ไข" class="special" /></li>
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
