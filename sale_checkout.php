<?php
include('connect_db.php');

$sale_id = isset($_GET['sale_id']) ? trim($_GET['sale_id']) : '';

if ($sale_id == '') {
    echo "<script>alert('ไม่พบเลขที่บิล'); window.location='sale_list.php';</script>";
    exit;
}

$sale_q = mysqli_query($conn, "SELECT s.*, c.customer_name, c.phone 
                               FROM sales s 
                               LEFT JOIN customer c ON s.customer_id = c.customer_id 
                               WHERE s.sale_id = '" . mysqli_real_escape_string($conn, $sale_id) . "'");
$sale = mysqli_fetch_assoc($sale_q);

if (!$sale) {
    echo "<script>alert('ไม่พบข้อมูลการขาย'); window.location='sale_list.php';</script>";
    exit;
}

// รายการอาหารในบิลนี้
$items = mysqli_query($conn, "SELECT d.*, p.product_name 
                              FROM sale_details d 
                              LEFT JOIN product p ON d.product_id = p.product_id 
                              WHERE d.sale_id = '" . mysqli_real_escape_string($conn, $sale_id) . "'");

$paytypes = mysqli_query($conn, "SELECT * FROM paytype ORDER BY paytype_id");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>ชำระเงิน - บิล <?php echo $sale['sale_id']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<style>
		.checkout-box {
			background: #fafafa;
			border: 2px solid #2e7d32;
			padding: 25px;
			border-radius: 8px;
			margin-bottom: 25px;
		}
		.total-highlight {
			font-size: 2.2em;
			color: #2e7d32;
			font-weight: bold;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="sale_list.php" class="logo"><strong>ข้อมูลการทำงาน</strong> &gt; <a href="sale_list.php">รายการการขาย</a> &gt; ชำระเงิน</a>
				</header>

				<section>
					<header class="main">
						<h2>รับชำระเงินและปิดบิล: <?php echo htmlspecialchars($sale['sale_id']); ?></h2>
						<p>โต๊ะ: <strong><?php echo $sale['table_no']; ?></strong> | จำนวนลูกค้า: <strong><?php echo $sale['customer_count']; ?></strong> ท่าน | ลูกค้า: <strong><?php echo htmlspecialchars($sale['customer_name']); ?></strong></p>
					</header>

					<!-- ตารางสรุปรายการอาหาร -->
					<div class="table-wrapper">
						<h3>รายการอาหารที่สั่งทั้งหมด</h3>
						<table class="alt">
							<thead>
								<tr style="background-color: #f5f5f5;">
									<th style="width: 50px;">ลำดับ</th>
									<th>เมนูอาหาร</th>
									<th style="text-align: right;">ราคาต่อหน่วย (฿)</th>
									<th style="text-align: center;">จำนวน</th>
									<th style="text-align: right;">รวมเงิน (฿)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								while ($row = mysqli_fetch_assoc($items)) {
								?>
								<tr>
									<td><?php echo $no++; ?></td>
									<td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
									<td style="text-align: right;"><?php echo number_format($row['unit_price'], 2); ?></td>
									<td style="text-align: center;"><?php echo $row['quantity']; ?></td>
									<td style="text-align: right; font-weight: bold;"><?php echo number_format($row['subtotal'], 2); ?></td>
								</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr style="background-color: #e8f5e9; font-weight: bold;">
									<td colspan="4" style="text-align: right; font-size: 1.2em;">ยอดรวมที่ต้องชำระ:</td>
									<td style="text-align: right;" class="total-highlight"><?php echo number_format($sale['total_amount'], 2); ?> ฿</td>
								</tr>
							</tfoot>
						</table>
					</div>

					<!-- ฟอร์มการรับชำระเงิน -->
					<div class="checkout-box">
						<form method="post" action="save_checkout.php">
							<input type="hidden" name="sale_id" value="<?php echo htmlspecialchars($sale['sale_id']); ?>" />
							<input type="hidden" name="table_no" value="<?php echo htmlspecialchars($sale['table_no']); ?>" />
							<input type="hidden" id="billTotal" value="<?php echo $sale['total_amount']; ?>" />

							<h3>ข้อมูลการรับเงิน</h3>
							<div class="row uniform">
								<div class="6u 12u$(xsmall)">
									<label for="paytype_id">ประเภทการชำระเงิน *</label>
									<div class="select-wrapper">
										<select name="paytype_id" id="paytype_id" required>
											<?php
											while ($pt = mysqli_fetch_assoc($paytypes)) {
												$sel = ($pt['paytype_id'] == $sale['paytype_id']) ? "selected" : "";
												echo "<option value='{$pt['paytype_id']}' {$sel}>{$pt['paytype_name']}</option>";
											}
											?>
										</select>
									</div>
								</div>

								<div class="6u$ 12u$(xsmall)">
									<label for="received_amount">จำนวนเงินที่รับมา (บาท) [สำหรับเงินสด]</label>
									<input type="number" step="0.01" id="received_amount" value="<?php echo $sale['total_amount']; ?>" oninput="calcChange()" />
								</div>

								<div class="12u$" style="margin-top: 10px;">
									<h4>เงินทอน: <span id="changeDisplay" style="color: #c62828; font-weight: bold; font-size: 1.4em;">0.00</span> บาท</h4>
								</div>

								<div class="12u$" style="margin-top: 20px;">
									<ul class="actions">
										<li><input type="submit" value="ยืนยันรับชำระเงินและออกใบเสร็จ" class="special big" style="background-color: #2e7d32;" /></li>
										<li><a href="sale_list.php" class="button big">ยกเลิก</a></li>
									</ul>
								</div>
							</div>
						</form>
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
	<script>
		function calcChange() {
			var total = parseFloat(document.getElementById('billTotal').value) || 0;
			var received = parseFloat(document.getElementById('received_amount').value) || 0;
			var change = received - total;
			if (change < 0) {
				document.getElementById('changeDisplay').innerText = '0.00 (ยอดรับไม่พอ)';
				document.getElementById('changeDisplay').style.color = '#c62828';
			} else {
				document.getElementById('changeDisplay').innerText = change.toFixed(2);
				document.getElementById('changeDisplay').style.color = '#2e7d32';
			}
		}
		calcChange();
	</script>
</body>
</html>
