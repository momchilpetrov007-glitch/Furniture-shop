# 🗄️ DATABASE SETUP INSTRUCTIONS
# Furniture Shop - E-commerce Platform

## 📋 PREREQUISITES

✅ XAMPP installed (Apache + MySQL + PHP 8.2)
✅ Browser (Chrome, Firefox, Edge)
✅ Text editor (VS Code, Notepad++)

---

## 🚀 INSTALLATION STEPS

### STEP 1: Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

---

### STEP 2: Create Database

**OPTION A: Using phpMyAdmin (Recommended)**

1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" on the left sidebar
3. Database name: `furniture_shop`
4. Collation: `utf8mb4_general_ci`
5. Click "Create"

**OPTION B: Using SQL Command**

1. Open phpMyAdmin
2. Click "SQL" tab
3. Paste this command:

```sql
CREATE DATABASE furniture_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

4. Click "Go"

---

### STEP 3: Import Database Structure

1. Select `furniture_shop` database (click on it)
2. Click "Import" tab
3. Click "Choose File"
4. Select the file: `furniture_shop.sql`
5. Click "Go" at the bottom
6. Wait for "Import has been successfully finished"

**Expected Result:**
- ✅ 5 tables created
- ✅ Sample data imported
- ✅ 3 users created
- ✅ 10 furniture items
- ✅ 21 orders
- ✅ 4 custom requests

---

### STEP 4: Configure Project

1. Copy all project files to: `C:\xampp\htdocs\furniture_shop\`

2. Edit `config.php`:

```php
// Database credentials (default XAMPP)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');              // Empty for XAMPP
define('DB_NAME', 'furniture_shop');
```

3. Update email settings (if using email features):

```php
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password'); // Gmail App Password
```

4. Update Stripe keys (if using Stripe payments):

```php
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_KEY');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_KEY');
```

---

### STEP 5: Test Database Connection

1. Open browser: `http://localhost/furniture_shop/`

**If you see the homepage → Success! ✅**

**If you see error → Check:**
- ✅ Apache is running
- ✅ MySQL is running
- ✅ Database name is correct
- ✅ config.php credentials match

---

## 👤 DEFAULT LOGIN CREDENTIALS

### Admin Account:
- **Username:** `momos1607`
- **Password:** `password` (the actual hash is already in DB)
- **Email:** `momchil.petrov007@gmail.com`

### Regular User:
- **Username:** `todor`
- **Password:** `password`
- **Email:** `todor@gmail.com`

**⚠️ IMPORTANT:** Change admin password after first login!

---

## 📊 DATABASE STRUCTURE

### Tables Overview:

**1. users** - User accounts
- Columns: id, username, email, password, is_admin, full_name, phone, address
- Records: 3 users (1 admin, 2 regular)

**2. furniture** - Product catalog
- Columns: id, name, description, category, price, image, stock
- Records: 10 furniture items

**3. orders** - Customer orders
- Columns: id, user_id, total_price, status, delivery_address, phone, notes
- Records: 21 orders

**4. order_items** - Order line items
- Columns: id, order_id, furniture_id, quantity, price
- Records: 23 items

**5. custom_requests** - Custom furniture inquiries
- Columns: id, name, email, phone, furniture_type, dimensions, material, budget, description, status
- Records: 4 requests

---

## 🔧 TROUBLESHOOTING

### Problem: "Access denied for user 'root'@'localhost'"
**Solution:** 
- Check MySQL is running in XAMPP
- Verify DB_USER and DB_PASS in config.php
- Default XAMPP: user='root', password=''

### Problem: "Unknown database 'furniture_shop'"
**Solution:**
- Create database first (Step 2)
- Check database name spelling

### Problem: "Table doesn't exist"
**Solution:**
- Import furniture_shop.sql (Step 3)
- Check import was successful

### Problem: "Cannot modify header information"
**Solution:**
- Check no whitespace before <?php in config.php
- Check no echo/output before redirect()

### Problem: Images not showing
**Solution:**
- Create folder: `C:\xampp\htdocs\furniture_shop\images\`
- Copy all product images to this folder
- Check file permissions

---

## 🗄️ BACKUP DATABASE

**To backup your database:**

1. Open phpMyAdmin
2. Select `furniture_shop` database
3. Click "Export" tab
4. Choose "Quick" export method
5. Format: SQL
6. Click "Go"
7. Save the .sql file

**To restore:**
- Follow Step 3 with your backup file

---

## 📁 REQUIRED FOLDERS

Create these folders in your project root:

```
C:\xampp\htdocs\furniture_shop\
├── images/           (product images)
├── uploads/          (user uploads - optional)
└── logs/             (error logs - optional)
```

---

## ✅ POST-INSTALLATION CHECKLIST

- [ ] Database created
- [ ] Tables imported (5 tables)
- [ ] Sample data loaded
- [ ] config.php configured
- [ ] Apache running
- [ ] MySQL running
- [ ] Can access http://localhost/furniture_shop/
- [ ] Can login as admin
- [ ] Images folder exists
- [ ] Email settings configured (if needed)
- [ ] Stripe keys configured (if needed)

---

## 🔐 SECURITY NOTES

**For Production Deployment:**

1. Change all default passwords
2. Update config.php:
   - Set strong DB password
   - Disable error display
   - Enable error logging
   - Enable HTTPS cookies

3. Update these settings:
```php
// In config.php for production:
error_reporting(0);
ini_set('display_errors', 0);
ini_set('session.cookie_secure', 1); // HTTPS only
```

4. Remove or secure:
   - furniture_shop.sql (don't leave in web root)
   - Any setup/install scripts
   - phpMyAdmin access

---

## 📞 SUPPORT

If you encounter issues:

1. Check XAMPP error logs: `C:\xampp\apache\logs\error.log`
2. Check PHP error logs: `C:\xampp\php\logs\php_error_log`
3. Enable error display in config.php temporarily
4. Check MySQL is running: `http://localhost/phpmyadmin`

---

## 🎉 YOU'RE ALL SET!

Your furniture shop is now ready to use!

Access:
- **Frontend:** http://localhost/furniture_shop/
- **Admin Panel:** http://localhost/furniture_shop/admin_orders.php
- **phpMyAdmin:** http://localhost/phpmyadmin/

Happy selling! 🛋️✨
