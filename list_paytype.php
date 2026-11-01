<?php
include('connect_db.php');
$res = mysqli_query($conn, "SELECT * FROM paytype ORDER BY paytype_id");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>การจ่ายเงิน (Paytype Setup) - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="index.php" class="logo"><strong>ข้อมูลพื้นฐานกลุ่ม Setup</strong> &gt; การจ่ายเงิน (Paytype)</a>
				</header>

				<section>
					<header class="main">
						<h2>ประเภทการชำระเงิน (Paytype Setup)</h2>
						<p>ข้อมูลประเภทการรับชำระเงิน เช่น cash, transfer ตามข้อกำหนดโจทย์</p>
					</header>

					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 140px;">รหัสประเภท</th>
									<th>ชื่อประเภทการชำระเงิน</th>
									<th>คำอธิบาย</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = mysqli_fetch_assoc($res)) {
								?>
								<tr>
									<td><strong><?php echo htmlspecialchars($row['paytype_id']); ?></strong></td>
									<td><span style="font-weight: bold; color: #1565c0;"><?php echo htmlspecialchars($row['paytype_name']); ?></span></td>
									<td><?php echo htmlspecialchars($row['description']); ?></td>
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
