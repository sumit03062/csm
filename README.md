
## Overview

This CMS assignment is a full-featured content management system built on the CodeIgniter 3 framework. It allows users to create, manage, and publish posts with category organization. The system includes role-based access control (admin and user roles) and a RESTful API for external integrations.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **PHP**: Version 5.6 or newer (PHP 7.x recommended)
- **MySQL/MariaDB**: Version 5.7 or newer
- **Web Server**: Apache (with mod_rewrite enabled) or equivalent
- **Composer**: For dependency management
- **Git**: For version control (optional)



## Installation Instructions

### Step 1: Download and Extract Files

1. Clone or download the project to your web server root:
   ```bash
   # If using XAMPP (Windows)
   cd C:\xampp\htdocs
   ```
   
   Or place the entire `cms_assignment` folder in your web server's document root.

### Step 2: Install Dependencies

Navigate to the project directory and install PHP dependencies using Composer:

```bash
cd cms_assignment
composer install
```



### Step 4: Start Your Web Server

If using XAMPP:
- Start Apache and MySQL from the XAMPP Control Panel

### Step 5: Access the Application

Open your web browser and navigate to:
```
http://localhost/cms_assignment/
```

## Environment Configuration

### Application Configuration

Edit `application/config/config.php`:

```php
// Base URL - Update this if hosted on a different domain
$config['base_url'] = 'http://localhost/cms_assignment/';

// Index file - Set to empty if using URL rewriting
$config['index_page'] = '';

// Encryption key - Set a random 32-character string for security
$config['encryption_key'] = 'your-random-encryption-key-here';
```

### Database Configuration

Edit `application/config/database.php`:

```php
$db['default'] = array(
    'dsn'       => '',
    'hostname'  => 'localhost',      // MySQL server hostname
    'username'  => 'root',           // MySQL username
    'password'  => '',               // MySQL password (empty for default XAMPP)
    'database'  => 'cms_assignment', // Database name
    'dbdriver'  => 'mysqli',         // Database driver (mysqli recommended)
    'dbprefix'  => '',               // Table prefix (optional)
    'pconnect'  => FALSE,
    'db_debug'  => TRUE,             // Set to FALSE in production
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8mb4',
    'dbcollat'  => 'utf8mb4_unicode_ci',
);
```

### .htaccess Configuration

For URL rewriting, create an `.htaccess` file in the project root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

Ensure `mod_rewrite` is enabled in Apache:
```apache
# In Apache configuration or uncomment in httpd.conf
LoadModule rewrite_module modules/mod_rewrite.so
```

## Database Setup



#### Method : Using phpMyAdmin
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "Import" in the top menu
3. Select and upload the `cms_assignment.sql` file
4. Click "Go" to import the database



### Step 2: Verify Database Creation

Connect to the database and verify tables were created:

```bash
mysql -u root -p cms_assignment -e "SHOW TABLES;"
```

Expected tables:
- `users` - Stores user account information
- `posts` - Stores blog post content
- `categories` - Stores post categories
- `post_categories` - Junction table for post-category relationships

### Database Schema

#### Users Table
- `id` - User identifier (Primary Key)
- `name` - User's full name
- `email` - User's email address (Unique)
- `password` - Password hash
- `role` - User role ('admin' or 'user')
- `created_at` - Account creation timestamp

#### Posts Table
- `id` - Post identifier (Primary Key)
- `user_id` - Author's user ID (Foreign Key)
- `title` - Post title
- `content` - Post content
- `status` - Publication status ('draft' or 'published')
- `created_at` - Post creation timestamp

#### Categories Table
- `id` - Category identifier (Primary Key)
- `name` - Category name
- `slug` - URL-friendly category identifier
- `created_at` - Creation timestamp

## Project Structure

```
cms_assignment/
├── application/                 # CodeIgniter application folder
│   ├── config/                 # Configuration files
│   │   ├── config.php         # Main application config
│   │   ├── database.php       # Database configuration
│   │   └── routes.php         # URL routing rules
│   ├── controllers/           # Request handlers
│   │   ├── Admin.php          # Admin panel controller
│   │   ├── Auth.php           # Authentication controller
│   │   ├── Posts.php          # Posts management controller
│   │   └── User.php           # User management controller
│   ├── models/                # Data models
│   │   ├── User_model.php     # User data operations
│   │   ├── Post_model.php     # Post data operations
│   │   ├── Category_model.php # Category data operations
│   │   └── Api_model.php      # API data operations
│   ├── views/                 # HTML templates
│   │   ├── admin/             # Admin panel views
│   │   ├── auth/              # Authentication views
│   │   ├── posts/             # Post management views
│   │   └── components/        # Reusable view components
│   ├── cache/                 # Cache directory
│   └── logs/                  # Application logs
├── system/                     # CodeIgniter core files
├── assets/                     # Static files
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript files
├── cms_assignment.sql         # Database schema
├── composer.json              # PHP dependencies
└── index.php                  # Application entry point
```

## Features

### User Management
- User registration and authentication
- Role-based access control (Admin/User)
- User profile management
- Secure password hashing

### Post Management
- Create, read, update, and delete posts
- Draft and publish workflow
- Post categorization
- Timestamps for post creation
- User attribution

### Category Management
- Create and manage post categories
- Assign multiple categories to posts
- Category-based post filtering

### API Integration
- RESTful API endpoints for external integrations
- JSON responses for API calls
- API authentication

## Usage

### Logging In

1. Navigate to the login page
2. Enter your email and password
3. Click "Sign In"

### Creating a Post (User)

1. Log in to your account
2. Navigate to "My Posts" or "Create Post"
3. Fill in the post details:
   - **Title**: Post title
   - **Content**: Post body text
   - **Categories**: Select one or more categories
   - **Status**: Save as draft or publish
4. Click "Save Post"

### Admin Panel

Admins have access to:
- All user posts
- User management
- Category management
- Site analytics
- System settings

## Troubleshooting

### Common Issues and Solutions

#### Issue: "Database connection error"
**Solution:**
- Verify MySQL is running
- Check database credentials in `config/database.php`
- Ensure the database `cms_assignment` exists
- Check MySQL username and password

#### Issue: "404 Not Found" on all pages
**Solution:**
- Enable mod_rewrite in Apache: `a2enmod rewrite`
- Check `.htaccess` file exists in project root
- Verify `$config['index_page']` is empty in `config/config.php`
- Restart Apache



#### Issue: "Headers already sent" error
**Solution:**
- Check for whitespace or BOM at the end of PHP files
- Ensure no output before controller execution
- Check view files for accidental spaces/newlines

#### Issue: Sessions not persisting
**Solution:**
- Verify `application/cache` folder is writable
- Check `config.php` session settings
- Clear browser cookies and cache
- Restart the application

### Enabling Debug Mode

For development, enable debug mode in `config/config.php`:

```php
define('ENVIRONMENT', 'development');
```

Monitor errors in `application/logs/` folder.

### Performance Optimization

For production:

1. Disable debug mode:
   ```php
   define('ENVIRONMENT', 'production');
   ```

2. Enable query caching:
   ```php
   $config['cache_on'] = TRUE;
   ```

3. Set appropriate encryption:
   ```php
   $config['encryption_key'] = 'production-secure-key';
   ```

## Support and Documentation

- [CodeIgniter Documentation](https://codeigniter.com/user_guide/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [PHP Documentation](https://www.php.net/docs.php)

## License

This project is licensed under the MIT License. See `license.txt` for details.

---

**Last Updated**: March 2026  
**Version**: 1.0  
**Status**: Production Ready
