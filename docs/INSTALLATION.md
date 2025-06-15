# 🚀 Aeris Installation Guide

## Quick Start (5 Minutes)

### 1. Download & Extract

- Download the Aeris project files
- Extract to your web server directory (e.g., `htdocs`, `www`, `public_html`)

### 2. Database Setup

```sql
-- Create database
CREATE DATABASE aeris_db;

-- Import schema (from your MySQL client or phpMyAdmin)
-- Import the file: database/schema.sql
```

### 3. Configure Database Connection

Edit `config/database.php`:

```php
$host = 'localhost';        // Your MySQL host
$dbname = 'aeris_db';       // Your database name
$username = 'your_user';    // Your MySQL username
$password = 'your_pass';    // Your MySQL password
```

### 4. Set Permissions (if needed)

```bash
chmod 755 /path/to/aeris
chmod 644 /path/to/aeris/*.php
```

### 5. Access Your Site

- Open your browser
- Navigate to your domain/subdomain where Aeris is installed
- You should see the beautiful landing page!

---

## Detailed Installation

### System Requirements

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.3+)
- **Browser**: Modern browser (Chrome, Firefox, Safari, Edge)

### Step-by-Step Installation

#### 1. Verify System Requirements

Visit `/system-check.php` after uploading files to verify your server meets all requirements.

#### 2. Database Setup Options

**Option A: Manual Setup**

```sql
-- Connect to MySQL
mysql -u root -p

-- Create database
CREATE DATABASE aeris_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional, for security)
CREATE USER 'aeris_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON aeris_db.* TO 'aeris_user'@'localhost';
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;

-- Import schema
mysql -u aeris_user -p aeris_db < database/schema.sql
```

**Option B: Using phpMyAdmin**

1. Open phpMyAdmin
2. Create new database: `aeris_db`
3. Select the database
4. Click "Import" tab
5. Choose file: `database/schema.sql`
6. Click "Go"

**Option C: Using cPanel/Hosting Panel**

1. Find "MySQL Databases" in cPanel
2. Create database: `aeris_db`
3. Create user and assign to database
4. Use "phpMyAdmin" to import `database/schema.sql`

#### 3. Configuration Files

**Database Configuration (`config/database.php`)**

```php
<?php
$host = 'localhost';           // Usually localhost
$dbname = 'aeris_db';          // Your database name
$username = 'aeris_user';      // Your MySQL username
$password = 'your_password';   // Your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

**Web Server Configuration (`.htaccess`)**
The included `.htaccess` file provides:

- Pretty URLs and security headers
- File protection for sensitive files
- Browser caching optimization
- Gzip compression

#### 4. File Permissions

**Linux/Unix Servers:**

```bash
# Set directory permissions
find /path/to/aeris -type d -exec chmod 755 {} \;

# Set file permissions
find /path/to/aeris -type f -exec chmod 644 {} \;

