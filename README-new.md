# 🚀 Aeris - Dropshipping Business Management System

![Aeris Logo](asset/aerislogoandtext.png)

A modern, lightweight dropshipping business management system designed for solo entrepreneurs. Built with PHP, MySQL, and TailwindCSS.

## ✨ Features

- **📦 Product Management** - Organize inventory with SKUs, pricing, and supplier information
- **📋 Order Tracking** - Complete order lifecycle management from placement to delivery
- **🚚 Supplier Coordination** - Track supplier orders and delivery schedules
- **📊 Analytics Dashboard** - Business insights with order trends and top products
- **🔒 Secure Authentication** - Username/password login with optional "remember me"
- **🛡️ Password Recovery** - Security question-based password reset system
- **📱 Mobile Responsive** - Works seamlessly on desktop, tablet, and mobile
- **🎨 Modern UI** - Clean, intuitive interface built with TailwindCSS
- **🔍 Advanced Filtering** - Powerful search and filter capabilities
- **👤 User-Specific Data** - Complete data isolation between users

## 🎯 Target Audience

Perfect for:

- Solo dropshipping entrepreneurs
- Small e-commerce businesses
- Anyone needing simple inventory and order management
- Developers looking for a clean PHP application structure

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, TailwindCSS, Vanilla JavaScript
- **Icons**: Feather Icons
- **Charts**: Chart.js (for analytics)

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache/Nginx)
- PDO PHP extension
- mod_rewrite (for Apache)

## 🚀 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/aeris.git
cd aeris
```

### 2. Database Setup

```sql
-- Create database
CREATE DATABASE aeris_db;

-- Import schema
mysql -u your_username -p aeris_db < database/schema.sql
```

### 3. Configuration

```bash
# Copy configuration template
cp config/database.php.example config/database.php

# Edit database credentials
nano config/database.php
```

### 4. Web Server Setup

Point your web server document root to the Aeris directory, or place files in your existing web root.

### 5. Access Application

Open your browser and navigate to your domain. Create your first user account through the signup page.

## 📚 Documentation

- [Installation Guide](docs/INSTALLATION.md) - Detailed setup instructions
- [Project Overview](docs/PROJECT-OVERVIEW.md) - Architecture and design decisions
- [API Documentation](docs/API.md) - For developers extending the system

## 🔧 Configuration

### Database Configuration

Edit `config/database.php`:

```php
$host = 'your_host';
$dbname = 'aeris_db';
$username = 'your_username';
$password = 'your_password';
```

### Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection protection with prepared statements
- CSRF protection on all forms
- Security question-based password recovery
- Session security with regeneration

## 📊 Features Overview

### Product Management

- Add, edit, delete products
- SKU management
- Cost and selling price tracking
- Stock level monitoring
- Supplier information
- Product images via URLs

### Order Management

- Customer order tracking
- Order status workflow
- Automatic stock updates
- Order filtering and search
- Bulk operations
- Order history

### Supplier Management

- Supplier order coordination
- Delivery tracking
- Order references
- Status updates

### Analytics

- Daily/monthly order statistics
- Top-performing products
- Revenue tracking
- Order status breakdown
- Visual charts and graphs

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/yourusername/aeris.git

# Set up local development environment
# Follow installation instructions above

# Make your changes
# Submit a pull request
```

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🐛 Bug Reports

Found a bug? Please open an issue on GitHub with:

- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Your PHP/MySQL versions

## 💡 Feature Requests

Have an idea for a new feature? Open an issue with:

- Clear description of the feature
- Use case explanation
- Any mockups or examples

## 🙏 Acknowledgments

- [TailwindCSS](https://tailwindcss.com/) - For the beautiful UI framework
- [Feather Icons](https://feathericons.com/) - For the clean icon set
- [Unsplash](https://unsplash.com/) - For product placeholder images
- All contributors and users of Aeris

## 📧 Support

- 📖 Documentation: Check the `docs/` directory
- 🐛 Issues: [GitHub Issues](https://github.com/yourusername/aeris/issues)
- 💬 Discussions: [GitHub Discussions](https://github.com/yourusername/aeris/discussions)

## 🔄 Version History

### v1.0.0 (Current)

- ✅ Complete dropshipping management system
- ✅ User authentication with password recovery
- ✅ Product, order, and supplier management
- ✅ Analytics dashboard
- ✅ Advanced filtering system
- ✅ Mobile-responsive design

---

**Built with ❤️ for the dropshipping community**

_Aeris - Streamline Your Dropshipping Business_
