@echo off
REM Aeris Database Setup Script for Windows
REM This script helps you set up the Aeris database quickly

echo 🚀 Aeris Database Setup
echo =======================
echo.

REM Check if MySQL is installed
mysql --version >nul 2>&1
if errorlevel 1 (
    echo ❌ MySQL is not installed or not in PATH
    echo Please install MySQL/MariaDB first
    pause
    exit /b 1
)

echo ✅ MySQL found

REM Get database credentials
set /p DB_USER="Enter MySQL username (default: root): "
if "%DB_USER%"=="" set DB_USER=root

set /p DB_PASS="Enter MySQL password: "

set /p DB_NAME="Enter database name (default: aeris): "
if "%DB_NAME%"=="" set DB_NAME=aeris

set /p DB_HOST="Enter MySQL host (default: localhost): "
if "%DB_HOST%"=="" set DB_HOST=localhost

set /p DB_PORT="Enter MySQL port (default: 3306): "
if "%DB_PORT%"=="" set DB_PORT=3306

echo.
echo 🔄 Setting up database...

REM Create database
mysql -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME%;"

if errorlevel 1 (
    echo ❌ Failed to create database
    pause
    exit /b 1
)

echo ✅ Database '%DB_NAME%' created successfully

REM Import schema
echo 📋 Importing database schema...
mysql -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASS% %DB_NAME% < database\schema.sql

if errorlevel 1 (
    echo ❌ Failed to import schema
    pause
    exit /b 1
)

echo ✅ Schema imported successfully

REM Ask if user wants sample data
echo.
set /p IMPORT_SAMPLE="Do you want to import sample data? (y/N): "
if /i "%IMPORT_SAMPLE%"=="y" (
    echo 📊 Importing sample data...
    mysql -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASS% %DB_NAME% < examples\sample-data.sql
    
    if errorlevel 1 (
        echo ❌ Failed to import sample data
    ) else (
        echo ✅ Sample data imported successfully
        echo 📝 Default login credentials:
        echo    Username: admin
        echo    Password: password
        echo    ^(Please change this password after first login!^)
    )
)

REM Create config file
echo.
echo ⚙️  Creating configuration file...

(
echo ^<?php
echo /**
echo  * Database Configuration
echo  * Generated automatically by setup script
echo  */
echo.
echo $host = '%DB_HOST%';
echo $port = '%DB_PORT%';
echo $dbname = '%DB_NAME%';
echo $username = '%DB_USER%';
echo $password = '%DB_PASS%';
echo.
echo try {
echo     $pdo = new PDO^("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password^);
echo     $pdo-^>setAttribute^(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION^);
echo     $pdo-^>setAttribute^(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC^);
echo } catch ^(PDOException $e^) {
echo     die^("Database connection failed: " . $e-^>getMessage^(^)^);
echo }
echo ?^>
) > config\database.php

echo ✅ Configuration file created

echo.
echo 🎉 Setup completed successfully!
echo.
echo Next steps:
echo 1. Configure your web server to serve files from this directory
echo 2. Make sure PHP is installed with PDO MySQL extension
echo 3. Copy .htaccess.example to .htaccess if using Apache
echo 4. Access the application in your web browser
echo.
echo 📚 Check docs\INSTALLATION.md for detailed setup instructions
echo.
pause