# Make sure PHP files are readable
chmod 644 /path/to/aeris/*.php
chmod 644 /path/to/aeris/config/*.php
chmod 644 /path/to/aeris/includes/*.php
```

**Windows Servers:**

- Ensure IIS_IUSRS has read access to all files
- Ensure the web directory is accessible

#### 5. Testing Installation

**System Check:**

1. Visit `/system-check.php`
2. Verify all requirements are green
3. Fix any issues shown

**Demo Setup:**

1. Visit `/demo-setup.php`
2. Set up sample data for testing
3. Explore all features

**Login Test:**

1. Visit `/login.php`
2. Use credentials: `admin` / `admin123`
3. Change password immediately

---

## Hosting-Specific Instructions

### Shared Hosting (cPanel/Plesk)

**cPanel:**

1. Upload files to `public_html` directory
2. Use "File Manager" or FTP
3. Create database via "MySQL Databases"
4. Import SQL via "phpMyAdmin"
5. Edit config file via "File Manager"

**Plesk:**

1. Upload to `httpdocs` directory
2. Use "File Manager" or FTP
3. Create database via "Databases"
4. Import SQL via "phpMyAdmin"
5. Edit config file as needed

### VPS/Dedicated Server

**Apache Setup:**

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/aeris

    <Directory /var/www/aeris>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/aeris_error.log
    CustomLog ${APACHE_LOG_DIR}/aeris_access.log combined
</VirtualHost>
```

**Nginx Setup:**

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/aeris;
    index landing.php index.php;

    location / {
        try_files $uri $uri/ /landing.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Security
    location ~ /\. {
        deny all;
    }

    location ~ \.(sql|ini)$ {
        deny all;
    }
}
```

### Cloud Hosting (AWS, DigitalOcean, etc.)

**Basic Setup:**

1. Create LAMP/LEMP stack
2. Upload files to web directory
3. Configure database connection
4. Set up SSL certificate (recommended)
5. Configure domain DNS

---

## Security Hardening

### 1. Change Default Credentials

```sql
-- Change admin password (use in application or directly in database)
UPDATE users SET password_hash = '$2y$10$your_new_hashed_password' WHERE username = 'admin';
```

### 2. Database Security

- Use strong, unique passwords
- Create dedicated database user (not root)
- Limit database user privileges
- Use localhost connections when possible

### 3. File Security

- Remove write permissions from PHP files after setup
- Protect config directory
- Use the included `.htaccess` for Apache
- Consider moving config files outside web root

### 4. Server Security

- Keep PHP/MySQL updated
- Enable HTTPS (SSL certificate)
- Use strong passwords
- Regular security updates
- Monitor access logs

---

## Troubleshooting

### Common Issues

**Database Connection Failed:**

- Check credentials in `config/database.php`
- Verify database exists
- Check MySQL service is running
- Verify user permissions

**Blank White Page:**

- Check PHP error logs
- Verify PHP version requirements
- Check file permissions
- Enable PHP error display temporarily

**Assets Not Loading:**

- Check file permissions
- Verify .htaccess is working
- Check browser console for 404 errors
- Verify asset files exist

**Session Issues:**

- Check PHP session configuration
- Verify session directory is writable
- Check for PHP errors

### Log Files to Check

- Apache: `/var/log/apache2/error.log`
- Nginx: `/var/log/nginx/error.log`
- PHP: `/var/log/php_errors.log`
- MySQL: `/var/log/mysql/error.log`

### Debug Mode

Temporarily enable PHP error display:

```php
// Add to top of any PHP file for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

---

## Post-Installation

### 1. First Login

- Use credentials: `admin` / `admin123`
- **Immediately change password**
- Explore the dashboard

### 2. Initial Setup

- Add your first products
- Configure supplier information
- Test order creation
- Review dashboard metrics

### 3. Backup Setup

- Schedule regular database backups
- Backup uploaded files/images
- Test restore procedures

### 4. Monitoring

- Set up uptime monitoring
- Monitor error logs
- Check database performance

---

## Updates & Maintenance

### Regular Tasks

- **Weekly**: Database backup
- **Monthly**: Check error logs
- **Quarterly**: Update PHP/MySQL
- **As Needed**: Security patches

### Backup Commands

```bash
# Database backup
mysqldump -u username -p aeris_db > aeris_backup_$(date +%Y%m%d).sql

# File backup
tar -czf aeris_files_$(date +%Y%m%d).tar.gz /path/to/aeris
```

---

## Support

### Self-Help Resources

- **System Check**: `/system-check.php`
- **Error Logs**: Check server error logs
- **Documentation**: Read `README.md` and `PROJECT-OVERVIEW.md`

### Before Seeking Help

1. Run system check
2. Check error logs
3. Verify file permissions
4. Test database connection
5. Try demo setup

---

**🎉 Congratulations!** You should now have Aeris running successfully. Visit your landing page and start managing your dropship business!

**Default URL Structure:**

- **Landing Page**: `/` or `/landing.php`
- **System Check**: `/system-check.php`
- **Demo Setup**: `/demo-setup.php`
- **Login**: `/login.php`
- **Dashboard**: `/index.php` (after login)
