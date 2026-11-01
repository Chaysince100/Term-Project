<?php
include('connect_db.php');

$sale_id = isset($_GET['sale_id']) ? trim($_GET['sale_id']) : '';

if ($sale_id == '') {
    echo "<script>alert('ไม่พบเลขที่บิล'); window.location='sale_list.php';</script>";
    exit;
}

$sale_q = mysqli_query($conn, "SELECT s.*, c.customer_name, c.phone, pt.paytype_name 
                               FROM sales s 
                               LEFT JOIN customer c ON s.customer_id = c.customer_id 
                               LEFT JOIN paytype pt ON s.paytype_id = pt.paytype_id 
                               WHERE s.sale_id = '" . mysqli_real_escape_string($conn, $sale_id) . "'");
$sale = mysqli_fetch_assoc($sale_q);

if (!$sale) {
    echo "<script>alert('ไม่พบข้อมูลการขาย'); window.location='sale_list.php';</script>";
    exit;
}

$items = mysqli_query($conn, "SELECT d.*, p.product_name 
                              FROM sale_details d 
                              LEFT JOIN product p ON d.product_id = p.product_id 
                              WHERE d.sale_id = '" . mysqli_real_escape_string($conn, $sale_id) . "'
                              ORDER BY d.detail_id");
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>Zoom ใบเสร็จรับเงิน - บิล <?php echo $sale['sale_id']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<style>
		.receipt-container {
			background: #fff;
			border: 2px dashed #999;
			max-width: 650px;
			margin: 0 auto 30px auto;
			padding: 30px 40px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.08);
			font-family: 'Courier New', Courier, monospace, Tahoma;
			color: #222;
		}
		.receipt-header {
			text-align: center;
			border-bottom: 2px dashed #bbb;
			padding-bottom: 15px;
			margin-bottom: 15px;
		}
		.receipt-header h2 {
			margin: 0 0 5px 0;
			font-size: 1.8em;
			color: #111;
		}
		.receipt-header p {
			margin: 2px 0;
			color: #555;
		}
		.receipt-meta {
			display: flex;
			justify-content: space-between;
			margin-bottom: 15px;
			font-size: 0.95em;
		}
		.receipt-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 15px;
		}
		.receipt-table th {
			border-bottom: 2px dashed #888;
			border-top: 2px dashed #888;
			padding: 8px 4px;
			text-align: left;
			font-size: 0.95em;
		}
		.receipt-table td {
			padding: 8px 4px;
			border-bottom: 1px dotted #ccc;
		}
		.receipt-summary {
			border-top: 2px dashed #888;
			padding-top: 10px;
			text-align: right;
		}
		.receipt-summary .total-line {
			font-size: 1.5em;
			font-weight: bold;
			color: #000;
			margin: 8px 0;
		}
		.receipt-footer {
			text-align: center;
			border-top: 2px dashed #bbb;
			margin-top: 20px;
			padding-top: 15px;
			color: #666;
			font-size: 0.9em;
		}
		@media print {
			#sidebar, #header, .non-printable, footer {
				display: none !important;
			}
			#main {
				margin: 0 !important;
				padding: 0 !important;
			}
			.receipt-container {
				border: none;
				box-shadow: none;
				max-width: 100%;
				padding: 0;
			}
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header" class="non-printable">
					<a href="sale_list.php" class="logo"><strong>ข้อมูลการทำงาน</strong> &gt; <a href="sale_list.php">รายการการขาย</a> &gt; Zoom เป็นใบเสร็จ</a>
				</header>

				<section>
					<div class="non-printable" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
						<div>
							<a href="sale_list.php" class="button"><i class="fa fa-arrow-left"></i> กลับหน้ารายการขาย</a>
							<a href="sale_add.php" class="button special">+ เปิดโต๊ะใหม่</a>
							<?php if ($sale['payment_status'] == 'pending') { ?>
								<a href="sale_checkout.php?sale_id=<?php echo urlencode($sale['sale_id']); ?>" class="button special" style="background-color: #2e7d32;">ชำระเงินบิลนี้</a>
							<?php } ?>
						</div>
						<div>
							<button onclick="window.print()" class="button special big" style="background-color: #f56a6a;">🖨️ พิมพ์ใบเสร็จ (Print)</button>
						</div>
					</div>

					<!-- กระดาษใบเสร็จ -->
					<div class="receipt-container">
						<div class="receipt-header">
							<h2>ร้านบูรพาสปาเก็ตตี้</h2>
							<p><strong>BLACK SHEEP IN THE GARDEN</strong></p>
							<p>Happiness is homemade ~ Taste the difference</p>
							<p>โทร. 081-234-5678 | เลขประจำตัวผู้เสียภาษี: 0105562000000</p>
							<h3 style="margin-top: 10px; margin-bottom: 0;">ใบเสร็จรับเงิน / ใบกำกับภาษีอย่างย่อ</h3>
						</div>

						<div class="receipt-meta">
							<div>
								<strong>เลขที่ใบเสร็จ:</strong> <?php echo htmlspecialchars($sale['sale_id']); ?><br />
								<strong>วันที่-เวลา:</strong> <?php echo $sale['sale_date']; ?><br />
								<strong>ลูกค้า:</strong> <?php echo htmlspecialchars($sale['customer_name'] ? $sale['customer_name'] : 'ลูกค้าทั่วไป'); ?>
							</div>
							<div style="text-align: right;">
								<strong>โต๊ะ:</strong> <span style="font-size: 1.2em; font-weight: bold;"><?php echo htmlspecialchars($sale['table_no']); ?></span><br />
								<strong>จำนวนลูกค้า:</strong> <?php echo $sale['customer_count']; ?> ท่าน<br />
								<strong>การชำระเงิน:</strong> <?php echo htmlspecialchars($sale['paytype_name'] ? $sale['paytype_name'] : 'เงินสด'); ?>
							</div>
						</div>

						<table class="receipt-table">
							<thead>
								<tr>
									<th style="width: 35px;">#</th>
									<th>รายการอาหาร</th>
									<th style="text-align: right; width: 80px;">ราคา</th>
									<th style="text-align: center; width: 60px;">จำนวน</th>
									<th style="text-align: right; width: 90px;">รวมเงิน</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								$total_items = 0;
								while ($item = mysqli_fetch_assoc($items)) {
									$total_items += $item['quantity'];
								?>
								<tr>
									<td><?php echo $no++; ?></td>
									<td><?php echo htmlspecialchars($item['product_name']); ?></td>
									<td style="text-align: right;"><?php echo number_format($item['unit_price'], 2); ?></td>
									<td style="text-align: center;"><?php echo $item['quantity']; ?></td>
									<td style="text-align: right;"><?php echo number_format($item['subtotal'], 2); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>

						<div class="receipt-summary">
							<div>จำนวนรายการทั้งหมด: <strong><?php echo $total_items; ?></strong> ชิ้น</div>
							<?php
							$subtotal_before_vat = $sale['total_amount'] * 100 / 107;
							$vat = $sale['total_amount'] - $subtotal_before_vat;
							?>
							<div style="font-size: 0.9em; color: #555;">(ราคาก่อนภาษี: <?php echo number_format($subtotal_before_vat, 2); ?> ฿ | VAT 7%: <?php echo number_format($vat, 2); ?> ฿)</div>
							<div class="total-line">
								ยอดรวมสุทธิ: <?php echo number_format($sale['total_amount'], 2); ?> บาท
							</div>
							<div style="font-size: 0.95em;">
								สถานะ: <strong><?php echo ($sale['payment_status'] == 'paid') ? 'ชำระเงินเรียบร้อยแล้ว' : 'รอชำระเงิน'; ?></strong>
							</div>
						</div>

						<div class="receipt-footer">
							<p>*** ขอบคุณที่มาอุดหนุน โอกาสหน้าเชิญใหม่ค่ะ ***<br />
							Please keep this receipt for your records.</p>
						</div>
					</div>
				</section>

				<footer id="footer" class="non-printable">
					<p class="copyright">ร้านบูรพาสปาเก็ตตี้ &copy; Term Project</p>
				</footer>
			</div>
		</div>

		<div id="sidebar" class="non-printable">
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
