-- ===============================================================
-- ฐานข้อมูลสำหรับโปรเจกต์: ร้านบูรพาสปาเก็ตตี้ (Black Sheep in the Garden)
-- รหัสภาษา: UTF-8 Unicode (utf8mb4_general_ci)
-- ===============================================================

CREATE DATABASE IF NOT EXISTS `burapa_spaghetti` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `burapa_spaghetti`;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------
-- 1. ตารางกลุ่ม Setup: หมวดหมู่สินค้า (category)
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `category_id` varchar(10) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `category` (`category_id`, `category_name`, `description`) VALUES
('CAT01', 'เมนูแนะนำ (Recommended)', 'เมนูไฮไลท์และเมนูขายดีประจำร้าน'),
('CAT02', 'อาหารทานเล่น (Appetizers)', 'ของทานเล่น เฟรนช์ฟราย ขนมปัง ผักโขมอบชีส'),
('CAT03', 'สเต็ก (Steak Menu)', 'สเต็กหมู ไก่ ปลา เนื้อ นุ่มชุ่มฉ่ำ'),
('CAT04', 'ลาซานญ่า (Lasagna)', 'ลาซานญ่าอบชีสอบร้อนโฮมเมด'),
('CAT05', 'ซุป (Soup)', 'ซุปประจำวัน ซุปครีมข้นหอมกรุ่น'),
('CAT06', 'เบอร์เกอร์ (Burger)', 'เบอร์เกอร์ชิ้นโตพร้อมชีสเยิ้มๆ'),
('CAT07', 'อาหารจานเดียว (Rice Dishes)', 'ข้าวหน้าสไตล์ญี่ปุ่นและฟิวชั่น'),
('CAT08', 'พาสต้าและสปาเก็ตตี้ (Pasta & Spaghetti)', 'สปาเก็ตตี้ซิกเนเจอร์สูตรเฉพาะของร้าน'),
('CAT09', 'สลัด (Salad)', 'สลัดผักสดกรอบเพื่อสุขภาพ พร้อมน้ำสลัดเข้มข้น'),
('CAT10', 'เครื่องดื่ม (Beverages)', 'เครื่องดื่มเย็น ชา กาแฟ น้ำผลไม้ และน้ำอัดลม');

