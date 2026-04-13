# BANTU Plus SVOD Platform - Setup & Configuration Guide

## Overview

BANTU Plus is a complete Netflix-style SVOD (Subscription Video On Demand) platform built as a custom WordPress Twenty Twenty-Five theme. This implementation uses pure PHP/JavaScript with no plugins, integrating Bunny.net for video delivery, Stripe for payments, and WordPress core for content management.

## System Architecture

### Core Components

1. **Video Management** - Custom post type (`video`) with Bunny.net integration
2. **Authentication** - Custom login/register using WordPress core functions
3. **Membership System** - Custom database tables for subscription tracking
4. **Payment Processing** - Stripe integration with 7-day trials
5. **Video Streaming** - HLS.js player with progress tracking
6. **REST API** - Mobile app integration endpoints
7. **Dark Theme** - Netflix-inspired UI with responsive design

### Technology Stack

- **Base**: WordPress Twenty Twenty-Five Theme
- **Video Hosting**: Bunny.net Stream CDN
- **Payments**: Stripe (primary), Flutterwave (optional)
- **Player**: HLS.js (CDN-hosted)
- **API**: WordPress REST API v2
- **Database**: Custom WordPress tables for memberships/payments

## Setup Instructions

### Step 1: Install Theme

The BANTU Plus theme is already in place as a modified Twenty Twenty-Five theme. No additional installation needed.

### Step 2: Configure Bunny.net

