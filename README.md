# Mağusa Taxi Website

Modern, SEO-optimized taxi website for Mağusa (Famagusta), Cyprus with admin panel.

## Features

### Frontend
- **SEO Optimized**: Structured data, meta tags, and content optimized for "Mağusa Taxi" search
- **Mobile Responsive**: Works perfectly on all devices
- **Image Slider**: Auto-rotating taxi images every 5 seconds
- **Contact Buttons**: WhatsApp and phone buttons in top-right corner
- **Modern Design**: Beautiful gradient design with smooth animations

### Admin Panel
- **Content Management**: Edit blog content, titles, and SEO settings
- **Image Management**: Upload, organize, and manage taxi photos
- **Contact Management**: Update phone numbers and WhatsApp info
- **User Settings**: Change admin password and view system info
- **Secure Login**: Password-protected admin access

## Installation

1. **Database Setup**:
   ```sql
   -- Import database.sql into MySQL
   mysql -u root -p < database.sql
   ```

2. **Configuration**:
   - Edit `config.php` with your database credentials
   - Update `SITE_URL` in config.php

3. **File Permissions**:
   ```bash
   chmod 755 assets/images/
   ```

4. **Admin Access**:
   - URL: `/admin/login.php`
   - Username: `admin`
   - Password: `admin123`

## File Structure

```
magusa-taxi/
├── index.php              # Main homepage
├── config.php            # Database configuration
├── database.sql          # Database schema
├── assets/
│   ├── css/
│   │   ├── style.css     # Frontend styles
│   │   └── admin.css     # Admin panel styles
│   ├── js/
│   │   ├── script.js     # Frontend JavaScript
│   │   └── admin.js      # Admin panel JavaScript
│   └── images/           # Image uploads directory
└── admin/
    ├── login.php         # Admin login
    ├── dashboard.php     # Admin dashboard
    ├── content.php       # Content management
    ├── images.php        # Image management
    ├── contact.php       # Contact management
    ├── settings.php      # Admin settings
    └── logout.php        # Logout functionality
```

## SEO Features

- **Schema.org** structured data for local business
- **Open Graph** and Twitter Card meta tags
- **Optimized meta descriptions** and keywords
- **Mobile-friendly** design
- **Fast loading** with optimized images
- **Turkish language** support

## Security Features

- **Password hashing** with PHP password_hash()
- **SQL injection** protection with prepared statements
- **XSS protection** with input sanitization
- **Session management** with secure cookies
- **File upload validation** with type and size checks

## Customization

### Adding Images
1. Go to Admin Panel → Image Management
2. Upload taxi photos (JPEG, PNG, WebP)
3. Set display order and descriptions
4. Images will auto-rotate on homepage

### Updating Content
1. Admin Panel → Content Management
2. Edit titles, blog content, and SEO settings
3. Changes appear immediately on homepage

### Contact Information
1. Admin Panel → Contact Information
2. Update WhatsApp and phone numbers
3. Test buttons to verify functionality

## Technical Requirements

- **PHP 7.4+** with PDO MySQL extension
- **MySQL 5.7+** or MariaDB 10.2+
- **Web server** (Apache/Nginx) with mod_rewrite
- **SSL certificate** recommended for production

## Support

For technical support or customization requests, contact the development team.

## License

© 2024 Mağusa Taxi. All rights reserved.
