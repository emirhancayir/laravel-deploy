# FLAVOR - Multi-Vendor E-Commerce Platform

![Laravel](https://img.shields.io/badge/Laravel-11-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/License-Envato-green)

A powerful, feature-rich multi-vendor e-commerce platform built with Laravel 11.

## Features

- **Multi-Vendor Support** - Multiple sellers can register and manage their stores
- **User Management** - Buyers, Sellers, Admins with different permissions
- **Product Management** - Categories, attributes, images, stock tracking
- **Shopping Cart** - Add to cart, checkout, order management
- **Payment Integration** - Iyzico payment gateway support
- **Messaging System** - Real-time chat between buyers and sellers
- **Admin Panel** - Full control over users, products, orders
- **Responsive Design** - Works on all devices
- **Dark/Light Theme** - User preference supported

## Requirements

- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- Composer 2.x
- Apache/Nginx

## Installation

1. Clone or upload files to your server
2. Create a MySQL database
3. Copy `.env.example` to `.env` and configure
4. Run `composer install`
5. Run `php artisan key:generate`
6. Run `php artisan migrate --seed`
7. Run `php artisan storage:link`

## Default Login

**Admin:**
- Email: admin@flavor.com
- Password: password123

**Demo Seller:**
- Email: seller@demo.com
- Password: 123456

**Demo Buyer:**
- Email: buyer@demo.com
- Password: 123456

## Documentation

See `DOCUMENTATION.txt` for detailed installation and configuration guide.

## Support

For support, please contact through CodeCanyon item comments.

## License

This item is sold exclusively on Envato Market (CodeCanyon).
---------------------------------------------------------------------------------------------------------------
<!-->

<-->
================================================================================
                              FLAVOR - E-Commerce Platform
                    Multi-Vendor Marketplace with Modern Design
================================================================================

================================================================================
                              PRODUCT DESCRIPTION
================================================================================

FLAVOR is a powerful, feature-rich multi-vendor e-commerce platform built with
Laravel 11. It provides everything you need to launch your own online marketplace
where multiple sellers can register, list products, and manage their stores.

FEATURES:
---------

[USER MANAGEMENT]
• User registration and login with email verification
• Two-factor authentication (2FA) support
• User roles: Buyer, Seller, Admin, Super Admin
• Profile management with avatar upload
• Address management with Turkey province/district/neighborhood selection

[SELLER FEATURES]
• Seller registration with document verification
• Product listing with multiple images
• Inventory management with stock tracking
• Order management and shipping
• Sales analytics and reports
• Real-time notifications

[PRODUCT MANAGEMENT]
• Category-based product organization
• Dynamic category attributes
• Multiple product images with gallery
• Price and discount management
• Stock management with low stock alerts
• Product approval workflow

[SHOPPING EXPERIENCE]
• Modern, responsive design (Amazon-inspired)
• Advanced product search and filtering
• Category navigation
• Wishlist/Favorites (AJAX-based, no page reload)
• Shopping cart
• Secure checkout

[MESSAGING SYSTEM]
• Real-time chat between buyers and sellers
• Offer/negotiation system
• Message notifications

[PAYMENT INTEGRATION]
• Iyzico payment gateway integration
• Secure payment processing
• Order tracking

[ADMIN PANEL]
• Comprehensive dashboard with statistics
• User management (ban/unban, role assignment)
• Product approval/rejection with notifications
• Category management with commission rates
• Review/comment moderation
• Site settings management
• Slider/banner management
• Activity logs
• IP management and security
• Real-time toast notifications

[ADDITIONAL FEATURES]
• Dark/Light theme toggle
• Fully responsive design
• SEO-friendly URLs
• Maintenance mode
• Multi-language ready structure
• Clean, well-documented code

TECHNICAL SPECIFICATIONS:
-------------------------
• Framework: Laravel 11
• PHP Version: 8.2+
• Database: MySQL 5.7+ / MariaDB 10.3+
• Frontend: Bootstrap 5, Vanilla JavaScript
• Icons: Font Awesome 6
• Charts: Chart.js

================================================================================
                            INSTALLATION GUIDE
================================================================================

REQUIREMENTS:
-------------
• PHP 8.2 or higher
• MySQL 5.7+ or MariaDB 10.3+
• Composer 2.x
• Node.js 18+ (optional, for asset compilation)
• Apache/Nginx web server
• mod_rewrite enabled (Apache)

INSTALLATION STEPS:
-------------------

1. UPLOAD FILES
   Upload all files to your web server via FTP or file manager.

2. INSTALL DEPENDENCIES
   Run via SSH:

   composer install

3. CREATE DATABASE
   Create a new MySQL database and user with full privileges.

4. CONFIGURE ENVIRONMENT
   - Rename `.env.example` to `.env`
   - Edit `.env` file with your database credentials:

     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_database_user
     DB_PASSWORD=your_database_password

5. SET PERMISSIONS
   Set the following directories to writable (755 or 775):
   - storage/
   - bootstrap/cache/
   - public/uploads/

6. GENERATE APP KEY
   Run via SSH or use the web installer:

   php artisan key:generate

7. RUN MIGRATIONS
   Run via SSH:

   php artisan migrate --seed

   This will create all database tables and seed initial data.

8. CREATE STORAGE LINK
   Run via SSH:

   php artisan storage:link

9. CONFIGURE WEB SERVER
   Point your domain's document root to the `/public` directory.

   For Apache, ensure .htaccess is working.
   For Nginx, use the following configuration:

   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }

