# แบบฝึกหัดที่ 8: โปรแกรมจัดการข้อมูลอุปกรณ์ (CRUD)

รายวิชา **ICT12367 การใช้กรอบงานสำหรับการพัฒนาเว็บแอปพลิเคชันเพื่อความปลอดภัย**

## โครงสร้างโปรเจกต์
- `src/` — Front-end พัฒนาด้วย Vue 3 + Bootstrap 5
  - `src/components/AssetCrud.vue` — แสดง เพิ่ม แก้ไข และลบข้อมูลอุปกรณ์ (`assets`)
- `php_api/` — Back-end พัฒนาด้วย PHP (PDO) + MySQL (`db_shop`)
  - `condb.php` — เชื่อมต่อฐานข้อมูล MySQL ด้วย PDO
  - `show_assets.php` — แสดงรายการอุปกรณ์ (`Read`)
  - `add_asset.php` — เพิ่มข้อมูลอุปกรณ์และอัปโหลดรูปภาพ (`Create`)
  - `update_asset.php` — แก้ไขข้อมูลอุปกรณ์ (`Update`)
  - `delete_asset.php` — ลบข้อมูลอุปกรณ์ (`Delete`)
  - `uploads/` — โฟลเดอร์เก็บรูปภาพอุปกรณ์
- `database/assets.sql` — คำสั่ง SQL สร้างตาราง `assets` และข้อมูลตัวอย่าง 5 รายการ

## วิธีติดตั้งและรันโปรเจกต์
1. นำเข้าไฟล์ `database/assets.sql` ลงใน MySQL / phpMyAdmin
2. รัน PHP API:
   ```bash
   php -S localhost:8004
   ```
3. รัน Vue.js Front-end:
   ```bash
   npm install
   npm run dev
   ```
   เปิดเบราว์เซอร์ไปที่ `http://localhost:5179`