-- ---------------------------------------------------------------
-- 2. ตารางกลุ่ม Setup: ประเภทการชำระเงิน (paytype)
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `paytype`;
CREATE TABLE `paytype` (
  `paytype_id` varchar(10) NOT NULL,
  `paytype_name` varchar(50) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`paytype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `paytype` (`paytype_id`, `paytype_name`, `description`) VALUES
('CASH', 'เงินสด (Cash)', 'ชำระด้วยเงินสดหน้าร้าน'),
('TRANSFER', 'โอนเงิน (Transfer / PromptPay)', 'สแกน QR Code พร้อมเพย์ หรือโอนเข้าบัญชีธนาคาร');

-- ---------------------------------------------------------------
-- 3. ตารางกลุ่ม Setup: โต๊ะหลักของร้าน (restaurant_table)
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `restaurant_table`;
CREATE TABLE `restaurant_table` (
  `table_no` varchar(10) NOT NULL,
  `seat_capacity` int(11) NOT NULL DEFAULT 4,
  `zone` varchar(50) DEFAULT 'ห้องปรับอากาศ',
  `status` varchar(20) NOT NULL DEFAULT 'ว่าง', -- ว่าง, มีลูกค้า, จอง
  PRIMARY KEY (`table_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `restaurant_table` (`table_no`, `seat_capacity`, `zone`, `status`) VALUES
('T01', 2, 'Indoor (ห้องแอร์)', 'ว่าง'),
('T02', 4, 'Indoor (ห้องแอร์)', 'ว่าง'),
('T03', 4, 'Indoor (ห้องแอร์)', 'ว่าง'),
('T04', 6, 'Indoor (ห้องแอร์)', 'ว่าง'),
('T05', 4, 'Indoor (ห้องแอร์)', 'ว่าง'),
('T06', 4, 'Garden (ในสวน)', 'ว่าง'),
('T07', 4, 'Garden (ในสวน)', 'ว่าง'),
('T08', 6, 'Garden (ในสวน)', 'ว่าง'),
('T09', 8, 'VIP Room', 'ว่าง'),
('T10', 2, 'Garden (ในสวน)', 'ว่าง');

-- ---------------------------------------------------------------
-- 4. ตารางกลุ่ม Setup: ประเภทค่าใช้จ่ายสาธารณูปโภค (expense_type)
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `expense_type`;
CREATE TABLE `expense_type` (
  `expense_type_id` varchar(10) NOT NULL,
  `expense_type_name` varchar(100) NOT NULL,
  `unit_charge` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`expense_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `expense_type` (`expense_type_id`, `expense_type_name`, `unit_charge`) VALUES
('EXP01', 'ค่าไฟฟ้า (Electricity)', 'ต่อเดือน/หน่วย'),
('EXP02', 'ค่าน้ำประปา (Water Supply)', 'ต่อเดือน/หน่วย'),
('EXP03', 'ค่าแก๊สหุงต้ม (Cooking Gas)', 'ต่อถัง'),
('EXP04', 'ค่าอินเทอร์เน็ตและโทรศัพท์ (Internet & Phone)', 'รายเดือน'),
('EXP05', 'ค่าบำรุงรักษาสถานที่และขยะ (Maintenance & Cleaning)', 'รายสัปดาห์/เดือน');

-- ---------------------------------------------------------------
-- 5. ตารางสินค้า (product) - มีข้อมูลจากเมนูของร้านครบถ้วน
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `product_id` varchar(10) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `category_id` varchar(10) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00, -- ต้นทุนสินค้าเพื่อใช้คำนวณกำไรขาดทุน
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'พร้อมขาย',
  PRIMARY KEY (`product_id`),
  KEY `fk_prod_cat` (`category_id`),
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `product` (`product_id`, `product_name`, `category_id`, `price`, `cost`, `description`, `status`) VALUES
-- Appetizers
('P001', 'เฟรนช์ฟราย', 'CAT02', 69.00, 25.00, 'มันฝรั่งทอดกรอบ ร้อนๆ เสิร์ฟพร้อมซอสมะเขือเทศและมายองเนส', 'พร้อมขาย'),
('P002', 'ขนมปังกระเทียม', 'CAT02', 69.00, 22.00, 'ขนมปังฝรั่งเศสอบเนยกระเทียมหอมกรุ่น กรอบนอกนุ่มใน', 'พร้อมขาย'),
('P003', 'เห็ดผัดเนย', 'CAT02', 89.00, 32.00, 'เห็ดสดผัดเนยสดแท้ ปรุงรสกลมกล่อม', 'พร้อมขาย'),
('P004', 'ผักโขมอบชีส', 'CAT02', 89.00, 35.00, 'ผักโขมนุ่มละมุน คลุกเคล้าชีสเข้มข้นอบจนเหลืองทอง', 'พร้อมขาย'),
('P005', 'มันบดราดซอสเกรวี่', 'CAT02', 89.00, 30.00, 'มันบดเนื้อเนียนนุ่มราดซอสเกรวี่สูตรพิเศษของร้าน', 'พร้อมขาย'),
('P006', 'มันบดทรัฟเฟิล', 'CAT02', 129.00, 48.00, 'มันบดผสมกลิ่นเห็ดทรัฟเฟิลแท้ หอมหรูหรา', 'พร้อมขาย'),
('P007', 'มันบดอบชีส', 'CAT02', 139.00, 50.00, 'มันบดเนื้อเนียนท็อปปิ้งมอสซาเรลล่าชีส อบเยิ้มๆ', 'พร้อมขาย'),
('P008', 'หอยแมลงภู่อบเนยกระเทียม', 'CAT02', 179.00, 75.00, 'หอยแมลงภู่นิวซีแลนด์ตัวโต อบเนยกระเทียมพาร์สลีย์', 'พร้อมขาย'),
('P009', 'หอยแมลงภู่อบชีส', 'CAT02', 229.00, 95.00, 'หอยแมลงภู่นิวซีแลนด์อบชีสเข้มข้นรสเลิศ', 'พร้อมขาย'),

-- Lasagna & Soup
('P010', 'ลาซานญ่าหมู', 'CAT04', 189.00, 75.00, 'แผ่นแป้งลาซานญ่าสลับชั้นซอสหมูและชีส อบจนหอม', 'พร้อมขาย'),
('P011', 'ลาซานญ่าเนื้อ', 'CAT04', 229.00, 95.00, 'ลาซานญ่าเนื้อบดนำเข้า คลุกเคล้าซอสและชีสอบร้อน', 'พร้อมขาย'),
('P012', 'ซุปประจำวัน', 'CAT05', 89.00, 30.00, 'ซุปครีมเห็ดหรือซุปฟักทองโฮมเมดสลับเปลี่ยนทุกวัน', 'พร้อมขาย'),

-- Steak Menu
('P013', 'สเต็กหมู', 'CAT03', 99.00, 42.00, 'สเต็กหมูหมักนุ่ม เสิร์ฟพร้อมสลัดและเฟรนช์ฟราย', 'พร้อมขาย'),
('P014', 'สเต็กหมูเพิ่มมันบดเกรวี่', 'CAT03', 139.00, 55.00, 'สเต็กหมูหมักนุ่มพร้อมมันบดราดซอสเกรวี่เข้มข้น', 'พร้อมขาย'),
('P015', 'ไส้กรอกหมูพรีเมียม', 'CAT03', 159.00, 65.00, 'ไส้กรอกหมูสดรมควันสไตล์เยอรมันแท้', 'พร้อมขาย'),
('P016', 'สเต็กหมูพิเศษ (หมู 2 ชิ้น)', 'CAT03', 169.00, 70.00, 'สเต็กหมูจุใจ 2 ชิ้นโต เสิร์ฟคู่เครื่องเคียงครบเซ็ต', 'พร้อมขาย'),
('P017', 'พอร์คช็อป', 'CAT03', 189.00, 78.00, 'สเต็กพอร์คช็อปติดกระดูกย่างหอม สเต็กพรีเมียม', 'พร้อมขาย'),
('P018', 'สเต็กไก่', 'CAT03', 99.00, 38.00, 'สเต็กสะโพกไก่หมักเครื่องเทศ ย่างหนังกรอบเนื้อฉ่ำ', 'พร้อมขาย'),
('P019', 'สเต็กไก่เพิ่มมันบดเกรวี่', 'CAT03', 139.00, 52.00, 'สเต็กไก่ย่างร้อนๆ เพิ่มมันบดเกรวี่เนียนนุ่ม', 'พร้อมขาย'),
('P020', 'สเต็กไก่พิเศษ (ไก่ 2 ชิ้น)', 'CAT03', 169.00, 65.00, 'สเต็กไก่ 2 ชิ้นโต อิ่มคุ้มสไตล์สายสุขภาพ', 'พร้อมขาย'),
('P021', 'สเต็กหมู ไก่คอมโบ (หมู 1 ชิ้น ไก่ 1 ชิ้น)', 'CAT03', 169.00, 68.00, 'ชุดคอมโบสเต็กหมูและไก่ ย่างรสชาติเข้มข้น', 'พร้อมขาย'),
('P022', 'สเต็กปลาดอลลี่', 'CAT03', 149.00, 60.00, 'สเต็กเนื้อปลาดอลลี่ขาวนุ่ม ย่างเนยเสิร์ฟพร้อมทาร์ทาร์ซอส', 'พร้อมขาย'),
('P023', 'ฟิช แอนด์ ชิพ', 'CAT03', 179.00, 70.00, 'ปลาทอดแป้งกรอบสไตล์อังกฤษแท้ พร้อมเฟรนช์ฟราย', 'พร้อมขาย'),
('P024', 'สเต็กปลาแซลมอน', 'CAT03', 369.00, 160.00, 'สเต็กปลาแซลมอนนอร์เวย์ย่างสุกกำลังดี ราดซอสเนยมะนาว', 'พร้อมขาย'),

-- Burger
('P025', 'เบอร์เกอร์ผัก', 'CAT06', 109.00, 40.00, 'เบอร์เกอร์มังสวิรัติ ผักรวมและมันบดย่างเพื่อสุขภาพ', 'พร้อมขาย'),
('P026', 'เบอร์เกอร์หมูชีส', 'CAT06', 159.00, 62.00, 'เบอร์เกอร์หมูบดปรุงรส สอดไส้เชดด้าชีสเยิ้มๆ', 'พร้อมขาย'),
('P027', 'เบอร์เกอร์ไก่ชีส', 'CAT06', 159.00, 60.00, 'เบอร์เกอร์ไก่กรอบชิ้นใหญ่พร้อมชีสและผักสด', 'พร้อมขาย'),
('P028', 'เบอร์เกอร์เนื้อชีส', 'CAT06', 179.00, 75.00, 'เบอร์เกอร์เนื้อวัวพรีเมียม กริลล์หอมชีสและเบคอน', 'พร้อมขาย'),
('P029', 'เบอร์เกอร์ปลา', 'CAT06', 199.00, 80.00, 'เบอร์เกอร์ปลาทอดกรอบ ราดซอสทาร์ทาร์สูตรเด็ด', 'พร้อมขาย'),

-- Rice Dishes
('P030', 'ข้าวหมูย่างเทอริยากิ', 'CAT07', 79.00, 32.00, 'ข้าวหอมมะลิท็อปปิ้งหมูย่างซอสเทอริยากิญี่ปุ่น', 'พร้อมขาย'),
('P031', 'ข้าวไก่ย่างเทอริยากิ', 'CAT07', 79.00, 30.00, 'ข้าวไก่ย่างเนื้อนุ่มราดซอสเทอริยากิกลมกล่อม', 'พร้อมขาย'),
('P032', 'ข้าวหมูย่างสไปซี่', 'CAT07', 79.00, 32.00, 'ข้าวหมูย่างคลุกซอสเผ็ดสไปซี่รสแซ่บ', 'พร้อมขาย'),
('P033', 'ข้าวไก่ย่างสไปซี่', 'CAT07', 79.00, 30.00, 'ข้าวไก่ย่างหมักพริกสไปซี่สไตล์ไทยฟิวชั่น', 'พร้อมขาย'),

-- Pasta & Spaghetti
('P034', 'สปาเก็ตตี้ซอสหมู', 'CAT08', 89.00, 35.00, 'สปาเก็ตตี้ซอสมะเขือเทศหมูสับสไตล์โฮมเมด', 'พร้อมขาย'),
('P035', 'สปาเก็ตตี้ซอสเนื้อ', 'CAT08', 109.00, 48.00, 'สปาเก็ตตี้โบโลเนสเนื้อสับเคี่ยวเครื่องเทศเข้มข้น', 'พร้อมขาย'),
('P036', 'สปาเก็ตตี้พริกแห้งเบคอน', 'CAT08', 129.00, 50.00, 'สปาเก็ตตี้ผัดพริกแห้งกระเทียมเบคอนกรอบ เผ็ดร้อนหอมมัน', 'พร้อมขาย'),
('P037', 'สปาเก็ตตี้คาโบนาร่า', 'CAT08', 139.00, 55.00, 'สปาเก็ตตี้คลุกซอสครีมชีสเข้มข้น ไข่แดง และเบคอนกรอบ', 'พร้อมขาย'),
('P038', 'สปาเก็ตตี้เส้นดำขี้เมาทะเล', 'CAT08', 149.00, 62.00, 'เส้นสปาเก็ตตี้หมึกดำผัดขี้เมาซีฟู้ด กุ้ง หอย ปลาหมึก', 'พร้อมขาย'),
('P039', 'มักกะโรนีผัดซอสหมู', 'CAT08', 89.00, 35.00, 'เส้นมักกะโรนีเหนียวนุ่มผัดซอสมะเขือเทศหมูสับและไข่', 'พร้อมขาย'),
('P040', 'มักกะโรนีผัดซอสเนื้อ', 'CAT08', 109.00, 48.00, 'มักกะโรนีผัดซอสเนื้อเข้มข้นรสชาติกลมกล่อม', 'พร้อมขาย'),
('P041', 'มักกะโรนีผัดซอสกุ้ง', 'CAT08', 119.00, 52.00, 'มักกะโรนีผัดซอสมะเขือเทศใส่กุ้งสดตัวโต', 'พร้อมขาย'),
('P042', 'มักกะโรนีอบชีส', 'CAT08', 159.00, 65.00, 'มักกะโรนีคลุกเคล้าครีมชีส ท็อปชีสหนาอบจนยืด', 'พร้อมขาย'),

-- Salad
('P043', 'สลัดผัก', 'CAT09', 69.00, 25.00, 'ผักสลัดไฮโดรโปนิกส์สดใหม่พร้อมน้ำสลัดใส/งาญี่ปุ่น', 'พร้อมขาย'),
('P044', 'สลัดไข่', 'CAT09', 79.00, 28.00, 'สลัดผักสดเสิร์ฟพร้อมไข่ต้มยางมะตูม', 'พร้อมขาย'),
('P045', 'สลัดเห็ดสามอย่าง', 'CAT09', 99.00, 38.00, 'สลัดผักพร้อมเห็ดย่างเนย 3 ชนิดเพื่อสุขภาพ', 'พร้อมขาย'),
('P046', 'สลัดหมูย่าง', 'CAT09', 99.00, 40.00, 'สลัดผักเคียงคู่หมูย่างนุ่ม เสิร์ฟคู่น้ำสลัดงาขาว', 'พร้อมขาย'),
('P047', 'สลัดไก่ย่าง', 'CAT09', 99.00, 38.00, 'สลัดอกไก่ย่างสมุนไพร โปรตีนสูงแคลอรีต่ำ', 'พร้อมขาย'),
('P048', 'สลัดทูน่า', 'CAT09', 99.00, 42.00, 'สลัดผักสดท็อปด้วยทูน่าคลุกเคล้าน้ำสลัดสูตรเด็ด', 'พร้อมขาย'),
('P049', 'สลัดปูอัด', 'CAT09', 109.00, 45.00, 'สลัดปูอัดเกรดพรีเมียม เสิร์ฟพร้อมน้ำสลัดซีฟู้ดครีม', 'พร้อมขาย'),
('P050', 'สลัดกุ้ง', 'CAT09', 119.00, 52.00, 'สลัดผักสดพร้อมกุ้งลวกตัวโตสดเด้ง', 'พร้อมขาย'),
('P051', 'สลัดผักและผลไม้', 'CAT09', 129.00, 50.00, 'สลัดผักรวมผลไม้ แอปเปิ้ล กีวี่ มะเขือเทศเชอร์รี่สดชื่น', 'พร้อมขาย'),
('P052', 'สลัดปลาดอลลี่ย่าง', 'CAT09', 149.00, 60.00, 'สลัดปลาดอลลี่ย่างเนยกระเทียม ทานง่ายไร้ก้าง', 'พร้อมขาย'),

-- Beverage
('P053', 'น้ำเปล่าเย็น', 'CAT10', 20.00, 7.00, 'น้ำดื่มสะอาด ตรา Black Sheep', 'พร้อมขาย'),
('P054', 'ชามะนาวเย็น', 'CAT10', 45.00, 15.00, 'ชามะนาวรสชาติเข้มข้น หวานอมเปรี้ยวสดชื่น', 'พร้อมขาย'),
('P055', 'น้ำอัดลม (โค้ก/สไปรท์)', 'CAT10', 30.00, 12.00, 'น้ำอัดลมกระป๋องเสิร์ฟพร้อมน้ำแข็งแก้วเย็นเจี๊ยบ', 'พร้อมขาย');

-- ---------------------------------------------------------------
-- 6. ตารางลูกค้า (customer) - มีข้อมูลตัวอย่างมากกว่า 8 รายการ
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `customer_id` varchar(10) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `member_point` int(11) DEFAULT 0,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customer` (`customer_id`, `customer_name`, `phone`, `email`, `member_point`) VALUES
('CUST001', 'สมชาย ใจดี', '081-234-5678', 'somchai@gmail.com', 120),
('CUST002', 'วิภาดา รักษ์สงบ', '089-876-5432', 'vipada@hotmail.com', 85),
('CUST003', 'อนุชา มุ่งมั่น', '086-555-1234', 'anucha@yahoo.com', 210),
('CUST004', 'กานดา ทรัพย์เจริญ', '082-999-8877', 'kanda@gmail.com', 45),
('CUST005', 'ธนพล เกียรติสกุล', '085-333-4411', 'thanapol@outlook.com', 320),
('CUST006', 'ปิยะมาศ ชื่นใจ', '084-111-2233', 'piyamas@gmail.com', 15),
('CUST007', 'ณัฐวุฒิ สิทธิชัย', '087-444-9988', 'natthawut@gmail.com', 190),
('CUST008', 'สุดารัตน์ พูนผล', '083-777-6655', 'sudarat@gmail.com', 75),
('CUST009', 'เกรียงไกร ชัยมงคล', '081-555-6677', 'kriengkrai@gmail.com', 130),
('CUST010', 'ลูกค้าทั่วไป (Walk-in)', '-', '-', 0);

-- ---------------------------------------------------------------
-- 7. ตารางวัตถุดิบ (raw_material) - สอดคล้องกับอาหารและ rule
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `raw_material`;
CREATE TABLE `raw_material` (
  `material_id` varchar(10) NOT NULL,
  `material_name` varchar(100) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `raw_material` (`material_id`, `material_name`, `unit`, `unit_cost`) VALUES
('MAT01', 'เนื้อหมูสันนอก', 'กิโลกรัม', 160.00),
('MAT02', 'พอร์คช็อปติดกระดูก', 'กิโลกรัม', 220.00),
('MAT03', 'เนื้ออกไก่/สะโพกไก่', 'กิโลกรัม', 95.00),
('MAT04', 'เนื้อวัวสันแหลมบด', 'กิโลกรัม', 280.00),
('MAT05', 'เนื้อปลาดอลลี่ฟิลเลต์', 'กิโลกรัม', 110.00),
('MAT06', 'เนื้อปลาแซลมอนนอร์เวย์', 'กิโลกรัม', 450.00),
('MAT07', 'หอยแมลงภู่นิวซีแลนด์', 'กิโลกรัม', 260.00),
('MAT08', 'เส้นสปาเก็ตตี้', 'กิโลกรัม', 75.00),
('MAT09', 'เส้นมักกะโรนี', 'กิโลกรัม', 75.00),
('MAT10', 'มันฝรั่งสด', 'กิโลกรัม', 45.00),
('MAT11', 'มอสซาเรลล่าชีส', 'กิโลกรัม', 290.00),
('MAT12', 'ผักโขมสด', 'กิโลกรัม', 60.00),
('MAT13', 'ผักสลัดไฮโดรโปนิกส์', 'กิโลกรัม', 80.00),
('MAT14', 'ขนมปังเบอร์เกอร์', 'ชิ้น', 12.00),
('MAT15', 'เบคอนรมควัน', 'กิโลกรัม', 210.00);

-- ---------------------------------------------------------------
-- 8. ตารางสูตรอาหารว่าใช้วัตถุดิบใด (recipe_rule) - เฉพาะวัตถุดิบหลัก
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `recipe_rule`;
CREATE TABLE `recipe_rule` (
  `rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(10) NOT NULL,
  `material_id` varchar(10) NOT NULL,
  `amount_needed` decimal(10,2) NOT NULL,
  `unit` varchar(20) NOT NULL,
  PRIMARY KEY (`rule_id`),
  KEY `fk_rule_prod` (`product_id`),
  KEY `fk_rule_mat` (`material_id`),
  CONSTRAINT `fk_rule_prod` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_rule_mat` FOREIGN KEY (`material_id`) REFERENCES `raw_material` (`material_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `recipe_rule` (`product_id`, `material_id`, `amount_needed`, `unit`) VALUES
('P001', 'MAT10', 0.20, 'กิโลกรัม'), -- เฟรนช์ฟราย ใช้มันฝรั่ง 0.20 kg
('P004', 'MAT12', 0.15, 'กิโลกรัม'), -- ผักโขมอบชีส ใช้ผักโขม
('P004', 'MAT11', 0.05, 'กิโลกรัม'), -- ผักโขมอบชีส ใช้ชีส
('P005', 'MAT10', 0.25, 'กิโลกรัม'), -- มันบด ใช้มันฝรั่ง
('P008', 'MAT07', 0.25, 'กิโลกรัม'), -- หอยแมลงภู่อบเนยกระเทียม
('P013', 'MAT01', 0.18, 'กิโลกรัม'), -- สเต็กหมู ใช้เนื้อหมู 180g
('P016', 'MAT01', 0.36, 'กิโลกรัม'), -- สเต็กหมูพิเศษ ใช้หมู 360g
('P017', 'MAT02', 0.25, 'กิโลกรัม'), -- พอร์คช็อป
('P018', 'MAT03', 0.20, 'กิโลกรัม'), -- สเต็กไก่ 200g
('P022', 'MAT05', 0.20, 'กิโลกรัม'), -- สเต็กปลาดอลลี่ 200g
('P024', 'MAT06', 0.20, 'กิโลกรัม'), -- สเต็กแซลมอน 200g
('P026', 'MAT01', 0.12, 'กิโลกรัม'), -- เบอร์เกอร์หมูชีส หมูบด
('P026', 'MAT14', 1.00, 'ชิ้น'),       -- เบอร์เกอร์หมูชีส ขนมปัง
('P026', 'MAT11', 0.03, 'กิโลกรัม'), -- เบอร์เกอร์หมูชีส ชีส
('P028', 'MAT04', 0.15, 'กิโลกรัม'), -- เบอร์เกอร์เนื้อชีส เนื้อบด
('P034', 'MAT08', 0.10, 'กิโลกรัม'), -- สปาเก็ตตี้ซอสหมู เส้น 100g
('P034', 'MAT01', 0.08, 'กิโลกรัม'), -- สปาเก็ตตี้ซอสหมู หมู 80g
('P035', 'MAT08', 0.10, 'กิโลกรัม'), -- สปาเก็ตตี้ซอสเนื้อ เส้น 100g
('P035', 'MAT04', 0.08, 'กิโลกรัม'), -- สปาเก็ตตี้ซอสเนื้อ เนื้อ 80g
('P036', 'MAT08', 0.10, 'กิโลกรัม'), -- สปาเก็ตตี้พริกแห้งเบคอน เส้น
('P036', 'MAT15', 0.06, 'กิโลกรัม'), -- สปาเก็ตตี้พริกแห้งเบคอน เบคอน
('P037', 'MAT08', 0.10, 'กิโลกรัม'), -- คาโบนาร่า เส้น
('P037', 'MAT15', 0.05, 'กิโลกรัม'), -- คาโบนาร่า เบคอน
('P037', 'MAT11', 0.04, 'กิโลกรัม'), -- คาโบนาร่า ชีส
('P043', 'MAT13', 0.18, 'กิโลกรัม'); -- สลัดผัก ผักสลัด 180g

-- ---------------------------------------------------------------
-- 9. ตารางบันทึกค่าสาธารณูปโภค (utility_expense)
-- เพื่อใช้คำนวณรายงานกำไรขาดทุนเบื้องต้น
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `utility_expense`;
CREATE TABLE `utility_expense` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `expense_type_id` varchar(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`expense_id`),
  KEY `fk_util_type` (`expense_type_id`),
  CONSTRAINT `fk_util_type` FOREIGN KEY (`expense_type_id`) REFERENCES `expense_type` (`expense_type_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `utility_expense` (`expense_date`, `expense_type_id`, `amount`, `remark`) VALUES
('2026-10-01', 'EXP01', 3500.00, 'ค่าไฟฟ้าร้านรอบบิลต้นเดือน'),
('2026-10-01', 'EXP02', 850.00, 'ค่าน้ำประปารอบบิลต้นเดือน'),
('2026-10-05', 'EXP03', 900.00, 'เติมแก๊สหุงต้มครัว 2 ถัง'),
('2026-10-10', 'EXP04', 650.00, 'ค่าบริการเน็ตทรูไฟเบอร์รายเดือน'),
('2026-10-15', 'EXP05', 400.00, 'ค่าเก็บขยะเทศบาลและสารทำความสะอาด'),
('2026-10-20', 'EXP03', 450.00, 'เติมแก๊สหุงต้มครัว 1 ถัง'),
('2026-11-01', 'EXP01', 3400.00, 'ค่าไฟฟ้าร้านรอบบิลเดือนใหม่'),
('2026-11-01', 'EXP02', 800.00, 'ค่าน้ำประปารอบบิลเดือนใหม่');

-- ---------------------------------------------------------------
-- 10. ตารางการขาย (sales)
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `sale_id` varchar(20) NOT NULL,
  `sale_date` datetime NOT NULL,
  `table_no` varchar(10) NOT NULL,
  `customer_count` int(11) NOT NULL DEFAULT 1,
  `customer_id` varchar(10) NOT NULL,
  `paytype_id` varchar(10) DEFAULT 'CASH',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(20) NOT NULL DEFAULT 'paid', -- 'pending' (กำลังทาน/ยังไม่ชำระ), 'paid' (ชำระแล้ว)
  PRIMARY KEY (`sale_id`),
  KEY `fk_sales_table` (`table_no`),
  KEY `fk_sales_cust` (`customer_id`),
  KEY `fk_sales_pay` (`paytype_id`),
  CONSTRAINT `fk_sales_table` FOREIGN KEY (`table_no`) REFERENCES `restaurant_table` (`table_no`) ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_cust` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_pay` FOREIGN KEY (`paytype_id`) REFERENCES `paytype` (`paytype_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------
-- 11. ตารางรายละเอียดการขาย (sale_details)
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `sale_details`;
CREATE TABLE `sale_details` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` varchar(20) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`detail_id`),
  KEY `fk_detail_sale` (`sale_id`),
  KEY `fk_detail_prod` (`product_id`),
  CONSTRAINT `fk_detail_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_prod` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------
-- ข้อมูลตัวอย่างการขาย (Sales & Sale Details) สำหรับทดสอบระบบ Zoom และ รายงาน
-- ---------------------------------------------------------------

-- บิลที่ 1: บิลวันที่ 2026-10-15 คุณสมชาย โต๊ะ T01
INSERT INTO `sales` (`sale_id`, `sale_date`, `table_no`, `customer_count`, `customer_id`, `paytype_id`, `total_amount`, `payment_status`) VALUES
('INV20261015-001', '2026-10-15 12:30:00', 'T01', 2, 'CUST001', 'CASH', 367.00, 'paid');
INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
('INV20261015-001', 'P034', 1, 89.00, 89.00),   -- สปาเก็ตตี้ซอสหมู
('INV20261015-001', 'P037', 1, 139.00, 139.00), -- สปาเก็ตตี้คาโบนาร่า
('INV20261015-001', 'P001', 1, 69.00, 69.00),   -- เฟรนช์ฟราย
('INV20261015-001', 'P054', 1, 45.00, 45.00),   -- ชามะนาวเย็น
('INV20261015-001', 'P055', 1, 30.00, 30.00);   -- น้ำอัดลม

-- บิลที่ 2: บิลวันที่ 2026-10-15 คุณวิภาดา โต๊ะ T02
INSERT INTO `sales` (`sale_id`, `sale_date`, `table_no`, `customer_count`, `customer_id`, `paytype_id`, `total_amount`, `payment_status`) VALUES
('INV20261015-002', '2026-10-15 18:45:00', 'T02', 3, 'CUST002', 'TRANSFER', 587.00, 'paid');
INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
('INV20261015-002', 'P013', 2, 99.00, 198.00),  -- สเต็กหมู 2 ที่
('INV20261015-002', 'P017', 1, 189.00, 189.00), -- พอร์คช็อป
('INV20261015-002', 'P004', 1, 89.00, 89.00),   -- ผักโขมอบชีส
('INV20261015-002', 'P043', 1, 69.00, 69.00),   -- สลัดผัก
('INV20261015-002', 'P053', 2, 20.00, 40.00);   -- น้ำเปล่า 2 ขวด

-- บิลที่ 3: บิลวันที่ 2026-10-18 คุณอนุชา โต๊ะ T06
INSERT INTO `sales` (`sale_id`, `sale_date`, `table_no`, `customer_count`, `customer_id`, `paytype_id`, `total_amount`, `payment_status`) VALUES
('INV20261018-001', '2026-10-18 13:10:00', 'T06', 4, 'CUST003', 'TRANSFER', 816.00, 'paid');
INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
('INV20261018-001', 'P036', 2, 129.00, 258.00), -- สปาเก็ตตี้พริกแห้งเบคอน 2 จาน
('INV20261018-001', 'P010', 1, 189.00, 189.00), -- ลาซานญ่าหมู
('INV20261018-001', 'P008', 1, 179.00, 179.00), -- หอยแมลงภู่อบเนยกระเทียม
('INV20261018-001', 'P002', 1, 69.00, 69.00),   -- ขนมปังกระเทียม
('INV20261018-001', 'P054', 2, 45.00, 90.00),   -- ชามะนาวเย็น 2
('INV20261018-001', 'P055', 1, 30.00, 30.00);   -- น้ำอัดลม

-- บิลที่ 4: บิลวันที่ 2026-10-20 คุณธนพล โต๊ะ T09
INSERT INTO `sales` (`sale_id`, `sale_date`, `table_no`, `customer_count`, `customer_id`, `paytype_id`, `total_amount`, `payment_status`) VALUES
('INV20261020-001', '2026-10-20 19:20:00', 'T09', 5, 'CUST005', 'TRANSFER', 1236.00, 'paid');
INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
('INV20261020-001', 'P024', 1, 369.00, 369.00), -- สเต็กแซลมอน
('INV20261020-001', 'P028', 2, 179.00, 358.00), -- เบอร์เกอร์เนื้อชีส 2 ชิ้น
('INV20261020-001', 'P038', 1, 149.00, 149.00), -- สปาเก็ตตี้เส้นดำขี้เมา
('INV20261020-001', 'P006', 1, 129.00, 129.00), -- มันบดทรัฟเฟิล
('INV20261020-001', 'P051', 1, 129.00, 129.00), -- สลัดผักผลไม้
('INV20261020-001', 'P054', 2, 45.00, 90.00);   -- ชามะนาว

-- บิลที่ 5: บิลวันที่ 2026-10-25 ลูกค้าทั่วไป โต๊ะ T03
INSERT INTO `sales` (`sale_id`, `sale_date`, `table_no`, `customer_count`, `customer_id`, `paytype_id`, `total_amount`, `payment_status`) VALUES
('INV20261025-001', '2026-10-25 12:00:00', 'T03', 2, 'CUST010', 'CASH', 287.00, 'paid');
INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
('INV20261025-001', 'P030', 1, 79.00, 79.00),   -- ข้าวหมูย่างเทอริยากิ
('INV20261025-001', 'P031', 1, 79.00, 79.00),   -- ข้าวไก่ย่างเทอริยากิ
('INV20261025-001', 'P012', 1, 89.00, 89.00),   -- ซุปประจำวัน
('INV20261025-001', 'P053', 2, 20.00, 40.00);   -- น้ำเปล่า

-- บิลที่ 6: บิลวันที่ 2026-10-28 คุณกานดา โต๊ะ T04
INSERT INTO `sales` (`sale_id`, `sale_date`, `table_no`, `customer_count`, `customer_id`, `paytype_id`, `total_amount`, `payment_status`) VALUES
('INV20261028-001', '2026-10-28 17:30:00', 'T04', 2, 'CUST004', 'TRANSFER', 457.00, 'paid');
INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
('INV20261028-001', 'P021', 1, 169.00, 169.00), -- หมูไก่คอมโบ
('INV20261028-001', 'P042', 1, 159.00, 159.00), -- มักกะโรนีอบชีส
('INV20261028-001', 'P005', 1, 89.00, 89.00),   -- มันบดราดเกรวี่
('INV20261028-001', 'P053', 2, 20.00, 40.00);

-- บิลที่ 7: บิลวันที่ 2026-10-31 คุณณัฐวุฒิ โต๊ะ T05
INSERT INTO `sales` (`sale_id`, `sale_date`, `table_no`, `customer_count`, `customer_id`, `paytype_id`, `total_amount`, `payment_status`) VALUES
('INV20261031-001', '2026-10-31 19:15:00', 'T05', 2, 'CUST007', 'CASH', 427.00, 'paid');
INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
('INV20261031-001', 'P034', 2, 89.00, 178.00),  -- สปาเก็ตตี้ซอสหมู 2
('INV20261031-001', 'P014', 1, 139.00, 139.00), -- สเต็กหมูเพิ่มมันบด
('INV20261031-001', 'P044', 1, 79.00, 79.00),   -- สลัดไข่
('INV20261031-001', 'P055', 1, 30.00, 30.00);

-- บิลที่ 8: บิลวันที่ 2026-11-01 คุณสมชาย โต๊ะ T01
INSERT INTO `sales` (`sale_id`, `sale_date`, `table_no`, `customer_count`, `customer_id`, `paytype_id`, `total_amount`, `payment_status`) VALUES
('INV20261101-001', '2026-11-01 12:15:00', 'T01', 2, 'CUST001', 'TRANSFER', 516.00, 'paid');
INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
('INV20261101-001', 'P016', 1, 169.00, 169.00), -- สเต็กหมูพิเศษ
('INV20261101-001', 'P037', 1, 139.00, 139.00), -- สปาเก็ตตี้คาโบนาร่า
('INV20261101-001', 'P007', 1, 139.00, 139.00), -- มันบดอบชีส
('INV20261101-001', 'P051', 1, 129.00, 129.00), -- สลัดผักผลไม้
('INV20261101-001', 'P054', 1, 45.00, 45.00);

SET FOREIGN_KEY_CHECKS = 1;
