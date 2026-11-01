<?php
include('connect_db.php');
$res = mysqli_query($conn, "SELECT * FROM expense_type ORDER BY expense_type_id");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>ประเภทค่าสาธารณูปโภค (Expense Type) - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="index.php" class="logo"><strong>ข้อมูลพื้นฐานกลุ่ม Setup</strong> &gt; ประเภทค่าใช้จ่ายสาธารณูปโภค</a>
				</header>

				<section>
					<header class="main">
						<h2>ประเภทค่าใช้จ่ายจากสาธารณูปโภค (Expense Type Setup)</h2>
						<p>ข้อมูลประเภทค่าใช้จ่ายสาธารณูปโภคสำหรับใช้คำนวณในรายงานกำไร-ขาดทุนเบื้องต้น</p>
					</header>

					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 140px;">รหัสประเภท</th>
									<th>ชื่อค่าใช้จ่ายสาธารณูปโภค</th>
									<th>รอบ/รูปแบบการคิดค่าบริการ</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = mysqli_fetch_assoc($res)) {
								?>
								<tr>
									<td><strong><?php echo htmlspecialchars($row['expense_type_id']); ?></strong></td>
									<td><strong style="color: #c62828;"><?php echo htmlspecialchars($row['expense_type_name']); ?></strong></td>
									<td><?php echo htmlspecialchars($row['unit_charge']); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>

					<hr class="major" />

					<h3>ตัวอย่างรายการค่าสาธารณูปโภคที่บันทึกในระบบ (Utility Expense Data)</h3>
					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th style="width: 120px;">วันที่บันทึก</th>
									<th>ประเภทค่าใช้จ่าย</th>
									<th>หมายเหตุ</th>
									<th style="text-align: right; width: 140px;">จำนวนเงิน (฿)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$res_exp = mysqli_query($conn, "SELECT u.*, e.expense_type_name 
								                                FROM utility_expense u 
								                                LEFT JOIN expense_type e ON u.expense_type_id = e.expense_type_id 
								                                ORDER BY u.expense_date DESC");
								while ($exp = mysqli_fetch_assoc($res_exp)) {
								?>
								<tr>
									<td><?php echo $exp['expense_date']; ?></td>
									<td><?php echo htmlspecialchars($exp['expense_type_name']); ?></td>
									<td><?php echo htmlspecialchars($exp['remark']); ?></td>
									<td style="text-align: right; color: #c62828; font-weight: bold;"><?php echo number_format($exp['amount'], 2); ?></td>
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
