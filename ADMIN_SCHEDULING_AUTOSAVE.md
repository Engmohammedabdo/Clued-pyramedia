# PYRAMEDIA Admin - Post Scheduling & Auto-save Features

## Overview

This document describes the Post Scheduling and Auto-save Drafts features implemented in the PYRAMEDIA admin dashboard.

---

## 1. Post Scheduling System

### Features
- **Schedule posts for future publication** - Set exact date and time for automatic publishing
- **Visual date/time picker** - User-friendly datetime-local input
- **Scheduled status badge** - Clear visual indicator with clock icon
- **Automatic publishing** - Cron job automatically publishes scheduled posts
- **Validation** - Prevents scheduling in the past

### How to Use

#### Creating a Scheduled Post
1. Go to **Posts → New Post**
2. Fill in your post content (title, content, category, etc.)
3. In the **Publish** box, select **"Schedule for Later"** from the Status dropdown
4. Choose your desired **Publish Date & Time**
5. Click **"Create Post"**

#### Editing Scheduled Posts
- Scheduled posts appear with a blue badge and clock icon: 🕐 Scheduled
- You can change the scheduled time by editing the post
- Change status to "Publish Now" to publish immediately
- Change status to "Draft" to cancel scheduling

### Database Schema
```sql
-- Post scheduling uses these fields:
status ENUM('draft', 'published', 'scheduled', 'archived')
scheduled_for TIMESTAMP NULL    -- When to auto-publish
published_at TIMESTAMP NULL     -- When actually published
```

### Cron Job Setup

The system requires a cron job to automatically publish scheduled posts.

#### Option 1: System Crontab (Recommended)

Add to your server's crontab (run every 5 minutes):

```bash
*/5 * * * * /usr/bin/php /path/to/pyramedia/admin/cron.php >> /var/log/pyramedia-cron.log 2>&1
```

**Steps:**
1. SSH into your server
2. Edit crontab: `crontab -e`
3. Add the line above (update path to match your installation)
4. Save and exit

#### Option 2: cPanel Cron Jobs

1. Log into cPanel
2. Navigate to **Advanced → Cron Jobs**
3. Add new cron job:
   - **Common Settings:** Every 5 minutes
   - **Command:** `/usr/bin/php /home/username/public_html/admin/cron.php`
4. Save

#### Option 3: Manual HTTP Trigger (Development Only)

For testing, you can manually trigger the cron job via HTTP:

```bash
curl https://yourdomain.com/admin/cron.php
```

**Note:** This requires admin authentication when accessed via HTTP.

#### Verifying Cron Job

Check the log file to verify cron job execution:

```bash
tail -f /var/log/pyramedia-cron.log
```

Sample output:
```
[PYRAMEDIA CRON] Published scheduled post #42: Future of Marketing (scheduled for 2025-11-01 10:00:00)
Successfully published 1 scheduled post(s)
```

### File Structure
```
admin/
├── cron.php              # Cron job handler (publishes scheduled posts)
├── posts.php             # Main posts controller (handles scheduling logic)
├── pages/
│   ├── posts-form.php    # Post editor with scheduling UI
│   └── posts-list.php    # Posts list with scheduled filter
```

---

## 2. Auto-save Drafts System

### Features
- **Automatic saving every 30 seconds** - Prevents data loss
- **Smart triggers** - Saves on content changes
- **Visual feedback** - Shows "Draft saved at [time]" indicator
- **Non-intrusive** - Works silently in background
- **New post creation** - Creates draft automatically on first save
- **URL updating** - Updates URL to edit mode after first auto-save

### How It Works

#### User Experience
1. Start typing in the post editor
2. After 30 seconds of changes, draft is automatically saved
3. Visual indicator shows: "Draft saved at 2:45:30 PM"
4. Continue editing - saves every 30 seconds when changes detected

#### Technical Flow
```
User types → Timer starts (30s) → Auto-save triggered →
POST to autosave.php → Save to database → Update indicator
```

### Auto-save Behavior

#### When Auto-save Triggers
- ✅ Any input field change (title, content, meta fields)
- ✅ TinyMCE editor content change
- ✅ Image upload
- ✅ Category or tag selection

#### What Gets Saved
- All post content (EN & AR)
- Meta information (description, keywords)
- Featured image
- Category selection
- Excerpts and reading time

#### What Doesn't Trigger Auto-save
- ❌ Status changes (manual save required)
- ❌ Publishing actions (manual save required)
- ❌ Less than 30 seconds since last save

### API Endpoint

**URL:** `/admin/autosave.php`

