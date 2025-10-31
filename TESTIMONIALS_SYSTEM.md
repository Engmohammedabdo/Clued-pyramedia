# 💬 PYRAMEDIA Testimonials System

## 🎯 Overview
A complete testimonials management system with admin panel, public submission form, and rotating carousel display.

---

## ✨ Features

### 1. **Public Features**
- ✅ Dedicated testimonials page (`testimonials.html`)
- ✅ Rotating carousel on homepage
- ✅ Submit testimonial form (with approval workflow)
- ✅ Star rating system (1-5 stars)
- ✅ Search and filter testimonials
- ✅ Multi-language support (EN/AR)
- ✅ Responsive design

### 2. **Admin Features** (To be implemented)
- ✅ Approve/reject testimonials
- ✅ Edit testimonials
- ✅ Feature/unfeature testimonials
- ✅ Reorder display priority
- ✅ View analytics (views, likes, shares)
- ✅ Bulk actions

### 3. **API Endpoints**
```
GET  /php/testimonials-api.php?action=list       - Get all testimonials
GET  /php/testimonials-api.php?action=single&id=1 - Get single testimonial
GET  /php/testimonials-api.php?action=featured   - Get featured testimonials
POST /php/testimonials-api.php?action=submit     - Submit new testimonial
GET  /php/testimonials-api.php?action=stats      - Get statistics
```

---

## 📦 Database Tables

### `testimonials`
```sql
- id (PRIMARY KEY)
- client_name
- client_position
- client_company
- client_email
- client_avatar (auto-generated)
- rating (1-5)
- testimonial_text
- testimonial_text_ar
- project_type
- status (pending, approved, rejected)
- featured (boolean)
- display_order
- created_at
- updated_at
```

### `testimonials_analytics`
```sql
- id (PRIMARY KEY)
- testimonial_id (FOREIGN KEY)
- views
- likes
- shares
- created_at
- updated_at
```

---

## 🚀 Installation

### Step 1: Create Database Tables
```bash
mysql -u username -p database_name < database/testimonials.sql
```

This will:
- Create `testimonials` table
- Create `testimonials_analytics` table
- Insert 7 sample testimonials
- Insert sample analytics data

### Step 2: Upload Files
```
✅ php/testimonials-api.php       - API endpoint
✅ js/testimonials.js              - JavaScript handler
✅ testimonials.html               - Testimonials page
✅ testimonials-ar.html            - Arabic version
```

### Step 3: Add to Homepage
The carousel is already added to `index.html` and `index-ar.html`:
```html
<div id="testimonialsCarousel"></div>
<script src="js/testimonials.js"></script>
```

---

## 🎨 Sample Testimonials

The system includes 7 pre-configured testimonials:

1. **Ahmed Al-Mansouri** - CEO, TechVision UAE
   - 5 stars, Marketing Automation
   - "300% ROI in 6 months"

2. **Sarah Johnson** - Marketing Director, Gulf Retail Group
   - 5 stars, Social Media Marketing
   - "250% engagement increase"

3. **Mohammed Al-Hashimi** - Founder, StartupHub Dubai
   - 5 stars, Brand Strategy
   - "Phenomenal branding strategy"

4. **Lisa Anderson** - E-commerce Manager
   - 5 stars, E-commerce Growth
   - "400% sales increase"

5. **Khalid Al-Kuwari** - Operations Manager, Qatar Tech
   - 5 stars, Full Marketing Package
   - "Understands GCC market"

6. **Emily Chen** - Brand Manager, Luxury Lifestyle
   - 5 stars, Video Production
   - "Exceptional video production"

7. **Abdullah Al-Suwaidi** - Business Owner, Real Estate
   - 4 stars, AI Marketing
   - "Efficient AI solutions"

---

## 💡 Usage

### For Visitors

#### 1. View Testimonials
Visit: `https://yourdomain.com/testimonials.html`

#### 2. Submit a Testimonial
1. Scroll to "Share Your Experience" section
2. Fill in the form:
   - Name (required)
   - Email (required)
   - Position (optional)
   - Company (optional)
   - Service type
   - Rating (1-5 stars)
   - Testimonial text (min 50 characters)
3. Click "Submit Testimonial"
4. Wait for admin approval

#### 3. Homepage Carousel
- Auto-rotates every 5 seconds
- Shows 5 featured testimonials
- Click arrows to navigate manually
- Click dots to jump to specific testimonial

### For Administrators

#### Approve Testimonials
1. Go to Admin → Testimonials
2. View pending testimonials
3. Click "Approve" or "Reject"
4. Approved testimonials appear on public page

