<?php
include('connect_db.php');

$sale_id = isset($_GET['sale_id']) ? trim($_GET['sale_id']) : '';

if ($sale_id == '') {
    echo "<script>alert('ไม่พบเลขที่บิล'); window.location='sale_list.php';</script>";
    exit;
}

$sale_q = mysqli_query($conn, "SELECT s.*, c.customer_name 
                               FROM sales s 
                               LEFT JOIN customer c ON s.customer_id = c.customer_id 
                               WHERE s.sale_id = '" . mysqli_real_escape_string($conn, $sale_id) . "'");
$sale = mysqli_fetch_assoc($sale_q);

if (!$sale) {
    echo "<script>alert('ไม่พบข้อมูลการขาย'); window.location='sale_list.php';</script>";
    exit;
}

// รายการอาหารเดิมที่เคยสั่ง
$existing_items = mysqli_query($conn, "SELECT d.*, p.product_name 
                                       FROM sale_details d 
                                       LEFT JOIN product p ON d.product_id = p.product_id 
                                       WHERE d.sale_id = '" . mysqli_real_escape_string($conn, $sale_id) . "'");

// รายการอาหารสำหรับเลือกสั่งเพิ่ม
$products = mysqli_query($conn, "SELECT * FROM product WHERE status != 'ยกเลิก' ORDER BY category_id, product_name");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>สั่งอาหารเพิ่ม - บิล <?php echo $sale['sale_id']; ?></title>
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
					<a href="sale_list.php" class="logo"><strong>ข้อมูลการทำงาน</strong> &gt; <a href="sale_list.php">รายการการขาย</a> &gt; สั่งอาหารเพิ่ม</a>
				</header>

				<section>
					<header class="main">
						<h2>สั่งอาหารเพิ่มในบิล: <?php echo htmlspecialchars($sale['sale_id']); ?></h2>
						<p>โต๊ะ: <strong><?php echo $sale['table_no']; ?></strong> | จำนวนลูกค้า: <strong><?php echo $sale['customer_count']; ?></strong> ท่าน | ลูกค้า: <strong><?php echo htmlspecialchars($sale['customer_name']); ?></strong></p>
					</header>

					<!-- รายการที่สั่งไปแล้ว -->
					<div class="order-box">
						<h3>รายการอาหารเดิมที่สั่งไว้แล้ว</h3>
						<div class="table-wrapper">
							<table class="alt">
								<thead>
									<tr style="background-color: #f0f0f0;">
										<th>ลำดับ</th>
										<th>เมนูอาหาร</th>
										<th style="text-align: right;">ราคา/หน่วย</th>
										<th style="text-align: center;">จำนวน</th>
										<th style="text-align: right;">รวมเงิน (฿)</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$no = 1;
									while ($item = mysqli_fetch_assoc($existing_items)) {
									?>
									<tr>
										<td><?php echo $no++; ?></td>
										<td><?php echo htmlspecialchars($item['product_name']); ?></td>
										<td style="text-align: right;"><?php echo number_format($item['unit_price'], 2); ?></td>
										<td style="text-align: center;"><?php echo $item['quantity']; ?></td>
										<td style="text-align: right; font-weight: bold;"><?php echo number_format($item['subtotal'], 2); ?></td>
									</tr>
									<?php } ?>
								</tbody>
								<tfoot>
									<tr style="background-color: #f9f9f9; font-weight: bold;">
										<td colspan="4" style="text-align: right;">ยอดรวมปัจจุบัน:</td>
										<td style="text-align: right; color: #1565c0; font-size: 1.1em;"><?php echo number_format($sale['total_amount'], 2); ?> ฿</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>

					<!-- ฟอร์มสั่งเพิ่ม -->
					<form method="post" action="save_add_items.php">
						<input type="hidden" name="sale_id" value="<?php echo htmlspecialchars($sale['sale_id']); ?>" />
						
						<div class="order-box">
							<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
								<h3 style="margin: 0; color: #2e7d32;">+ เลือกรายการอาหารที่ต้องการสั่งเพิ่ม</h3>
								<button type="button" class="button small special" onclick="addRow();">+ เพิ่มแถวอาหาร</button>
							</div>

							<div id="itemsContainer">
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

							<div style="text-align: right; margin-top: 15px; font-size: 1.2em;">
								<strong>ยอดสั่งเพิ่มครั้งนี้: <span id="displayTotal" style="color: #2e7d32; font-weight: bold;">0.00</span> บาท</strong>
							</div>
						</div>

						<ul class="actions">
							<li><input type="submit" value="ยืนยันสั่งอาหารเพิ่ม" class="special big" /></li>
							<li><a href="sale_list.php" class="button big">ยกเลิก / กลับ</a></li>
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
