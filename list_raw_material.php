<?php
include('connect_db.php');
$res_mat = mysqli_query($conn, "SELECT * FROM raw_material ORDER BY material_id");
$res_rule = mysqli_query($conn, "SELECT r.*, p.product_name, m.material_name 
                                 FROM recipe_rule r 
                                 LEFT JOIN product p ON r.product_id = p.product_id 
                                 LEFT JOIN raw_material m ON r.material_id = m.material_id 
                                 ORDER BY r.product_id");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>วัตถุดิบและสูตรอาหาร (Rule) - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="index.php" class="logo"><strong>ข้อมูลพื้นฐาน</strong> &gt; วัตถุดิบและสูตรอาหาร (Rule)</a>
				</header>

				<section>
					<header class="main">
						<h2>ตารางวัตถุดิบ (Raw Material) &amp; สูตรอาหาร (Rule)</h2>
						<p>ตารางข้อมูลตามข้อกำหนดโจทย์ (สอดคล้องกับรายการอาหารและวัตถุดิบหลัก)</p>
					</header>

					<!-- Table Raw Material -->
					<h3>1. ข้อมูลวัตถุดิบหลัก (Raw Material)</h3>
					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 120px;">รหัสวัตถุดิบ</th>
									<th>ชื่อวัตถุดิบ</th>
									<th style="text-align: center;">หน่วยนับ</th>
									<th style="text-align: right; width: 140px;">ต้นทุนต่อหน่วย (฿)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($mat = mysqli_fetch_assoc($res_mat)) {
								?>
								<tr>
									<td><strong><?php echo htmlspecialchars($mat['material_id']); ?></strong></td>
									<td><?php echo htmlspecialchars($mat['material_name']); ?></td>
									<td style="text-align: center;"><?php echo htmlspecialchars($mat['unit']); ?></td>
									<td style="text-align: right; color: #c62828; font-weight: bold;"><?php echo number_format($mat['unit_cost'], 2); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>

					<hr class="major" />

					<!-- Table Recipe Rules -->
					<h3>2. สูตรอาหาร (Rule ว่าใช้วัตถุดิบใด - เฉพาะวัตถุดิบหลัก)</h3>
					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 80px; text-align: center;">ลำดับ</th>
									<th>รหัสเมนูอาหาร</th>
									<th>ชื่อเมนูอาหาร</th>
									<th>วัตถุดิบหลักที่ใช้</th>
									<th style="text-align: right;">ปริมาณที่ใช้</th>
									<th style="text-align: center;">หน่วย</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								while ($rule = mysqli_fetch_assoc($res_rule)) {
								?>
								<tr>
									<td style="text-align: center;"><?php echo $no++; ?></td>
									<td><strong><?php echo htmlspecialchars($rule['product_id']); ?></strong></td>
									<td><span style="font-weight: bold; color: #1565c0;"><?php echo htmlspecialchars($rule['product_name']); ?></span></td>
									<td><?php echo htmlspecialchars($rule['material_name']); ?></td>
									<td style="text-align: right; font-weight: bold;"><?php echo number_format($rule['amount_needed'], 2); ?></td>
									<td style="text-align: center;"><?php echo htmlspecialchars($rule['unit']); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
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
