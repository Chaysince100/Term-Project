<?php
include('connect_db.php');

// สร้างรหัสบิลอัตโนมัติตามวันที่ เช่น INV20261101-002
$today_str = date('Ymd');
$prefix = "INV" . $today_str . "-";
$res_max = mysqli_query($conn, "SELECT sale_id FROM sales WHERE sale_id LIKE '{$prefix}%' ORDER BY sale_id DESC LIMIT 1");
$next_sale_id = $prefix . "001";
if ($row_max = mysqli_fetch_assoc($res_max)) {
    $cur_seq = intval(substr($row_max['sale_id'], -3)) + 1;
    $next_sale_id = $prefix . str_pad($cur_seq, 3, '0', STR_PAD_LEFT);
}

// ดึงรายชื่อโต๊ะ
$tables = mysqli_query($conn, "SELECT * FROM restaurant_table ORDER BY table_no");
// ดึงรายชื่อลูกค้า
$customers = mysqli_query($conn, "SELECT * FROM customer ORDER BY customer_id");
// ดึงประเภทการชำระเงิน
$paytypes = mysqli_query($conn, "SELECT * FROM paytype ORDER BY paytype_id");
// ดึงรายการอาหาร
$products = mysqli_query($conn, "SELECT * FROM product WHERE status != 'ยกเลิก' ORDER BY category_id, product_name");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>บันทึกการขาย / เปิดโต๊ะ - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<style>
		.order-box {
			background: #fafafa;
			border: 1px solid #ddd;
			padding: 20px;
			border-radius: 6px;
			margin-bottom: 20px;
		}
		.item-row {
			background: #fff;
			border: 1px solid #eee;
			padding: 10px 15px;
			margin-bottom: 10px;
			border-radius: 4px;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="sale_list.php" class="logo"><strong>ข้อมูลการทำงาน</strong> &gt; บันทึกการขาย (เปิดโต๊ะใหม่)</a>
				</header>

				<section>
					<header class="main">
						<h2>บันทึกการขายและเปิดโต๊ะใหม่</h2>
						<p>บันทึกข้อมูลโต๊ะ จำนวนลูกค้า สั่งรายการอาหาร และรับชำระเงิน</p>
					</header>

					<form method="post" action="save_sale.php" id="saleForm">
						<!-- ส่วนที่ 1: ข้อมูลบิล & โต๊ะ & ลูกค้า -->
						<div class="order-box">
							<h3>1. ข้อมูลการขายและลูกค้า</h3>
							<div class="row uniform">
								<div class="4u 12u$(xsmall)">
									<label for="sale_id">เลขที่การขาย (บิล) *</label>
									<input type="text" name="sale_id" id="sale_id" value="<?php echo $next_sale_id; ?>" readonly style="background-color: #f1f1f1;" />
								</div>
								<div class="4u 12u$(xsmall)">
									<label for="sale_date">วันที่และเวลาขาย *</label>
									<input type="datetime-local" name="sale_date" id="sale_date" value="<?php echo date('Y-m-d\TH:i'); ?>" required />
								</div>
								<div class="4u$ 12u$(xsmall)">
									<label for="table_no">หมายเลขโต๊ะ *</label>
									<div class="select-wrapper">
										<select name="table_no" id="table_no" required>
											<option value="">-- เลือกโต๊ะ --</option>
											<?php
											while ($t = mysqli_fetch_assoc($tables)) {
												echo "<option value='{$t['table_no']}'>{$t['table_no']} ({$t['zone']}) - {$t['status']}</option>";
											}
											?>
										</select>
									</div>
								</div>

								<div class="4u 12u$(xsmall)">
									<label for="customer_count">จำนวนลูกค้า (ท่าน) *</label>
									<input type="number" name="customer_count" id="customer_count" value="1" min="1" max="50" required />
								</div>
								<div class="4u 12u$(xsmall)">
									<label for="customer_id">ลูกค้า / สมาชิก</label>
									<div class="select-wrapper">
										<select name="customer_id" id="customer_id">
											<?php
											while ($c = mysqli_fetch_assoc($customers)) {
												$sel = ($c['customer_id'] == 'CUST010') ? 'selected' : '';
												echo "<option value='{$c['customer_id']}' {$sel}>{$c['customer_name']} ({$c['customer_id']})</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="4u$ 12u$(xsmall)">
									<label for="paytype_id">ประเภทการชำระเงิน</label>
									<div class="select-wrapper">
										<select name="paytype_id" id="paytype_id">
											<?php
											while ($pt = mysqli_fetch_assoc($paytypes)) {
												echo "<option value='{$pt['paytype_id']}'>{$pt['paytype_name']}</option>";
											}
											?>
										</select>
									</div>
								</div>
							</div>
						</div>

						<!-- ส่วนที่ 2: เลือกรายการอาหารที่สั่ง -->
						<div class="order-box">
							<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
								<h3 style="margin: 0;">2. รายการอาหารที่สั่ง</h3>
								<button type="button" class="button small special" onclick="addRow();">+ เพิ่มแถวรายการอาหาร</button>
							</div>

							<div id="itemsContainer">
								<!-- แถวที่ 1 เริ่มต้น -->
								<div class="item-row row uniform">
									<div class="6u 12u$(xsmall)">
										<label>เลือกเมนูอาหาร</label>
										<div class="select-wrapper">
											<select name="prod_id[]" class="prod-select" onchange="updateRowPrice(this)" required>
												<option value="" data-price="0">-- เลือกเมนู --</option>
												<?php
												mysqli_data_seek($products, 0);
												while ($p = mysqli_fetch_assoc($products)) {
													echo "<option value='{$p['product_id']}' data-price='{$p['price']}'>{$p['product_name']} ({$p['price']} ฿)</option>";
												}
												?>
											</select>
										</div>
									</div>
									<div class="2u 6u(xsmall)">
										<label>จำนวน</label>
										<input type="number" name="qty[]" class="qty-input" value="1" min="1" oninput="calculateTotal()" required />
									</div>
									<div class="2u 6u$(xsmall)">
										<label>ราคา/หน่วย (฿)</label>
										<input type="text" name="price[]" class="price-input" value="0.00" readonly style="background-color: #f1f1f1;" />
									</div>
									<div class="2u$ 12u$(xsmall)" style="padding-top: 28px;">
										<button type="button" class="button small" onclick="removeRow(this)" style="color: #c62828;">ลบแถว</button>
									</div>
								</div>
							</div>

							<div style="text-align: right; margin-top: 15px; font-size: 1.3em;">
								<strong>ยอดรวมทั้งสิ้น: <span id="displayTotal" style="color: #2e7d32; font-weight: bold;">0.00</span> บาท</strong>
							</div>
						</div>

						<!-- ส่วนที่ 3: สถานะการชำระเงิน -->
						<div class="order-box">
							<h3>3. สถานะการชำระเงิน</h3>
							<div class="row uniform">
								<div class="6u 12u$(small)">
									<input type="radio" id="status_paid" name="payment_status" value="paid" checked>
									<label for="status_paid"><strong>ชำระเงินทันที</strong> (พร้อมออกใบเสร็จ)</label>
								</div>
								<div class="6u$ 12u$(small)">
									<input type="radio" id="status_pending" name="payment_status" value="pending">
									<label for="status_pending"><strong>เปิดโต๊ะ / รอชำระเงิน</strong> (ลูกค้านั่งทาน สามารถสั่งอาหารเพิ่มได้)</label>
								</div>
							</div>
						</div>

						<ul class="actions">
							<li><input type="submit" value="บันทึกการขาย" class="special big" /></li>
							<li><a href="sale_list.php" class="button big">ยกเลิก / ไปหน้ารายการขาย</a></li>
						</ul>
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
	<script>
		function updateRowPrice(selectElem) {
			var selectedOption = selectElem.options[selectElem.selectedIndex];
			var price = selectedOption.getAttribute('data-price') || 0;
			var row = selectElem.closest('.item-row');
			row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
			calculateTotal();
		}

		function calculateTotal() {
			var rows = document.querySelectorAll('.item-row');
			var total = 0;
			rows.forEach(function(row) {
				var qty = parseFloat(row.querySelector('.qty-input').value) || 0;
				var price = parseFloat(row.querySelector('.price-input').value) || 0;
				total += (qty * price);
			});
			document.getElementById('displayTotal').innerText = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
		}

		function addRow() {
			var container = document.getElementById('itemsContainer');
			var firstRow = container.querySelector('.item-row');
			var clone = firstRow.cloneNode(true);
			
			clone.querySelector('.prod-select').selectedIndex = 0;
			clone.querySelector('.qty-input').value = 1;
			clone.querySelector('.price-input').value = '0.00';
			container.appendChild(clone);
		}

		function removeRow(btn) {
			var container = document.getElementById('itemsContainer');
			var rows = container.querySelectorAll('.item-row');
			if (rows.length > 1) {
				btn.closest('.item-row').remove();
				calculateTotal();
			} else {
				alert('ต้องมีรายการอาหารอย่างน้อย 1 รายการ');
			}
		}
	</script>
</body>
</html>