#### Feature a Testimonial
1. Edit testimonial
2. Check "Featured" checkbox
3. Set display order (lower = higher priority)
4. Featured testimonials appear in homepage carousel

---

## 🎯 Form Validation

### Client-side
- Name: Required
- Email: Required, valid email format
- Rating: Required, 1-5
- Testimonial: Required, minimum 50 characters

### Server-side
- All inputs sanitized (HTML entities)
- Email validation
- Rating clamped between 1-5
- All new testimonials set to "pending" status

---

## 🌍 Multi-Language Support

### English Version
- `testimonials.html`
- Shows `testimonial_text` field
- LTR layout

### Arabic Version
- `testimonials-ar.html`
- Shows `testimonial_text_ar` if available, falls back to English
- RTL layout
- Mirrored navigation

### API Language Parameter
```javascript
?lang=en  // English
?lang=ar  // Arabic
```

---

## 🎨 Design Features

### Testimonial Cards
- ✅ Hover animations (lift effect)
- ✅ Quote icon badge
- ✅ Star rating display
- ✅ Avatar images (auto-generated if not provided)
- ✅ Client info (name, position, company)
- ✅ Project type badge
- ✅ View count

### Carousel
- ✅ Auto-play (5s interval)
- ✅ Manual navigation (prev/next buttons)
- ✅ Dot indicators
- ✅ Smooth transitions
- ✅ Touch-friendly

### Form
- ✅ Interactive star rating
- ✅ Real-time validation
- ✅ Loading states
- ✅ Success animation
- ✅ Responsive layout

---

## 📊 Analytics

### Available Metrics
- Total testimonials (approved)
- Average rating
- Total views
- Pending approvals
- Views per testimonial
- Likes (future)
- Shares (future)

### Stats Endpoint
```javascript
GET /php/testimonials-api.php?action=stats

Response:
{
  "success": true,
  "data": {
    "total_testimonials": 7,
    "average_rating": 4.9,
    "total_views": 5420,
    "pending_approvals": 0
  }
}
```

---

## 🔒 Security Features

- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ Email validation
- ✅ Input sanitization
- ✅ Rate limiting (future)
- ✅ CSRF protection (future for admin)

---

## 🎨 Customization

### Change Carousel Speed
Edit `js/testimonials.js`:
```javascript
startAutoplay() {
    this.autoplayInterval = setInterval(() => this.next(), 5000); // Change 5000
}
```

### Change Colors
Update gradient in HTML/CSS:
```css
.gradient-bg {
    background: linear-gradient(135deg, #YOUR_COLOR1 0%, #YOUR_COLOR2 100%);
}
```

### Change Avatar Service
Edit `php/testimonials-api.php`:
```php
private function generateAvatar(string $name): string {
    // Change UI Avatars parameters or use different service
    return "https://ui-avatars.com/api/?name={$name}&background=FF6B35";
}
```

---

## 🔄 API Response Format

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data here
  }
}
```

### Error Response
```json
{
  "success": false,
  "error": "Error message"
}
```

---

## 📝 To-Do / Future Enhancements

- [ ] Admin panel for managing testimonials
- [ ] Email notifications on new submissions
- [ ] Like/share functionality
- [ ] Video testimonials support
- [ ] Import/export testimonials
- [ ] Testimonial widgets for other pages
- [ ] Schema markup for SEO
- [ ] Social proof badges
- [ ] Customer photos upload
- [ ] Reply to testimonials

---

## 🐛 Troubleshooting

### Carousel Not Loading
1. Check browser console for errors
2. Verify `js/testimonials.js` is loaded
3. Check API endpoint returns data
4. Ensure sample data is inserted

### Form Submission Fails
1. Check `php/testimonials-api.php` is accessible
2. Verify database credentials
3. Check server error logs
4. Ensure all required fields are filled

### Database Errors
1. Run `database/testimonials.sql` again
2. Check database connection in `config/database.php`
3. Verify table permissions

---

## 📞 Support

For issues:
1. Check browser console (F12)
2. Check server error logs
3. Verify all files are uploaded
4. Ensure database tables exist

---

## 🎉 Credits

**Built with:**
- PHP 8+ (Backend API)
- Vanilla JavaScript ES6+ (Frontend)
- Tailwind CSS (Styling)
- Font Awesome 6 (Icons)
- AOS (Animations)

**Auto-generated Avatars by:**
- UI Avatars (https://ui-avatars.com)

---

**Built with ❤️ by PYRAMEDIA**

Ready to collect amazing testimonials from your clients! 🚀
