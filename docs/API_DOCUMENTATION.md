# 📘 PYRAMEDIA Portfolio API Documentation

**Version:** 1.0.0
**Last Updated:** October 31, 2025
**Base URL:** `https://ccode.pyramedia.info/api/`
**Format:** JSON
**Authentication:** Session-based (admin endpoints only)

---

## 📑 Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Rate Limiting](#rate-limiting)
4. [Error Handling](#error-handling)
5. [Endpoints](#endpoints)
   - [List Projects](#list-projects)
   - [Get Single Project](#get-single-project)
   - [Get Featured Projects](#get-featured-projects)
   - [Get Categories](#get-categories)
   - [Get Tags](#get-tags)
   - [Search Projects](#search-projects)
   - [Increment View Count](#increment-view-count)
6. [Response Format](#response-format)
7. [Code Examples](#code-examples)

---

## 🌐 Overview

The PYRAMEDIA Portfolio API is a RESTful API that provides access to portfolio projects, categories, tags, and metrics. It supports bilingual content (English and Arabic) and includes advanced features like full-text search, pagination, and filtering.

### Key Features:
- ✅ RESTful architecture
- ✅ Bilingual support (English/Arabic)
- ✅ Full-text search
- ✅ Advanced filtering
- ✅ Pagination
- ✅ Rate limiting
- ✅ CORS support
- ✅ Secure (SQL injection prevention, XSS protection)

---

## 🔐 Authentication

### Public Endpoints
Most read endpoints are public and don't require authentication:
- List Projects
- Get Single Project
- Get Featured Projects
- Get Categories
- Get Tags
- Search Projects

### Admin Endpoints
Write operations require authentication:
- Create Project
- Update Project
- Delete Project

**Authentication Method:** PHP Sessions

```javascript
// No authentication needed for public endpoints
fetch('https://ccode.pyramedia.info/api/portfolio.php?action=list')
  .then(res => res.json())
  .then(data => console.log(data));
```

---

## ⏱️ Rate Limiting

**Limit:** 200 requests per hour per IP address
**Window:** 1 hour (3600 seconds)
**Method:** Database-backed rate limiting

### Rate Limit Headers
The API includes rate limiting to prevent abuse. If you exceed the limit, you'll receive:

**Response:**
```json
{
  "success": false,
  "error": "Rate limit exceeded. Please try again later.",
  "code": 429,
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

## ❌ Error Handling

All errors follow a consistent format:

```json
{
  "success": false,
  "error": "Error message here",
  "code": 400,
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

### HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request succeeded |
| 400 | Bad Request | Invalid parameters |
| 401 | Unauthorized | Authentication required |
| 404 | Not Found | Resource not found |
| 405 | Method Not Allowed | HTTP method not allowed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

---

## 📍 Endpoints

### List Projects

Get a list of published projects with optional filtering and pagination.

**Endpoint:** `GET /api/portfolio.php?action=list`

**Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `action` | string | Yes | - | Must be "list" |
| `lang` | string | No | `en` | Language code (`en` or `ar`) |
| `category` | string | No | - | Category slug to filter by |
| `featured` | boolean | No | - | Only featured projects (`1` or `0`) |
| `limit` | integer | No | `20` | Number of results (max: 100) |
| `offset` | integer | No | `0` | Pagination offset |
| `sort` | string | No | `published_at` | Sort field |
| `order` | string | No | `DESC` | Sort order (`ASC` or `DESC`) |

**Example Request:**
```javascript
fetch('https://ccode.pyramedia.info/api/portfolio.php?action=list&lang=en&category=ecommerce&limit=10')
  .then(res => res.json())
  .then(data => console.log(data));
```

**Example Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "projects": [
      {
        "id": 1,
        "slug": "300-ecommerce-growth",
        "title": "300% E-Commerce Growth",
        "client_name": "Gulf Retail Group",
        "description": "Transformed online presence...",
        "featured_image": "https://...",
        "view_count": 1250,
        "like_count": 45,
        "published_at": "2024-10-15 10:00:00",
        "is_featured": true,
        "category_slug": "ecommerce",
        "category_name": "E-Commerce",
        "category_color": "#FF6B35",
        "metrics": [
          {
            "metric_key": "sales_growth",
            "metric_value": "300%",
            "label": "Sales Growth",
            "metric_type": "percentage"
          }
        ],
        "tags": [
          {
            "slug": "marketing-automation",
            "name": "Marketing Automation",
            "type": "service"
          }
        ]
      }
    ],
    "pagination": {
      "total": 45,
      "limit": 10,
      "offset": 0,
      "has_more": true
    }
  },
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

### Get Single Project

Get detailed information about a single project.

**Endpoint:** `GET /api/portfolio.php?action=get`

**Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `action` | string | Yes | - | Must be "get" |
| `id` | integer/string | Yes | - | Project ID or slug |
| `slug` | string | No | - | Alternative to ID |
| `lang` | string | No | `en` | Language code (`en` or `ar`) |

**Example Request:**
```javascript
fetch('https://ccode.pyramedia.info/api/portfolio.php?action=get&slug=300-ecommerce-growth&lang=en')
  .then(res => res.json())
  .then(data => console.log(data));
```

**Example Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "id": 1,
    "slug": "300-ecommerce-growth",
    "title": "300% E-Commerce Growth",
    "client_name": "Gulf Retail Group",
    "description": "Full detailed description...",
    "featured_image": "https://...",
    "project_url": "https://gulfretail.ae",
    "project_year": 2024,
    "project_duration": "6 months",
    "view_count": 1250,
    "like_count": 45,
    "share_count": 23,
    "published_at": "2024-10-15 10:00:00",
    "services": ["Marketing Automation", "SEO"],
    "technologies": ["Shopify", "HubSpot"],
    "category_slug": "ecommerce",
    "category_name": "E-Commerce",
    "category_color": "#FF6B35",
    "metrics": [...],
    "tags": [...],
    "images": [
      {
        "image_url": "https://...",
        "image_type": "screenshot",
        "title": "Homepage Redesign",
        "caption": "New responsive homepage"
      }
    ]
  },
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

### Get Featured Projects

Get a list of featured projects for homepage display.

**Endpoint:** `GET /api/portfolio.php?action=featured`

**Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `action` | string | Yes | - | Must be "featured" |
| `lang` | string | No | `en` | Language code (`en` or `ar`) |
| `limit` | integer | No | `5` | Number of results (max: 20) |

**Example Request:**
```javascript
fetch('https://ccode.pyramedia.info/api/portfolio.php?action=featured&lang=en&limit=5')
  .then(res => res.json())
  .then(data => console.log(data));
```

**Example Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "slug": "300-ecommerce-growth",
      "title": "300% E-Commerce Growth",
      "client_name": "Gulf Retail Group",
      "description": "Brief description...",
      "featured_image": "https://...",
      "category_slug": "ecommerce",
      "category_name": "E-Commerce",
      "category_color": "#FF6B35",
      "metrics": [...],
      "tags": [...]
    }
  ],
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

### Get Categories

Get all active portfolio categories.

**Endpoint:** `GET /api/portfolio.php?action=categories`

**Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `action` | string | Yes | - | Must be "categories" |
| `lang` | string | No | `en` | Language code (`en` or `ar`) |

**Example Request:**
```javascript
fetch('https://ccode.pyramedia.info/api/portfolio.php?action=categories&lang=en')
  .then(res => res.json())
  .then(data => console.log(data));
```

**Example Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "slug": "ecommerce",
      "name": "E-Commerce",
      "description": "Online store development and optimization",
      "icon": "fa-shopping-cart",
      "color": "#FF6B35",
      "project_count": 12
    },
    {
      "slug": "branding",
      "name": "Branding",
      "description": "Brand identity and visual design",
      "icon": "fa-palette",
      "color": "#4A90E2",
      "project_count": 8
    }
  ],
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

### Get Tags

Get all tags/skills used in projects.

**Endpoint:** `GET /api/portfolio.php?action=tags`

**Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `action` | string | Yes | - | Must be "tags" |
| `lang` | string | No | `en` | Language code (`en` or `ar`) |
| `type` | string | No | - | Filter by type (`service`, `technology`, `industry`, `skill`) |

**Example Request:**
```javascript
fetch('https://ccode.pyramedia.info/api/portfolio.php?action=tags&type=technology&lang=en')
  .then(res => res.json())
  .then(data => console.log(data));
```

**Example Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "slug": "hubspot",
      "name": "HubSpot",
      "type": "technology",
      "icon": "fa-cog",
      "color": "#FF6B35",
      "usage_count": 15
    }
  ],
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

### Search Projects

Search projects using full-text search.

**Endpoint:** `GET /api/portfolio.php?action=search`

**Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `action` | string | Yes | - | Must be "search" |
| `q` | string | Yes | - | Search query |
| `lang` | string | No | `en` | Language code (`en` or `ar`) |
| `limit` | integer | No | `20` | Number of results (max: 100) |

**Example Request:**
```javascript
fetch('https://ccode.pyramedia.info/api/portfolio.php?action=search&q=ecommerce&lang=en')
  .then(res => res.json())
  .then(data => console.log(data));
```

**Example Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "query": "ecommerce",
    "results": [...],
    "count": 8
  },
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

### Increment View Count

Increment the view count for a project (analytics).

**Endpoint:** `POST /api/portfolio.php?action=increment_view`

**Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `action` | string | Yes | - | Must be "increment_view" |
| `id` | integer | Yes | - | Project ID |

**Example Request:**
```javascript
fetch('https://ccode.pyramedia.info/api/portfolio.php?action=increment_view', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ id: 1 })
})
  .then(res => res.json())
  .then(data => console.log(data));
```

**Example Response:**
```json
{
  "success": true,
  "message": "View count incremented",
  "data": null,
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

## 📦 Response Format

All successful responses follow this format:

```json
{
  "success": true,
  "message": "Success message",
  "data": { /* response data */ },
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

All error responses follow this format:

```json
{
  "success": false,
  "error": "Error message",
  "code": 400,
  "timestamp": "2025-10-31T12:00:00+04:00"
}
```

---

## 💻 Code Examples

### JavaScript (Fetch API)

```javascript
/**
 * Fetch portfolio projects
 */
async function getProjects(category = null, lang = 'en') {
  try {
    const params = new URLSearchParams({
      action: 'list',
      lang: lang,
      limit: 9
    });

    if (category && category !== 'all') {
      params.append('category', category);
    }

    const response = await fetch(`https://ccode.pyramedia.info/api/portfolio.php?${params}`);
    const data = await response.json();

    if (data.success) {
      return data.data.projects;
    } else {
      throw new Error(data.error);
    }
  } catch (error) {
    console.error('Error fetching projects:', error);
    return [];
  }
}

/**
 * Get single project
 */
async function getProject(slug, lang = 'en') {
  try {
    const response = await fetch(
      `https://ccode.pyramedia.info/api/portfolio.php?action=get&slug=${slug}&lang=${lang}`
    );
    const data = await response.json();

    if (data.success) {
      return data.data;
    } else {
      throw new Error(data.error);
    }
  } catch (error) {
    console.error('Error fetching project:', error);
    return null;
  }
}
```

### PHP (cURL)

```php
<?php
/**
 * Fetch portfolio projects
 */
function getProjects($category = null, $lang = 'en', $limit = 9) {
    $params = http_build_query([
        'action' => 'list',
        'lang' => $lang,
        'limit' => $limit,
        'category' => $category
    ]);

    $url = "https://ccode.pyramedia.info/api/portfolio.php?{$params}";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    return $data['success'] ? $data['data']['projects'] : [];
}

// Usage
$projects = getProjects('ecommerce', 'en', 10);
foreach ($projects as $project) {
    echo $project['title'] . "\n";
}
?>
```

---

## 🔒 Security Features

The API includes multiple security layers:

### 1. SQL Injection Prevention
✅ All queries use PDO prepared statements

### 2. XSS Protection
✅ All input is sanitized
✅ Output uses htmlspecialchars

### 3. CSRF Protection
✅ Token-based CSRF protection for write operations

### 4. Rate Limiting
✅ 200 requests per hour per IP

### 5. Input Validation
✅ Type checking
✅ Length validation
✅ Whitelist validation

---

## 📝 Changelog

### Version 1.0.0 (2025-10-31)
- ✅ Initial release
- ✅ RESTful endpoints
- ✅ Bilingual support
- ✅ Full-text search
- ✅ Rate limiting
- ✅ Security features

---

## 🤝 Support

For API support or questions:
- 📧 Email: dev@pyramedia.info
- 🌐 Website: https://pyramedia.info
- 📚 Docs: https://docs.pyramedia.info

---

**Built with ❤️ by PYRAMEDIA Development Team**
