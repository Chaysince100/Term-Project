<?php
include('connect_db.php');
$res = mysqli_query($conn, "SELECT * FROM restaurant_table ORDER BY table_no");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>หมายเลขโต๊ะ (Table Setup) - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="index.php" class="logo"><strong>ข้อมูลพื้นฐานกลุ่ม Setup</strong> &gt; หมายเลขโต๊ะ (Table)</a>
				</header>

				<section>
					<header class="main">
						<h2>หมายเลขโต๊ะหลักที่ลูกค้านั่ง (Table Setup)</h2>
						<p>ข้อมูลโต๊ะอาหาร โซนที่นั่ง และจำนวนที่นั่งรองรับ</p>
					</header>

					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 140px;">หมายเลขโต๊ะ</th>
									<th>โซนที่นั่ง (Zone)</th>
									<th style="text-align: center;">จำนวนที่นั่ง (ที่)</th>
									<th style="text-align: center;">สถานะปัจจุบัน</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = mysqli_fetch_assoc($res)) {
								?>
								<tr>
									<td><strong><?php echo htmlspecialchars($row['table_no']); ?></strong></td>
									<td><?php echo htmlspecialchars($row['zone']); ?></td>
									<td style="text-align: center;"><?php echo $row['seat_capacity']; ?> ที่นั่ง</td>
									<td style="text-align: center;">
										<span class="tag"><?php echo htmlspecialchars($row['status']); ?></span>
									</td>
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
