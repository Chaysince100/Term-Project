<?php
include('connect_db.php');

$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-01');
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date('Y-m-d');

// a. รายได้จากการขาย (Total Revenue)
$sql_rev = "SELECT IFNULL(SUM(total_amount), 0) AS total_revenue, COUNT(*) AS count_bills 
            FROM sales 
            WHERE payment_status = 'paid' 
              AND DATE(sale_date) BETWEEN '" . mysqli_real_escape_string($conn, $date_start) . "' 
                                      AND '" . mysqli_real_escape_string($conn, $date_end) . "'";
$res_rev = mysqli_query($conn, $sql_rev);
$data_rev = mysqli_fetch_assoc($res_rev);
$revenue = floatval($data_rev['total_revenue']);
$count_bills = intval($data_rev['count_bills']);

// b. ค่าใช้จ่ายจากต้นทุนสินค้า (Cost of Goods Sold - COGS)
$sql_cogs = "SELECT IFNULL(SUM(d.quantity * p.cost), 0) AS total_cogs,
                    SUM(d.quantity) AS total_items_sold
             FROM sale_details d
             INNER JOIN sales s ON d.sale_id = s.sale_id
             INNER JOIN product p ON d.product_id = p.product_id
             WHERE s.payment_status = 'paid'
               AND DATE(s.sale_date) BETWEEN '" . mysqli_real_escape_string($conn, $date_start) . "' 
                                         AND '" . mysqli_real_escape_string($conn, $date_end) . "'";
$res_cogs = mysqli_query($conn, $sql_cogs);
$data_cogs = mysqli_fetch_assoc($res_cogs);
$cogs = floatval($data_cogs['total_cogs']);
$total_items_sold = intval($data_cogs['total_items_sold']);

// c. ค่าใช้จ่ายจากสาธารณูปโภค (Utility Expenses)
$sql_util = "SELECT IFNULL(SUM(amount), 0) AS total_utility 
             FROM utility_expense 
             WHERE expense_date BETWEEN '" . mysqli_real_escape_string($conn, $date_start) . "' 
                                    AND '" . mysqli_real_escape_string($conn, $date_end) . "'";
$res_util = mysqli_query($conn, $sql_util);
$data_util = mysqli_fetch_assoc($res_util);
$utility_cost = floatval($data_util['total_utility']);

// ดึงรายละเอียดค่าใช้จ่ายสาธารณูปโภคแยกประเภท
$sql_util_detail = "SELECT e.expense_type_name, IFNULL(SUM(u.amount), 0) AS type_total 
                    FROM expense_type e 
                    LEFT JOIN utility_expense u ON e.expense_type_id = u.expense_type_id 
                         AND u.expense_date BETWEEN '" . mysqli_real_escape_string($conn, $date_start) . "' 
                                                AND '" . mysqli_real_escape_string($conn, $date_end) . "'
                    GROUP BY e.expense_type_id, e.expense_type_name";
$res_util_detail = mysqli_query($conn, $sql_util_detail);

