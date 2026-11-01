<?php
include('connect_db.php');
$res = mysqli_query($conn, "SELECT * FROM category ORDER BY category_id");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>ประเภทสินค้า (Category Setup) - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="index.php" class="logo"><strong>ข้อมูลพื้นฐานกลุ่ม Setup</strong> &gt; ประเภทสินค้า (Category)</a>
				</header>

				<section>
					<header class="main">
						<h2>ประเภทสินค้า (Category Setup)</h2>
						<p>ข้อมูลพื้นฐานกลุ่ม Setup สำหรับจัดหมวดหมู่เมนูอาหารและเครื่องดื่ม</p>
					</header>

					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 120px;">รหัสหมวดหมู่</th>
									<th>ชื่อหมวดหมู่ (Category Name)</th>
									<th>คำอธิบาย</th>
									<th style="text-align: center; width: 140px;">จำนวนสินค้าในระบบ</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = mysqli_fetch_assoc($res)) {
									$count_q = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM product WHERE category_id = '{$row['category_id']}'");
									$cnt = mysqli_fetch_assoc($count_q)['cnt'];
								?>
								<tr>
									<td><strong><?php echo htmlspecialchars($row['category_id']); ?></strong></td>
									<td><?php echo htmlspecialchars($row['category_name']); ?></td>
									<td><?php echo htmlspecialchars($row['description']); ?></td>
									<td style="text-align: center;"><span class="tag"><?php echo $cnt; ?> เมนู</span></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
					<p><small>* ข้อมูลกลุ่ม Setup จัดการผ่าน phpMyAdmin หรือสคริปต์ SQL ตามข้อกำหนดโจทย์</small></p>
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
