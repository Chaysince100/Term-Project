<?php
include('connect_db.php');

$s_sale_id = isset($_REQUEST['s_sale_id']) ? trim($_REQUEST['s_sale_id']) : '';
$s_table_no = isset($_REQUEST['s_table_no']) ? trim($_REQUEST['s_table_no']) : '';
$s_status = isset($_REQUEST['s_status']) ? trim($_REQUEST['s_status']) : '';
$s_cust_id = isset($_REQUEST['s_cust_id']) ? trim($_REQUEST['s_cust_id']) : '';

$sql = "SELECT s.*, c.customer_name, c.phone, pt.paytype_name 
        FROM sales s 
        LEFT JOIN customer c ON s.customer_id = c.customer_id 
        LEFT JOIN paytype pt ON s.paytype_id = pt.paytype_id 
        WHERE 1=1 ";

if ($s_sale_id != '') {
    $sql .= " AND s.sale_id LIKE '%" . mysqli_real_escape_string($conn, $s_sale_id) . "%' ";
}
if ($s_table_no != '') {
    $sql .= " AND s.table_no = '" . mysqli_real_escape_string($conn, $s_table_no) . "' ";
}
if ($s_status != '') {
    $sql .= " AND s.payment_status = '" . mysqli_real_escape_string($conn, $s_status) . "' ";
}
if ($s_cust_id != '') {
    $sql .= " AND s.customer_id = '" . mysqli_real_escape_string($conn, $s_cust_id) . "' ";
}

$sql .= " ORDER BY s.sale_date DESC ";
$record = mysqli_query($conn, $sql);
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>รายการการขายและการรับชำระ - บูรพาสปาเก็ตตี้</title>
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
		.status-badge-pending {
			background-color: #ff9800;
			color: #fff;
			padding: 4px 8px;
			border-radius: 4px;
			font-size: 0.85em;
			font-weight: bold;
		}
		.status-badge-paid {
			background-color: #4caf50;
			color: #fff;
			padding: 4px 8px;
			border-radius: 4px;
			font-size: 0.85em;
			font-weight: bold;
		}
		.action-btn {
			margin-right: 4px;
			margin-bottom: 4px;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="sale_list.php" class="logo"><strong>ข้อมูลการทำงาน</strong> &gt; รายการการขาย (เพิ่ม / สั่งเพิ่ม / ชำระเงิน / Zoom ใบเสร็จ)</a>
				</header>

				<section>
					<header class="main">
						<h2>รายการการขายและการรับชำระเงิน</h2>
						<p>การขาย(บันทึกจำนวนลูกค้า) และการรับชำระ (เพิ่ม / สั่งเพิ่ม / ชำระเงิน - zoom เป็นใบเสร็จ)</p>
					</header>

					<!-- Filter Form -->
					<div class="search-panel">
						<form method="get" action="sale_list.php">
							<div class="row uniform">
								<div class="3u 12u$(xsmall)">
									<label for="s_sale_id">เลขที่การขาย (บิล)</label>
									<input type="text" name="s_sale_id" id="s_sale_id" value="<?php echo htmlspecialchars($s_sale_id); ?>" placeholder="เช่น INV20261101-001" />
								</div>
								<div class="2u 12u$(xsmall)">
									<label for="s_table_no">โต๊ะอาหาร</label>
									<div class="select-wrapper">
										<select name="s_table_no" id="s_table_no">
											<option value="">-- ทุกโต๊ะ --</option>
											<?php
											$t_res = mysqli_query($conn, "SELECT table_no FROM restaurant_table ORDER BY table_no");
											while ($t = mysqli_fetch_assoc($t_res)) {
												$sel = ($s_table_no == $t['table_no']) ? "selected" : "";
												echo "<option value='{$t['table_no']}' {$sel}>{$t['table_no']}</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="3u 12u$(xsmall)">
									<label for="s_status">สถานะบิล</label>
									<div class="select-wrapper">
										<select name="s_status" id="s_status">
											<option value="">-- ทุกสถานะ --</option>
											<option value="pending" <?php if ($s_status == 'pending') echo 'selected'; ?>>รอชำระเงิน (กำลังทาน)</option>
											<option value="paid" <?php if ($s_status == 'paid') echo 'selected'; ?>>ชำระเงินแล้ว</option>
										</select>
									</div>
								</div>
								<div class="4u$ 12u$(xsmall)" style="padding-top: 28px;">
									<input type="submit" value="ค้นหา" class="special" />
									<a href="sale_list.php" class="button">ล้างค่า</a>
									<a href="sale_add.php" class="button special" style="background-color: #2e7d32;">+ บันทึกขายใหม่</a>
								</div>
							</div>
						</form>
					</div>

					<!-- Table of Sales -->
					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th>เลขที่บิล (Zoom)</th>
									<th>วันที่-เวลา</th>
									<th style="text-align: center;">โต๊ะ</th>
									<th style="text-align: center;">จำนวนลูกค้า</th>
									<th>ลูกค้า</th>
									<th>การชำระเงิน</th>
									<th style="text-align: right;">ยอดรวม (฿)</th>
									<th style="text-align: center;">สถานะ</th>
									<th style="text-align: center; width: 220px;">การทำงาน</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if (mysqli_num_rows($record) > 0) {
									while ($row = mysqli_fetch_assoc($record)) {
								?>
								<tr>
									<td>
										<a href="zoom_receipt.php?sale_id=<?php echo urlencode($row['sale_id']); ?>" style="font-weight: bold; color: #f56a6a;">
											🔍 <?php echo htmlspecialchars($row['sale_id']); ?>
										</a>
									</td>
									<td><?php echo $row['sale_date']; ?></td>
									<td style="text-align: center;"><span class="tag"><?php echo htmlspecialchars($row['table_no']); ?></span></td>
									<td style="text-align: center;"><strong><?php echo $row['customer_count']; ?></strong> ท่าน</td>
									<td><?php echo htmlspecialchars($row['customer_name'] ? $row['customer_name'] : 'ลูกค้าทั่วไป'); ?></td>
									<td><?php echo htmlspecialchars($row['paytype_name']); ?></td>
									<td style="text-align: right; font-weight: bold; color: #2e7d32;"><?php echo number_format($row['total_amount'], 2); ?></td>
									<td style="text-align: center;">
										<?php if ($row['payment_status'] == 'paid') { ?>
											<span class="status-badge-paid">ชำระแล้ว</span>
										<?php } else { ?>
											<span class="status-badge-pending">รอชำระ</span>
										<?php } ?>
									</td>
									<td style="text-align: center;">
										<?php if ($row['payment_status'] == 'pending') { ?>
											<a href="sale_add_items.php?sale_id=<?php echo urlencode($row['sale_id']); ?>" class="button small action-btn" style="background-color: #0288d1; color:#fff;" title="สั่งอาหารเพิ่มในบิลเดิม">+ สั่งเพิ่ม</a>
											<a href="sale_checkout.php?sale_id=<?php echo urlencode($row['sale_id']); ?>" class="button small action-btn special" style="background-color: #388e3c;" title="ชำระเงิน">ชำระเงิน</a>
										<?php } else { ?>
											<a href="zoom_receipt.php?sale_id=<?php echo urlencode($row['sale_id']); ?>" class="button small action-btn" title="ดูใบเสร็จ">Zoom ใบเสร็จ</a>
										<?php } ?>
									</td>
								</tr>
								<?php
									}
								} else {
									echo "<tr><td colspan='9' style='text-align:center;'>ไม่พบรายการข้อมูลการขายตามเงื่อนไข</td></tr>";
								}
								?>
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
