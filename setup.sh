#!/bin/bash

# Aeris Database Setup Script
# This script helps you set up the Aeris database quickly

echo "🚀 Aeris Database Setup"
echo "======================="
echo

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL is not installed or not in PATH"
    echo "Please install MySQL/MariaDB first"
    exit 1
fi

echo "✅ MySQL found"

# Get database credentials
read -p "Enter MySQL username (default: root): " DB_USER
DB_USER=${DB_USER:-root}

read -s -p "Enter MySQL password: " DB_PASS
echo

read -p "Enter database name (default: aeris): " DB_NAME
DB_NAME=${DB_NAME:-aeris}

read -p "Enter MySQL host (default: localhost): " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Enter MySQL port (default: 3306): " DB_PORT
DB_PORT=${DB_PORT:-3306}

echo
echo "🔄 Setting up database..."

# Create database
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"

if [ $? -eq 0 ]; then
    echo "✅ Database '$DB_NAME' created successfully"
else
    echo "❌ Failed to create database"
    exit 1
fi

# Import schema
echo "📋 Importing database schema..."
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/schema.sql

if [ $? -eq 0 ]; then
    echo "✅ Schema imported successfully"
else
    echo "❌ Failed to import schema"
    exit 1
fi

# Ask if user wants sample data
echo
read -p "Do you want to import sample data? (y/N): " IMPORT_SAMPLE
if [[ $IMPORT_SAMPLE =~ ^[Yy]$ ]]; then
    echo "📊 Importing sample data..."
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < examples/sample-data.sql
    
    if [ $? -eq 0 ]; then
        echo "✅ Sample data imported successfully"
        echo "📝 Default login credentials:"
        echo "   Username: admin"
        echo "   Password: password"
        echo "   (Please change this password after first login!)"
    else
        echo "❌ Failed to import sample data"
    fi
fi

# Create config file
echo
echo "⚙️  Creating configuration file..."

cat > config/database.php << EOF
<?php
/**
 * Database Configuration
 * Generated automatically by setup script
 */

\$host = '$DB_HOST';
\$port = '$DB_PORT';
\$dbname = '$DB_NAME';
\$username = '$DB_USER';
\$password = '$DB_PASS';

try {
    \$pdo = new PDO("mysql:host=\$host;port=\$port;dbname=\$dbname;charset=utf8mb4", \$username, \$password);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException \$e) {
    die("Database connection failed: " . \$e->getMessage());
}
?>
EOF

echo "✅ Configuration file created"

echo
echo "🎉 Setup completed successfully!"
echo
echo "Next steps:"
echo "1. Configure your web server to serve files from this directory"
echo "2. Make sure PHP is installed with PDO MySQL extension"
echo "3. Copy .htaccess.example to .htaccess if using Apache"
echo "4. Access the application in your web browser"
echo
echo "📚 Check docs/INSTALLATION.md for detailed setup instructions"