1. Create an account at [bunnycdn.com](https://bunnycdn.com)
2. Create a **Stream** library (video service)
3. Get your API credentials:
   - API Key (Account > API)
   - Library ID (Stream > Your Library)
   - Storage Zone name
   - CDN URL (pull zone URL)

4. In WordPress Admin: Go to **BANTU Plus > Settings**
5. Fill in all Bunny.net fields
6. Save settings

### Step 3: Configure Stripe

1. Create a Stripe account at [stripe.com](https://stripe.com)
2. Get your API keys from **Developers > API Keys**:
   - Publishable Key (starts with `pk_`)
   - Secret Key (starts with `sk_`)

3. Optionally set up webhook at:
   - Endpoint: `https://yoursite.com/?bantu_webhook=stripe`
   - Events: `customer.subscription.updated`, `customer.subscription.deleted`, `invoice.payment_succeeded`

4. In WordPress Admin: Go to **BANTU Plus > Settings**
5. Fill in Stripe keys
6. Save settings

### Step 4: Create Pages for Key Routes

Create these pages in WordPress Admin:

1. **Sign In** - Use shortcode: `[bantu_login_form]`
2. **Register** - Use shortcode: `[bantu_register_form]`
3. **Account** - Use shortcode: `[bantu_account_dashboard]`
4. **Subscribe** - Use shortcode: `[bantu_stripe_checkout]`
5. **Videos** - Set post type to "Videos" (archive will auto-display)

## Usage

### Uploading Videos

1. Go to **Videos > Bunny Upload** in WordPress Admin
2. Fill in:
   - Title
   - Description
   - Category
   - Video file (MP4, WebM, etc.)
   - Thumbnail (optional)
   - Duration (minutes)
3. Click "Upload to Bunny"
4. Video will be processed and HLS-encoded automatically

### Managing Memberships

Memberships are tracked in custom database tables:
- `wp_bantu_memberships` - Active subscriptions
- `wp_bantu_payments` - Payment history
- `wp_bantu_trials` - Trial tracking

Check from admin: **Users > User List** (view user meta for membership details)

### REST API Endpoints (Mobile)

All endpoints use WordPress authentication (cookie or token-based).

**Authentication**
```
POST /wp-json/bantu/v1/auth/login
GET /wp-json/bantu/v1/auth/me
POST /wp-json/bantu/v1/auth/logout
```

**Videos**
```
GET /wp-json/bantu/v1/videos?per_page=12&page=1&category=drama
GET /wp-json/bantu/v1/videos/{id}
GET /wp-json/bantu/v1/videos/{id}/stream (requires membership)
```

**Progress Tracking**
```
POST /wp-json/bantu/v1/account/progress
GET /wp-json/bantu/v1/account/progress/{video_id}
```

**Membership**
```
GET /wp-json/bantu/v1/membership/status
```

## File Structure

```
/vercel/share/v0-project/
├── functions.php                 # Main theme functions
├── single-video.php             # Single video template
├── archive-video.php            # Video grid template
├── assets/
│   ├── css/
│   │   └── bantu-plus.css       # Netflix-style dark theme
│   └── js/
│       └── bantu-plus.js        # HLS player & interactions
├── inc/
│   ├── admin-video-upload.php   # Bunny.net upload form
│   ├── bunny-settings.php       # CDN & payment settings
│   ├── membership-database.php  # Database schema & functions
│   ├── auth-shortcodes.php      # Login/register/account
│   ├── stripe-payments.php      # Payment processing
│   └── rest-api.php             # Mobile API endpoints
└── parts/ & patterns/           # Theme blocks & templates
```

## Security Considerations

### Implemented

- Nonce verification on all forms
- Sanitized input/output
- Password hashing (WordPress native)
- User capability checks
- HTTPS enforced (Stripe requirement)
- Access control checks before streaming
- User meta for membership tracking

### To Implement

- Enable RLS (Row-Level Security) if using Supabase
- Set secure Stripe webhook signature validation
- Use environment variables for API keys (not hard-coded)
- Implement rate limiting on API endpoints
- Add CORS headers for mobile apps
- Monitor payment failures and retry logic

## Configuration Options

### Environment Variables (Optional)

Add to `wp-config.php` or use options page:

```php
define( 'BUNNY_API_KEY', 'your-bunny-api-key' );
define( 'BUNNY_LIBRARY_ID', '12345' );
define( 'BUNNY_STORAGE_ZONE', 'your-zone' );
define( 'BUNNY_CDN_URL', 'https://your-cdn.b-cdn.net' );
define( 'STRIPE_PUBLIC_KEY', 'pk_...' );
define( 'STRIPE_SECRET_KEY', 'sk_...' );
```

### Database Tables Created

On first activation, these tables are auto-created:

- `wp_bantu_memberships` - User subscription records
- `wp_bantu_payments` - Payment transactions
- `wp_bantu_trials` - Free trial tracking

## Customization

### Styling

Edit `/assets/css/bantu-plus.css`:
- Colors: See `:root` variables (dark theme palette)
- Fonts: Uses system fonts (fast loading)
- Breakpoints: Mobile-first responsive design

### Video Categories

Go to **Videos > Categories** to add custom categories like:
- Diaspora Docs
- Documentaries
- Tutorials
- etc.

### Trial Period

Change trial days in `/inc/stripe-payments.php` (currently 7 days):
```php
$trial_days = 7; // Change this value
```

## Troubleshooting

### Videos Not Uploading

1. Check Bunny.net credentials in **BANTU Plus > Settings**
2. Verify file size is under 5GB
3. Check server has enough disk space for temp upload
4. Review WordPress file upload settings

### Payment Issues

1. Verify Stripe keys are correct and active
2. Check HTTPS is enabled (required)
3. Ensure users can create accounts (Settings > General > Membership)
4. Review payment logs in user account dashboard

### HLS Player Not Working

1. Check Bunny GUID is saved in post meta
2. Verify CDN URL is correct
3. Test HLS URL directly in browser: `https://cdn.url/guid/playlist.m3u8`
4. Check browser console for CORS errors

### Mobile App Issues

1. Ensure REST API is enabled
2. Verify authentication token is passed
3. Check user has active membership
4. Test endpoints with Postman or cURL

## Next Steps

### For Production

1. Set up SSL/TLS certificate (HTTPS)
2. Configure Bunny.net DRM and geo-blocking
3. Enable Stripe webhooks for payment confirmations
4. Implement email notifications for trials/expiry
5. Set up automated billing and retries
6. Add user support system
7. Implement analytics tracking
8. Deploy to production server

### For Enhancement

1. Add video chapters/bookmarks
2. Implement live streaming
3. Add social features (sharing, ratings)
4. Create admin analytics dashboard
5. Add multi-language support
6. Implement ad-supported tiers
7. Create mobile apps with AppMySite/MobiLoud

## Support & Documentation

- Bunny.net Docs: https://docs.bunnycdn.com
- Stripe Docs: https://stripe.com/docs
- WordPress REST API: https://developer.wordpress.org/rest-api
- HLS.js Docs: https://github.com/video-dev/hls.js

## License

This theme is built on WordPress Twenty Twenty-Five (GPL v2+). Customizations follow the same license.

---

**Version**: 1.0  
**Last Updated**: 2025  
**Author**: BANTU Plus Development Team
