<?php
// รองรับทั้ง localhost (XAMPP) และ Cloud Database (เมื่อขึ้น Vercel)
$dbhost = getenv('DB_HOST') ? getenv('DB_HOST') : "localhost";
$dbuser = getenv('DB_USER') ? getenv('DB_USER') : "root";
$dbpass = (getenv('DB_PASS') !== false && getenv('DB_PASS') !== null) ? getenv('DB_PASS') : "";
$dbname = getenv('DB_NAME') ? getenv('DB_NAME') : "burapa_spaghetti";
$dbport = getenv('DB_PORT') ? intval(getenv('DB_PORT')) : 3306;

// เชื่อมต่อฐานข้อมูล
$conn = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);

if (!$conn) {
    // หากรันอยู่บน Vercel แล้วยังไม่ได้ตั้งค่า Cloud DB
    if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
        echo "<!DOCTYPE HTML><html><head><meta charset='utf-8'><title>การเชื่อมต่อฐานข้อมูล - Vercel</title></head><body style='font-family: sans-serif; background: #f9f9f9; padding: 40px;'>";
        echo "<div style='max-width: 680px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 4px 10px rgba(0,0,0,0.05);'>";
        echo "<h2 style='color: #e53935; margin-top: 0;'>⚠️ เชื่อมต่อ Cloud Database ยังไม่สมบูรณ์</h2>";
        echo "<p>ระบบรันอยู่บน <strong>Vercel (Cloud)</strong> ซึ่งไม่สามารถเข้าถึงฐานข้อมูล <code>localhost</code> ในคอมพิวเตอร์ของคุณได้โดยตรง</p>";
        echo "<hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;' />";
        echo "<h4>วิธีตั้งค่าให้ฐานข้อมูลทำงานบน Vercel:</h4>";
        echo "<ol style='line-height: 1.8;'>";
        echo "<li>สมัครบริการ MySQL ฟรีบนคลาวด์ เช่น <a href='https://tidbcloud.com/' target='_blank'><strong>TiDB Cloud</strong></a> หรือ <a href='https://aiven.io/' target='_blank'><strong>Aiven for MySQL</strong></a></li>";
        echo "<li>นำไฟล์ <code>burapa_spaghetti.sql</code> ไป Import เข้าฐานข้อมูลบนคลาวด์</li>";
        echo "<li>ไปที่แดชบอร์ดของ Vercel &gt; Settings &gt; <strong>Environment Variables</strong> แล้วเพิ่มตัวแปร:";
        echo "<ul>";
        echo "<li><code>DB_HOST</code> = Host ของคลาวด์</li>";
        echo "<li><code>DB_USER</code> = Username</li>";
        echo "<li><code>DB_PASS</code> = Password</li>";
        echo "<li><code>DB_NAME</code> = burapa_spaghetti</li>";
        echo "<li><code>DB_PORT</code> = พอร์ตเชื่อมต่อ (เช่น 3306 หรือ 4000)</li>";
        echo "</ul></li>";
        echo "</ol>";
        echo "<p style='color: #888; font-size: 0.85em;'>Error detail: " . htmlspecialchars(mysqli_connect_error()) . "</p>";
        echo "</div></body></html>";
        exit;
    }
    die("เชื่อมต่อฐานข้อมูลล้มเหลว : " . mysqli_connect_error());
}

// ตั้งค่าชุดอักขระเป็น utf8mb4 ตามข้อกำหนด
mysqli_set_charset($conn, "utf8mb4");
?>
