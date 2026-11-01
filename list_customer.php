<?php
include('connect_db.php');
$res = mysqli_query($conn, "SELECT * FROM customer ORDER BY customer_id");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>ข้อมูลลูกค้า (Customer) - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="index.php" class="logo"><strong>ข้อมูลพื้นฐาน</strong> &gt; ข้อมูลลูกค้า (Customer)</a>
				</header>

				<section>
					<header class="main">
						<h2>ข้อมูลลูกค้า (Customer Information)</h2>
						<p>รายชื่อสมาชิกลูกค้าประจำและข้อมูลติดต่อ (กรอกข้อมูลในระบบมากกว่า 8 รายการตามข้อกำหนด)</p>
					</header>

					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 120px;">รหัสลูกค้า</th>
									<th>ชื่อ-นามสกุลลูกค้า</th>
									<th>เบอร์โทรศัพท์</th>
									<th>อีเมล</th>
									<th style="text-align: center;">คะแนนสะสม</th>
									<th style="text-align: center; width: 140px;">ดูประวัติการซื้อ</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = mysqli_fetch_assoc($res)) {
								?>
								<tr>
									<td><strong><?php echo htmlspecialchars($row['customer_id']); ?></strong></td>
									<td><?php echo htmlspecialchars($row['customer_name']); ?></td>
									<td><?php echo htmlspecialchars($row['phone']); ?></td>
									<td><?php echo htmlspecialchars($row['email']); ?></td>
									<td style="text-align: center;"><span class="tag"><?php echo $row['member_point']; ?> แต้ม</span></td>
									<td style="text-align: center;">
										<a href="sale_list.php?s_cust_id=<?php echo urlencode($row['customer_id']); ?>" class="button small">ดูประวัติบิล</a>
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