**Method:** `POST`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "post_id": 0,
  "title_en": "My Post Title",
  "content_en": "<p>Post content here...</p>",
  "category_id": 1,
  "featured_image": "/uploads/image.jpg",
  ...
}
```

**Response (Success):**
```json
{
  "success": true,
  "action": "created|updated",
  "post_id": 42,
  "timestamp": 1698765432,
  "message": "Draft auto-saved"
}
```

**Response (Error):**
```json
{
  "success": false,
  "error": "Error message",
  "timestamp": 1698765432
}
```

### File Structure
```
admin/
├── autosave.php          # Auto-save endpoint (handles AJAX saves)
├── pages/
│   └── posts-form.php    # Post editor with auto-save JavaScript
```

### JavaScript Implementation

The auto-save system is implemented in `admin/pages/posts-form.php`:

```javascript
// Configuration
const AUTO_SAVE_INTERVAL = 30000; // 30 seconds

// Main auto-save function
function performAutoSave() {
    // Collect all form data
    const formData = { ... };

    // POST to autosave.php
    fetch('autosave.php', {
        method: 'POST',
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        // Update indicator
        // Handle new post creation
    });
}
```

### Security

Both systems include security measures:

1. **Authentication Required**
   - Must be logged in to auto-save
   - Cron job verifies admin access for HTTP triggers

2. **Permission Checks**
   - Authors can only edit their own posts
   - Admins can edit all posts

3. **CSRF Protection**
   - Token validation on manual saves
   - Auto-save uses session authentication

4. **Input Validation**
   - All data sanitized before database insertion
   - SQL injection prevention via PDO prepared statements

---

## Browser Compatibility

### Auto-save Feature
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+

### Scheduling (datetime-local input)
- ✅ Chrome 20+
- ✅ Firefox 93+
- ✅ Safari 14.1+
- ✅ Edge 79+

**Note:** For older browsers, the datetime-local input falls back to text input.

---

## Troubleshooting

### Scheduled Posts Not Publishing

**Problem:** Posts stay in "scheduled" status past their scheduled time

**Solutions:**
1. Verify cron job is running: `grep PYRAMEDIA /var/log/cron`
2. Check cron job permissions: `ls -l admin/cron.php`
3. Test manual execution: `php admin/cron.php`
4. Check error logs: `tail -f /var/log/pyramedia-cron.log`

### Auto-save Not Working

**Problem:** "Auto-save failed" appears in indicator

**Solutions:**
1. Check browser console for JavaScript errors (F12)
2. Verify `autosave.php` is accessible
3. Check file permissions: `ls -l admin/autosave.php`
4. Verify session is active (logged in)
5. Check server error logs for PHP errors

### Duplicate Slugs on Auto-save

**Problem:** "Slug already exists" error

**Solution:** The auto-save endpoint includes automatic slug uniqueness handling. If you see this error:
1. Manually edit the slug to be unique
2. Check for existing posts with similar titles
3. The system will append `-1`, `-2`, etc. to make slugs unique

---

## Performance Considerations

### Auto-save
- **Database writes:** 1 write per 30 seconds (when editing)
- **Network traffic:** ~2-5 KB per auto-save request
- **Impact:** Minimal - uses asynchronous AJAX

### Cron Job
- **Frequency:** Every 5 minutes (recommended)
- **Query load:** Single SELECT + UPDATE per scheduled post
- **Impact:** Negligible for < 100 scheduled posts

### Optimization Tips
1. Don't set cron job more frequently than every 5 minutes
2. Clean up old draft posts periodically
3. Use database indexes (already configured)

---

## Statistics

### Implementation Details
- **Auto-save code:** ~150 lines JavaScript, ~180 lines PHP
- **Scheduling code:** ~100 lines UI, ~50 lines PHP logic
- **Cron handler:** ~120 lines PHP
- **Total files modified:** 4 files
- **Total files created:** 2 new files

### Features Count
- ✅ Post Scheduling System - COMPLETE
- ✅ Auto-save Drafts - COMPLETE
- ✅ Visual date/time picker - COMPLETE
- ✅ Scheduled status filtering - COMPLETE
- ✅ Cron job automation - COMPLETE
- ✅ Real-time save indicator - COMPLETE

---

## Future Enhancements

Potential improvements for future versions:

1. **Email Notifications**
   - Notify admin when scheduled post is published
   - Alert if cron job fails

2. **Post Revisions**
   - Keep history of auto-saved versions
   - Restore previous versions

3. **Conflict Detection**
   - Warn if multiple users editing same post
   - Lock mechanism for concurrent edits

4. **Advanced Scheduling**
   - Recurring posts (weekly, monthly)
   - Timezone selection
   - Bulk scheduling interface

5. **Auto-save Improvements**
   - Offline support with localStorage
   - Delta saves (only changed fields)
   - Visual diff showing unsaved changes

---

## Credits

**Developed for:** PYRAMEDIA Marketing Agency
**Version:** 1.0
**Date:** November 2025
**Technologies:** PHP 8+, JavaScript ES6+, MySQL 8+

---

## Support

For issues or questions:
- Check the troubleshooting section above
- Review server error logs
- Contact system administrator