// การคำนวณผลประกอบการ
$gross_profit = $revenue - $cogs;
$total_expenses = $cogs + $utility_cost;
$net_profit = $revenue - $total_expenses;
$profit_margin = ($revenue > 0) ? ($net_profit / $revenue) * 100 : 0;
?>
<!DOCTYPE HTML>
<html lang="th">
<head>
	<title>รายงานกำไรขาดทุนเบื้องต้น - บูรพาสปาเก็ตตี้</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
	<style>
		.filter-box {
			background: #fafafa;
			padding: 20px;
			border: 1px solid #ddd;
			border-radius: 6px;
			margin-bottom: 25px;
		}
		.pl-card {
			background: #fff;
			border: 1px solid #e0e0e0;
			border-radius: 8px;
			padding: 20px;
			text-align: center;
			box-shadow: 0 2px 6px rgba(0,0,0,0.06);
			margin-bottom: 20px;
		}
		.pl-card h4 { margin-bottom: 8px; font-size: 1.1em; color: #555; }
		.pl-card .amount { font-size: 1.9em; font-weight: bold; }
		.text-revenue { color: #2e7d32; }
		.text-cogs { color: #d84315; }
		.text-utility { color: #c62828; }
		.text-profit { color: #1565c0; }
		.table-statement th, .table-statement td {
			padding: 12px 16px;
			font-size: 1.05em;
		}
		@media print {
			#sidebar, #header, .filter-box, .btn-print, footer {
				display: none !important;
			}
			#main {
				margin: 0 !important;
				padding: 0 !important;
			}
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<header id="header">
					<a href="index.php" class="logo"><strong>รายงานผลประกอบการ</strong> &gt; กำไรขาดทุนเบื้องต้น</a>
				</header>

				<section>
					<header class="main">
						<h2>2. รายงานกำไรขาดทุนเบื้องต้น</h2>
						<p>คำนวณและแจกแจงผลประกอบการ: <strong>a. รายได้จากการขาย, b. ค่าใช้จ่ายต้นทุนสินค้า, c. ค่าใช้จ่ายสาธารณูปโภค</strong></p>
					</header>

					<!-- Date filter -->
					<div class="filter-box">
						<form method="get" action="report_profit_loss.php">
							<div class="row uniform">
								<div class="4u 12u$(xsmall)">
									<label for="date_start">ตั้งแต่วันที่ *</label>
									<input type="date" name="date_start" id="date_start" value="<?php echo htmlspecialchars($date_start); ?>" required />
								</div>
								<div class="4u 12u$(xsmall)">
									<label for="date_end">ถึงวันที่ *</label>
									<input type="date" name="date_end" id="date_end" value="<?php echo htmlspecialchars($date_end); ?>" required />
								</div>
								<div class="4u$ 12u$(xsmall)" style="padding-top: 28px;">
									<input type="submit" value="คำนวณรายงาน" class="special" />
									<button type="button" onclick="window.print()" class="button btn-print">🖨️ พิมพ์รายงาน</button>
								</div>
							</div>
						</form>
					</div>

					<div style="margin-bottom: 20px;">
						<h3>ผลประกอบการระหว่างวันที่ <u><?php echo date('d/m/Y', strtotime($date_start)); ?></u> ถึง <u><?php echo date('d/m/Y', strtotime($date_end)); ?></u></h3>
					</div>

					<!-- Cards Overview -->
					<div class="row">
						<div class="3u 12u$(medium)">
							<div class="pl-card" style="border-top: 4px solid #2e7d32;">
								<h4>a. รายได้จากการขาย</h4>
								<div class="amount text-revenue"><?php echo number_format($revenue, 2); ?> ฿</div>
								<p><small>(จำนวน <?php echo $count_bills; ?> บิล)</small></p>
							</div>
						</div>
						<div class="3u 12u$(medium)">
							<div class="pl-card" style="border-top: 4px solid #d84315;">
								<h4>b. ต้นทุนสินค้าที่ขาย</h4>
								<div class="amount text-cogs"><?php echo number_format($cogs, 2); ?> ฿</div>
								<p><small>(อาหาร <?php echo $total_items_sold; ?> รายการ)</small></p>
							</div>
						</div>
						<div class="3u 12u$(medium)">
							<div class="pl-card" style="border-top: 4px solid #c62828;">
								<h4>c. ค่าสาธารณูปโภค</h4>
								<div class="amount text-utility"><?php echo number_format($utility_cost, 2); ?> ฿</div>
								<p><small>(ค่าน้ำ ไฟ แก๊ส ฯลฯ)</small></p>
							</div>
						</div>
						<div class="3u 12u$(medium)">
							<div class="pl-card" style="border-top: 4px solid #1565c0;">
								<h4>กำไรสุทธิเบื้องต้น</h4>
								<div class="amount <?php echo ($net_profit >= 0) ? 'text-profit' : 'text-utility'; ?>">
									<?php echo number_format($net_profit, 2); ?> ฿
								</div>
								<p><small>อัตรากำไร: <?php echo number_format($profit_margin, 2); ?>%</small></p>
							</div>
						</div>
					</div>

					<!-- Statement Table -->
					<div class="table-wrapper">
						<table class="alt table-statement">
							<thead>
								<tr style="background-color: #f7f7f7;">
									<th>รายการทางการเงิน (Financial Statement)</th>
									<th style="text-align: right; width: 220px;">จำนวนเงิน (บาท)</th>
									<th style="text-align: right; width: 140px;">สัดส่วน (%)</th>
								</tr>
							</thead>
							<tbody>
								<tr style="font-weight: bold; background-color: #f1f8e9;">
									<td>a. รายได้จากการขาย (Total Sales Revenue)</td>
									<td style="text-align: right; color: #2e7d32;"><?php echo number_format($revenue, 2); ?></td>
									<td style="text-align: right;">100.00%</td>
								</tr>
								<tr>
									<td style="padding-left: 30px;">
										<strong>หัก: b. ค่าใช้จ่ายจากต้นทุนสินค้า (Cost of Goods Sold)</strong><br />
										<small style="color: #666;">ต้นทุนวัตถุดิบและอาหารที่ขายได้ตามสูตร/มาตรฐาน</small>
									</td>
									<td style="text-align: right; color: #d84315;">-<?php echo number_format($cogs, 2); ?></td>
									<td style="text-align: right;"><?php echo ($revenue > 0) ? number_format(($cogs / $revenue) * 100, 2) : '0.00'; ?>%</td>
								</tr>
								<tr style="font-weight: bold; background-color: #fafafa;">
									<td>= กำไรขั้นต้น (Gross Profit)</td>
									<td style="text-align: right; color: #1565c0;"><?php echo number_format($gross_profit, 2); ?></td>
									<td style="text-align: right;"><?php echo ($revenue > 0) ? number_format(($gross_profit / $revenue) * 100, 2) : '0.00'; ?>%</td>
								</tr>
								<tr>
									<td style="padding-left: 30px;">
										<strong>หัก: c. ค่าใช้จ่ายจากสาธารณูปโภค (Utility Expenses)</strong>
										<div style="font-size: 0.9em; color: #777; margin-top: 5px;">
											<?php
											while ($u_row = mysqli_fetch_assoc($res_util_detail)) {
												echo "• " . htmlspecialchars($u_row['expense_type_name']) . ": " . number_format($u_row['type_total'], 2) . " ฿ &nbsp;&nbsp;";
											}
											?>
										</div>
									</td>
									<td style="text-align: right; color: #c62828;">-<?php echo number_format($utility_cost, 2); ?></td>
									<td style="text-align: right;"><?php echo ($revenue > 0) ? number_format(($utility_cost / $revenue) * 100, 2) : '0.00'; ?>%</td>
								</tr>
							</tbody>
							<tfoot>
								<tr style="background-color: <?php echo ($net_profit >= 0) ? '#e8f5e9' : '#ffebee'; ?>; font-weight: bold; font-size: 1.2em;">
									<td>สรุป: กำไร (ขาดทุน) สุทธิเบื้องต้น [a - b - c]</td>
									<td style="text-align: right; color: <?php echo ($net_profit >= 0) ? '#2e7d32' : '#c62828'; ?>;">
										<?php echo number_format($net_profit, 2); ?> ฿
									</td>
									<td style="text-align: right; color: <?php echo ($net_profit >= 0) ? '#2e7d32' : '#c62828'; ?>;">
										<?php echo number_format($profit_margin, 2); ?>%
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</section>

				<footer id="footer">
					<p class="copyright">ร้านบูรพาสปาเก็ตตี้ &copy; Term Project</p>
				</footer>
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
