### วิธีติดตั้ง
1. clone project
```git clone https://github.com/nirusduddinmateh/restful-api.git```
2. เมื่อ clone เสร็จแล้วให้เข้าไป folder project คลิกขวา เลือก Git Bash Here
3. รันคำสั่ง ```composer update```
4. ให้สร้างฐานข้อมูลชื่อ restful_db ผ่าน phpMyAdmin หรือ อื่นๆ ที่สามารถสร้างฐานข้อมูลได้
5. รันคำสั่ง ```copy .env.example .env``` ***Windows
6. รันคำสั่ง ```php artisan migrate```
7. รันคำสั่ง ```php artisan db:seed```
8. รันคำสั่ง ```php artisan serve```
