<?php
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "burapa_spaghetti";
	
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	if (!$conn) {
		die("เชื่อมต่อฐานข้อมูลล้มเหลว : " . mysqli_connect_error());
	}
	
	// ตั้งค่าชุดอักขระเป็น utf8mb4 ตามข้อกำหนด
	mysqli_set_charset($conn, "utf8mb4");
?>