10. DEFAULT ADMIN LOGIN
   After installation, login with:

   Email: admin@flavor.com
   Password: password123

   IMPORTANT: Change this password immediately after first login!

OPTIONAL CONFIGURATIONS:
------------------------

[IYZICO PAYMENT GATEWAY]
Add your Iyzico credentials to .env:

IYZICO_API_KEY=your_api_key
IYZICO_SECRET_KEY=your_secret_key
IYZICO_BASE_URL=https://api.iyzipay.com  (use sandbox URL for testing)

[EMAIL CONFIGURATION]
Configure SMTP settings in .env:

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com

[RECAPTCHA]
Add Google reCAPTCHA keys to .env:

RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key

TROUBLESHOOTING:
----------------

1. "500 Internal Server Error"
   - Check storage/ and bootstrap/cache/ permissions
   - Check PHP error logs
   - Ensure .htaccess is working

2. "Class not found" errors
   - Run: composer dump-autoload
   - Run: php artisan config:clear

3. Images not loading
   - Run: php artisan storage:link
   - Check public/storage symlink exists

4. Styles not loading
   - Clear browser cache
   - Check public/css/style.css exists

FOLDER STRUCTURE:
-----------------
/
├── app/                    # Application logic
│   ├── Http/Controllers/   # Controllers
│   ├── Models/             # Eloquent models
│   └── Notifications/      # Notification classes
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── public/                 # Publicly accessible files
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── uploads/            # User uploads
├── resources/
│   └── views/              # Blade templates
├── routes/                 # Route definitions
└── storage/                # Logs, cache, uploads

================================================================================
                               SUPPORT
================================================================================

For support, please contact through CodeCanyon item comments or support tab.

Before contacting support, please:
1. Read this documentation thoroughly
2. Check the FAQ section on the item page
3. Provide detailed information about your issue including:
   - PHP version
   - Server type (Apache/Nginx)
   - Error messages (if any)
   - Steps to reproduce the issue

================================================================================
                              CHANGELOG
================================================================================

Version 1.0.0 (Initial Release)
- Complete multi-vendor e-commerce platform
- User authentication with 2FA
- Seller dashboard and product management
- Admin panel with full control
- Iyzico payment integration
- Real-time notifications
- Dark/Light theme
- Responsive design

================================================================================
                              LICENSE
================================================================================

This item is sold exclusively on Envato Market (CodeCanyon).
You may use this item for personal or commercial projects.
You may NOT resell, redistribute, or include this item in other products.

For full license details, see: https://codecanyon.net/licenses/standard

================================================================================
                         Thank you for your purchase!
================================================================================
