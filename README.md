# 🎨 PYRAMEDIA - Complete Marketing & Media Website

## 🌟 Overview
PYRAMEDIA is a comprehensive, modern marketing and media agency website built with **PHP 8+ and MySQL**. Features bilingual support (English/Arabic), complete blog system with REST API, and powerful admin capabilities.

---

## 🚀 Tech Stack

### Frontend
- **HTML5** - Semantic markup
- **Tailwind CSS** - Modern utility-first CSS
- **JavaScript ES6+** - Classes, async/await, Fetch API
- **Font Awesome 6.4.0** - Icons
- **Google Fonts** - Poppins (EN), Cairo & Tajawal (AR)

### Backend
- **PHP 8+** - Typed properties, match expressions, strict typing
- **MySQL** - Full-text search, optimized queries
- **PDO** - Prepared statements for security
- **RESTful API** - JSON responses

---

## 📁 Project Structure

```
Clued-pyramedia/
├── index.html & index-ar.html      # Homepage (EN/AR)
├── blog.html & blog-ar.html        # Blog listing pages ⭐ NEW
├── blog-post.html & blog-post-ar   # Single post pages ⭐ NEW
├── setup.php                       # DB setup (delete after use!)
├── N8N_WEBHOOK_INTEGRATION.md      # n8n integration guide ⭐ NEW
├── admin/                          # Admin panel (coming)
├── config/
│   └── database.php                # DB connection
├── php/
│   ├── contact.php                 # Contact form
│   ├── booking.php                 # Booking system
│   ├── newsletter.php              # Newsletter
│   └── blog-api.php                # Blog REST API ⭐
├── js/
│   ├── main.js                     # Main JS
│   ├── language.js                 # i18n system
│   └── blog.js                     # Blog manager ⭐
├── sql/
│   └── blog_schema.sql             # Database schema
└── uploads/blog/                   # Blog images
```

---

## 🔧 Quick Installation

### 1. Database Setup
Database already configured:
```
DB: pyramed1_final
User: pyramed1_final
Password: Engmidoz@2020
```

### 2. Create Tables
Visit: `https://your-domain.com/setup.php`
- Click "Start Database Setup"
- Wait for completion
- **DELETE setup.php!**

### 3. Admin Access
```
URL: /admin/login.php (coming soon)
Username: admin
Password: admin123 (CHANGE THIS!)
```

---

## 🔌 Blog API Endpoints

Base: `php/blog-api.php`

| Endpoint | Parameters | Description |
|----------|------------|-------------|
| `?action=list` | lang, page, per_page, category, tag | Get posts list |
| `?action=single` | lang, slug | Get single post |
| `?action=categories` | lang | Get all categories |
| `?action=tags` | lang | Get all tags |
| `?action=search` | lang, q, page | Search posts |
| `?action=popular` | lang, limit | Popular posts |
| `?action=recent` | lang, limit | Recent posts |
| `?action=related` | lang, post_id, limit | Related posts |
| `?action=increment-view` | post_id | Track views |

**Example:**
```
GET /php/blog-api.php?action=list&lang=en&page=1&per_page=12
```

---

## ✅ Features

### Core
- ✅ Bilingual (EN/AR) with RTL
- ✅ Dark Mode
- ✅ Responsive Design
- ✅ SEO Optimized

### Blog System
- ✅ PHP 8+ REST API
- ✅ Full-text search
- ✅ Pagination
- ✅ Categories & Tags
- ✅ View counter
- ✅ Related posts
- ✅ Blog listing pages (EN/AR)
- ✅ Single post pages (EN/AR)
- ✅ Popular posts sidebar
- ✅ Social media sharing
- ✅ n8n webhook integration

### Admin Dashboard
- ✅ Secure authentication system
- ✅ Role-based access (admin/author)
- ✅ Login attempt limiting
- ✅ Dashboard with statistics
- ✅ Recent posts overview
- ✅ Responsive sidebar navigation
- ✅ **Posts management (Full CRUD)**
- ✅ **TinyMCE rich text editor**
- ✅ **Image upload & optimization**
- ✅ **Categories & tags management**
- ✅ **Bilingual editing (EN/AR)**
- ✅ **SEO fields & meta tags**
- ✅ **Bulk actions (publish/draft/delete)**
- ⏳ Media library browser
- ⏳ User management

### Forms
- ✅ Contact form
- ✅ Booking system with calendar
- ✅ Newsletter subscription
- ✅ Email notifications

### Security
- ✅ PDO prepared statements
- ✅ SQL injection protection
- ✅ XSS prevention
- ✅ Input validation
- ✅ Type safety (PHP 8+)

---

## 📊 Database Schema

**Tables:**
- `users` - Admin & authors
- `blog_posts` - Posts (bilingual)
- `blog_categories` - Categories (bilingual)
- `blog_tags` - Tags (bilingual)
- `blog_post_tags` - Many-to-many
- `blog_comments` - Comments

**Sample Data:**
- ✅ 1 admin user
- ✅ 6 categories
- ✅ 10 tags
- ✅ 3 sample posts

---

## 🔒 Security Checklist

- [ ] Change admin password
- [ ] Delete `setup.php`
- [ ] Update email addresses
- [ ] Enable HTTPS
- [ ] Set file permissions (755/777)
- [ ] Configure backups

---

## 🎨 Customization

### Colors
```css
Primary: #FF6B35
Secondary: #FF8C42
```

### Update Emails
- `php/contact.php` - Line 36
- `php/booking.php` - Line 40
- `php/newsletter.php` - Line 180

---

## 📈 Current Status

### Completed ✅
- Phase 1: Bilingual website + Language switcher
- Phase 2: Blog API (PHP 8+) + Database
- Phase 3: Blog HTML pages (EN/AR) + n8n Integration
- Phase 4: Admin Dashboard Core (Login + Dashboard + Auth)
- Phase 5: **Admin Posts Management (CRUD + Editor + Upload)**

### In Progress ⏳
- Media library browser
- User management panel

### Coming Soon 🔜
- Comments system
- Analytics integration
- Image optimization
- Performance enhancements

---

## 🐛 Troubleshooting

**DB Connection Error:**
```php
// Check config/database.php
private $db_name = 'pyramed1_final';
private $username = 'pyramed1_final';
private $password = 'Engmidoz@2020';
```

**Upload Issues:**
```bash
chmod 777 uploads/
chmod 777 uploads/blog/
```

---

## 📞 Support

- Email: info@pyramedia.ae
- Website: https://pyramedia.ae

---

## 📄 License

© 2025 PYRAMEDIA. All rights reserved.

**Built with ❤️ using PHP 8+ & MySQL**
