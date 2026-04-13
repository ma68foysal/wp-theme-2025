# BANTU Plus SVOD - Frontend User Experience & Workflow

## User Journey Overview

### 1. Homepage Experience

#### What Users See:
1. **Hero Section** (Featured Video)
   - Large background image of the newest video
   - Gradient overlay for text readability
   - "New Release" badge
   - Video title and description
   - Two action buttons: "Play Now" and "More Info"
   - Professional fade-in animation on load

2. **Content Rows by Category**
   - 8 horizontal scrolling sections (one per category)
   - Each row contains up to 8 videos
   - Videos displayed as thumbnail cards
   - Smooth hover effects showing title and duration
   - Scroll left/right with mouse or arrow keys
   - Touch-friendly swipe support on mobile

3. **Browse Section**
   - Call-to-action to view all videos
   - Links to full video archive

#### Styling & Feel:
- **Dark Netflix aesthetic**: Deep blue background (#0a0e27)
- **Red accent buttons**: #e50914 for actions
- **Smooth animations**: Cards scale up on hover, text fades in
- **Professional spacing**: 2-4rem padding between sections
- **Typography**: Large, readable fonts with proper hierarchy

---

### 2. Video Player Experience

#### Access Control:
**User Not Logged In:**
- Paywall displays
- "Sign In Required" message
- Two CTA buttons: "Sign In" and "Create Account"
- Video player is blurred/hidden

**User Logged In (No Membership):**
- Paywall displays
- "Premium Content" message
- Explain subscription required
- Two buttons: "Subscribe Now" and "Manage Subscription"
- Player is blurred/hidden

**User Logged In (Active Membership):**
- Video player loads and auto-plays
- HLS.js handles streaming with adaptive bitrate
- Shows video quality options
- Progress bar with timestamps
- Standard video controls (play, pause, volume, fullscreen)
- Membership expiry shown below player

#### Player Features:
- **HLS Streaming**: Bunny.net video feeds via HLS protocol
- **Adaptive Bitrate**: Automatically adjusts quality (4K → 360p)
- **Progress Tracking**: Saves watch position every 10 seconds
- **Auto-Resume**: When returning, player starts from saved position
- **Mobile Support**: 
  - Full-screen mode on mobile
  - Touch-friendly controls
  - Playsinline for iOS compatibility
  - Landscape orientation support

#### Player Layout:
```
┌─────────────────────────────────────────┐
│                                         │
│     Video Player (16:9 aspect)          │
│     with HLS.js controls                │
│                                         │
└─────────────────────────────────────────┘

Video Title
Category Links
[Membership Status]
Comments / Related Videos (future)
```

---

### 3. Video Grid/Archive Page

#### Layout:
- **Responsive Grid**: 
  - Mobile: 1-2 columns
  - Tablet: 2-3 columns
  - Desktop: 4-5 columns
- **Cards**: Each video is a clickable card with thumbnail
- **Search Bar**: Full-width at top for searching videos
- **Pagination**: Navigate between pages of videos

#### Card Interactions:
1. **Hover**: 
   - Image scales up slightly (1.08x)
   - Text overlay fades in
   - Red glow shadow appears
   - Shows title (2 lines max) and duration

2. **Click**: 
   - Navigates to video player page
   - Player auto-loads for logged-in users with membership

3. **Keyboard**: 
   - Tab navigation through cards
   - Enter/Space to select and navigate

---

### 4. Search Experience

#### Real-Time Search:
1. User types in search box
2. **Debounced**: 500ms wait after typing stops
3. **AJAX Request**: Searches `bantu_search_videos` action
4. **Live Results**: Grid updates with matching videos
5. **Minimum 2 characters**: Won't search on 1 character

#### Search Results:
- Same grid layout as regular archive
- Shows only matching videos
- If no results: "No videos found" message with back button
- Results include: title, thumbnail, duration

---

### 5. Authentication Flow

#### Registration:
```
[Homepage]
    ↓
[Click "Create Account"]
    ↓
[/register page with shortcode form]
    ↓
[Email + Password input]
    ↓
[Validate & create user]
    ↓
[Auto-login → Homepage]
```

#### Login:
```
[Video Paywall]
    ↓
[Click "Sign In"]
    ↓
[/login page with shortcode form]
    ↓
[Email + Password input]
    ↓
[Validate credentials]
    ↓
[Create session]
    ↓
[Redirect back to video]
```

---

### 6. Subscription/Membership Flow

#### Subscribe Page (`/subscribe`):
```
┌─────────────────────────────────────────┐
│         SUBSCRIBE TO BANTU PLUS         │
├─────────────────────────────────────────┤
│                                         │
│  Standard Plan        Premium Plan      │
│  $5.99/month         $9.99/month        │
│  (7-day free trial)  (7-day free trial)│
│                                         │
│  [Subscribe]         [Subscribe]        │
│                                         │
└─────────────────────────────────────────┘
```

#### Payment:
1. User selects plan
2. Stripe Checkout page opens
3. Enter card details
4. Confirm payment
5. Webhook creates membership record
6. User redirected with success message
7. Can now watch videos

#### Account Dashboard (`/account`):
```
Welcome, [User Name]

Active Membership:
  Plan: Standard ($5.99/month)
  Expires: [Date]
  Days Remaining: [X]

[Manage Subscription]
[View Billing History]
[Logout]
```

---

## Component Structure

### Template Files

#### `single-video.php`
- Displays single video with player
- Checks membership status
- Shows paywall if needed
- Includes video metadata (title, category, duration)

#### `archive-video.php`
- Displays grid of videos in selected category
- Includes search functionality
- Pagination for large sets
- Category header with gradient background

#### `index.php`
- Homepage template
- Includes hero section and content rows
- Browse all videos CTA

#### `template-parts/home-hero.php`
- Featured video hero section
- Image background with gradient
- Title, description, action buttons
- Auto-fetches latest video

#### `template-parts/content-rows.php`
- Generates horizontal scrolling content rows
- One row per video category
- 8 videos per row
- Smooth scroll with keyboard support

### JavaScript Features

#### `assets/js/bantu-plus.js`
Handles:
1. **HLS Player Initialization**
   - Loads HLS.js library
   - Manages video playback
   - Error handling and fallbacks

2. **Progress Tracking**
   - localStorage for watch position
   - Saves every 10 seconds
   - Resumes on page reload

3. **Search Functionality**
   - Real-time AJAX search
   - Debounced input (500ms)
   - Results display in grid

4. **Video Grid Interactions**
   - Click handling for video cards
   - Keyboard navigation
   - Hover effects management

5. **Content Row Scrolling**
   - Horizontal scroll with arrow keys
   - Smooth scroll behavior
   - Scroll indicators

### CSS Architecture

#### `assets/css/bantu-plus.css`
Includes:
1. **Color System** (CSS custom properties)
2. **Typography** (headings, body, sizes)
3. **Layout** (containers, grids, flexbox)
4. **Components** (buttons, cards, hero, paywall)
5. **Animations** (fade-in, scale, hover)
6. **Responsive** (mobile, tablet, desktop breakpoints)

---

## Visual Design Highlights

### Color Psychology:
- **Deep Blue Background**: Creates immersive, TV-like environment
- **Red Actions**: Netflix-inspired, draws attention to CTAs
- **Gray Text**: Secondary information is de-emphasized
- **High Contrast**: White text on dark background = 4.5:1+ WCAG ratio

### Interactive States:
- **Hover**: +10% scale, red shadow glow
- **Focus**: 2px outline in red
- **Active**: Darker background, different state indicator
- **Disabled**: 50% opacity

### Spacing & Alignment:
- **Hero**: 2rem padding, centered content
- **Cards**: 1.5rem gap, aligned grid
- **Text**: 1.6 line-height, readable
- **Sections**: 3-4rem bottom margin

### Typography Hierarchy:
```
H1: 2-3.5rem   (Hero title, page title)
H2: 1.5-2.5rem (Section header)
H3: 1.25-1.75rem (Card title, subsection)
Body: 1rem     (Description, metadata)
Small: 0.75-0.85rem (Tags, duration, credits)
```

---

## Mobile vs Desktop Experience

### Mobile (< 640px)
- Hero height: 50vh (shorter to see content faster)
- Single video card per row
- Search bar full-width
- Touch-friendly larger tap targets
- Horizontal scroll more prominent
- Fonts scale down via clamp()

### Desktop (> 1024px)
- Hero height: 80vh (full immersion)
- 4-5 video cards per row
- Hover effects more pronounced
- Keyboard navigation (arrow keys)
- Smooth animations on all elements
- Full-size typography

---

## Performance Considerations

### Image Optimization:
- Thumbnails served at `medium` size from Bunny.net
- Lazy loading with Intersection Observer
- Fallback solid color if image unavailable

### Video Streaming:
- HLS protocol with adaptive bitrate
- No full download required
- Bunny.net CDN handles distribution
- Progress saved locally (no server calls)

### JavaScript:
- Minimal 300 lines for core functionality
- HLS.js loaded from CDN
- Debounced search (500ms)
- Efficient DOM queries

### CSS:
- 600 lines of organized styles
- No external stylesheets beyond fonts
- CSS custom properties for easy theming
- Hardware-accelerated animations

---

## Future Enhancement Opportunities

1. **Video Preview on Hover**: Auto-play short preview clips
2. **Personalized Homepage**: Show recommendations based on watch history
3. **Watch Lists**: Save videos to watch later
4. **Social Features**: Share videos, see what friends are watching
5. **Advanced Filtering**: Filter by duration, year, rating
6. **Subtitles UI**: Display subtitle/language options
7. **Quality Selection**: Manual bitrate control
8. **Offline Download**: Store videos for airplane mode
9. **Viewing History**: See what you've watched
10. **Dark/Light Mode Toggle**: Theme switcher

---

## Testing Checklist

### Functional
- [ ] Hero section loads and displays featured video
- [ ] Content rows scroll horizontally (mouse, keyboard, touch)
- [ ] Search returns correct results in 500ms
- [ ] Video player loads for logged-in members
- [ ] Paywall displays for non-members
- [ ] Video progress saves and resumes
- [ ] Pagination works on archive page
- [ ] Login/register forms process submissions

### Visual
- [ ] Colors display correctly (test on different monitors)
- [ ] Text is readable on all backgrounds
- [ ] Responsive layout works on mobile/tablet/desktop
- [ ] Hover effects smooth on desktop
- [ ] Animations are smooth (60fps)
- [ ] Images load and display properly

### Performance
- [ ] Page loads in < 3 seconds (desktop)
- [ ] Page loads in < 5 seconds (mobile 4G)
- [ ] Smooth scrolling without jank
- [ ] No console errors or warnings
- [ ] Search completes in 500-1000ms

### Accessibility
- [ ] Can navigate with keyboard only
- [ ] Screen reader announces content properly
- [ ] Color contrast meets WCAG AA standards
- [ ] Focus indicators visible
- [ ] Alt text on all images

### Browser Compatibility
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] iOS Safari
- [ ] Android Chrome

---

## Deployment Checklist

1. **Content Setup**
   - [ ] Create sample videos on Bunny.net
   - [ ] Add featured video to database
   - [ ] Create video categories
   - [ ] Create sample users

2. **Configuration**
   - [ ] Set Bunny.net API keys in admin
   - [ ] Configure Stripe keys (test mode first)
   - [ ] Set up webhook endpoints
   - [ ] Configure domain/SSL

3. **Testing**
   - [ ] Test video playback
   - [ ] Test payment flow
   - [ ] Test user auth
   - [ ] Test mobile experience

4. **Launch**
   - [ ] Switch Stripe to live mode
   - [ ] Monitor error logs
   - [ ] Check analytics
   - [ ] Get user feedback
