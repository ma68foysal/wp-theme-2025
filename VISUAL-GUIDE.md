# BANTU Plus SVOD - Visual Design Guide

## Homepage Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          BANTU Plus LOGO | ACCOUNT MENU                    │
│                                                                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│                     HERO SECTION - FEATURED VIDEO                           │
│                                                                               │
│   ┌────────────────────────────────────────────────────────────────┐        │
│   │░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│        │
│   │░                Background Image (Latest Video)           ░│        │
│   │░                                                           ░│        │
│   │░    [NEW RELEASE] ─────────────────────────────────────  ░│        │
│   │░                                                           ░│        │
│   │░    Amazing New Documentary Series                       ░│        │
│   │░    Discover the untold stories of our world             ░│        │
│   │░                                                           ░│        │
│   │░    [▶ Play Now]           [ℹ More Info]                 ░│        │
│   │░                                                           ░│        │
│   │░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│        │
│   └────────────────────────────────────────────────────────────┘        │
│                                                                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│   DOCUMENTARIES ──────────────────────────────────────────→                 │
│   [Thumbnail] [Thumbnail] [Thumbnail] [Thumbnail] ...                       │
│   │ on hover: scale up, show title & duration              │               │
│                                                                               │
│   DRAMA SERIES ────────────────────────────────────────────→                │
│   [Thumbnail] [Thumbnail] [Thumbnail] [Thumbnail] ...                       │
│                                                                               │
│   ACTION MOVIES ────────────────────────────────────────────→               │
│   [Thumbnail] [Thumbnail] [Thumbnail] [Thumbnail] ...                       │
│                                                                               │
│   [Browse All Videos] (CTA Button in Center)                                │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Hero Section - Detailed

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│
│ ░ Background Image (80vh height, covers full width)                         ░│
│ ░                    ↓ Gradient Overlay (Dark, left to right)  ↓           ░│
│ ░                                                                 ░░░░░░░░░░│
│ ░                                                                 ░NETFLIX░░│
│ ░                                                                 ░Red     ░│
│ ░     Hero Content Area (Max-width 500px)                       ░Effect  ░│
│ ░     ┌─────────────────────────────────────┐                   ░         ░│
│ ░     │ ┌──────────────────────┐            │                   ░         ░│
│ ░     │ │ NEW RELEASE          │ (Red Badge) │                  ░         ░│
│ ░     │ └──────────────────────┘            │                   ░         ░│
│ ░     │                                     │                   ░         ░│
│ ░     │ Amazing New Documentary             │ (White Text)      ░         ░│
│ ░     │ Series                              │ (Large, Bold)     ░         ░│
│ ░     │                                     │                   ░         ░│
│ ░     │ Discover the untold stories of      │ (Gray Text)       ░         ░│
│ ░     │ our world. A captivating journey   │ (Secondary)       ░         ░│
│ ░     │ that will change how you see life. │                   ░         ░│
│ ░     │                                     │                   ░         ░│
│ ░     │ [▶ Play Now] [ℹ More Info]         │ (Red + Gray Btn)  ░         ░│
│ ░     │                                     │                   ░         ░│
│ ░     └─────────────────────────────────────┘                   ░         ░│
│ ░                                                                 ░░░░░░░░░░│
│ ░ ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓         ░│
│ ░ ┃ Fade Overlay (bottom 200px, dark gradient)               ┃         ░│
│ ░ ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛         ░│
│ ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│
└─────────────────────────────────────────────────────────────────────────────┘
```

## Content Row - Horizontal Scroll

```
DOCUMENTARIES
┌─────────────────────────────────────────────────────────────────────┐ >>>
│ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐              │
│ │        │ │        │ │        │ │        │ │        │              │
│ │┌──────┐│ │┌──────┐│ │┌──────┐│ │┌──────┐│ │┌──────┐│              │
│ ││      ││ ││      ││ ││      ││ ││      ││ ││      ││              │
│ ││ IMG  ││ ││ IMG  ││ ││ IMG  ││ ││ IMG  ││ ││ IMG  ││              │
│ ││      ││ ││      ││ ││      ││ ││      ││ ││      ││              │
│ │└──────┘│ │└──────┘│ │└──────┘│ │└──────┘│ │└──────┘│              │
│ │        │ │        │ │        │ │        │ │        │              │
│ │ Title  │ │ Title  │ │ Title  │ │ Title  │ │ Title  │ (on hover)   │
│ │ 45 min │ │ 52 min │ │ 38 min │ │ 61 min │ │ 43 min │              │
│ │        │ │        │ │        │ │        │ │        │              │
│ └────────┘ └────────┘ └────────┘ └────────┘ └────────┘              │
└─────────────────────────────────────────────────────────────────────┘ >>>
  ← Click arrows or use arrow keys to scroll
  Tap and drag on mobile for horizontal scroll
```

## Video Card - Hover State

```
Without Hover:
┌─────────────┐
│             │
│   IMAGE     │
│             │
│             │
│             │
└─────────────┘

With Hover:
┌─────────────┐
│             │
│   IMAGE     │  (Image stays, but overlay fades in)
│   (Scaled   │
│    1.08x)   │
│             │
│  Box Shadow │
└─────────────┘

On Hover Display Text:
┌─────────────┐
│             │
│   IMAGE     │
│ ═════════   │ (Gradient overlay fades in)
│ Title Here  │ (White text, max 2 lines)
│ 45 min      │ (Gray text)
└─────────────┘
```

## Single Video Player Page

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          BANTU Plus LOGO | ACCOUNT MENU                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│   Amazing Documentary Series                    Category: Documentaries    │
│                                                                               │
│   ┌─────────────────────────────────────────────────────────────────┐      │
│   │                                                                 │      │
│   │                    VIDEO PLAYER (16:9)                         │      │
│   │               HLS.js with Controls                             │      │
│   │                                                                 │      │
│   │            ▶ [════════════════════════] 45:32 / 60:00         │      │
│   │                                                                 │      │
│   │   [  Play  ] [ Vol: 🔊 ] [ Captions ] [ Quality ▼ ] [ ⛶ ]   │      │
│   │                                                                 │      │
│   └─────────────────────────────────────────────────────────────────┘      │
│                                                                               │
│   Membership Status:                                                        │
│   Active Standard Plan • Expires: May 15, 2026 • 31 Days Remaining         │
│                                                                               │
│   [Manage Subscription] [Download] [Share]                                  │
│                                                                               │
│   Description:                                                              │
│   Lorem ipsum dolor sit amet, consectetur adipiscing elit. Discover       │
│   the untold stories...                                                    │
│                                                                               │
│   Related Videos:                                                           │
│   [Thumbnail] [Thumbnail] [Thumbnail] [Thumbnail]                         │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Paywall - Non-Member View

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          BANTU Plus LOGO | ACCOUNT MENU                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│   Amazing Documentary Series                    Category: Documentaries    │
│                                                                               │
│   ┌─────────────────────────────────────────────────────────────────┐      │
│   │                                                                 │      │
│   │     [BLURRED/HIDDEN PLAYER - PAYWALL BLOCKS VIEW]             │      │
│   │                                                                 │      │
│   │               ┌─────────────────────┐                          │      │
│   │               │  PREMIUM CONTENT    │ (Red Title)             │      │
│   │               │                     │                          │      │
│   │               │  This video requires │                          │      │
│   │               │  an active          │                          │      │
│   │               │  membership.        │                          │      │
│   │               │  Subscribe to       │                          │      │
│   │               │  access exclusive   │                          │      │
│   │               │  content.           │                          │      │
│   │               │                     │                          │      │
│   │               │ [Subscribe Now]     │ (Red Button)            │      │
│   │               │ [Manage Sub]        │ (Gray Button)           │      │
│   │               └─────────────────────┘                          │      │
│   │                                                                 │      │
│   └─────────────────────────────────────────────────────────────────┘      │
│                                                                               │
│   (Rest of page visible below paywall)                                     │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Archive Page - Video Grid

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          BANTU Plus LOGO | ACCOUNT MENU                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│   ┌───────────────────────────────────────────────────────────────┐        │
│   │ DOCUMENTARIES                                                 │        │
│   └───────────────────────────────────────────────────────────────┘        │
│                                                                               │
│   ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐        │
│   │                  │  │                  │  │                  │        │
│   │   [Thumbnail]    │  │   [Thumbnail]    │  │   [Thumbnail]    │        │
│   │                  │  │                  │  │                  │        │
│   │                  │  │                  │  │                  │        │
│   │   Title Here...  │  │   Title Here...  │  │   Title Here...  │        │
│   │   45 min         │  │   52 min         │  │   38 min         │        │
│   └──────────────────┘  └──────────────────┘  └──────────────────┘        │
│                                                                               │
│   ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐        │
│   │                  │  │                  │  │                  │        │
│   │   [Thumbnail]    │  │   [Thumbnail]    │  │   [Thumbnail]    │        │
│   │                  │  │                  │  │                  │        │
│   │                  │  │                  │  │                  │        │
│   │   Title Here...  │  │   Title Here...  │  │   Title Here...  │        │
│   │   61 min         │  │   43 min         │  │   50 min         │        │
│   └──────────────────┘  └──────────────────┘  └──────────────────┘        │
│                                                                               │
│   [← Previous]                                              [Next →]        │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Subscription Page

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          BANTU Plus LOGO | ACCOUNT MENU                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│                      CHOOSE YOUR PLAN                                       │
│                                                                               │
│   ┌──────────────────────┐           ┌──────────────────────┐              │
│   │   STANDARD PLAN      │           │  PREMIUM PLAN        │              │
│   │                      │           │                      │              │
│   │   $5.99 / month      │           │   $9.99 / month      │              │
│   │                      │           │                      │              │
│   │ ✓ 1080p HD          │           │ ✓ 4K Ultra HD        │              │
│   │ ✓ Watch on 1 device │           │ ✓ Watch on 4 devices │              │
│   │ ✓ Download videos   │           │ ✓ Priority support   │              │
│   │ ✓ No ads            │           │ ✓ Offline viewing    │              │
│   │                      │           │                      │              │
│   │ 7-day Free Trial     │           │ 7-day Free Trial     │              │
│   │                      │           │                      │              │
│   │ [Subscribe Now]      │           │ [Subscribe Now]      │              │
│   └──────────────────────┘           └──────────────────────┘              │
│                                                                               │
│   * After free trial, charged $5.99/month. Cancel anytime.                 │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Login Page

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          BANTU Plus LOGO | ACCOUNT MENU                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│                          SIGN IN                                            │
│                                                                               │
│                   ┌──────────────────────────┐                              │
│                   │  Email Address           │                              │
│                   │  [                     ] │                              │
│                   │                          │                              │
│                   │  Password                │                              │
│                   │  [                     ] │                              │
│                   │  [ ] Remember me         │                              │
│                   │                          │                              │
│                   │  [Sign In]               │ (Red Button)               │
│                   │                          │                              │
│                   │  Don't have an account?  │                              │
│                   │  [Create one]            │ (Link)                       │
│                   │                          │                              │
│                   └──────────────────────────┘                              │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Color Specifications

### Primary Colors
- **Background**: #0a0e27 (Deep Blue - almost black)
- **Surface**: #1a1f3a (Dark Navy)
- **Primary Action**: #e50914 (Netflix Red)
- **Text**: #ffffff (White)
- **Secondary Text**: #b3b3b3 (Gray)

### Hover States
- Primary Button: #ff1f29 (Lighter Red)
- Cards: Scale 1.08, Shadow: 0 12px 24px rgba(229,9,20,0.5)
- Text Overlay Opacity: 0 → 1 (300ms)

### Gradients
- Hero Overlay: 90deg, rgba(10,14,39,0.9) to transparent
- Bottom Fade: 180deg, transparent to rgba(10,14,39,1)
- Card Overlay: 180deg, transparent to rgba(0,0,0,0.95)

## Typography Sizes

```
H1 (Hero):       2.5rem - 4rem    (responsive)
H2 (Section):    1.5rem - 2.5rem  (responsive)
H3 (Subsection): 1.25rem - 1.75rem (responsive)
Body:            1rem              (16px)
Small:           0.85rem            (14px)
Caption:         0.75rem            (12px)
```

## Spacing Scale

```
4px   → Small gaps, button padding
8px   → Icon spacing, small margins
12px  → Section padding
16px  → Card padding, moderate spacing
24px  → Section margins
32px  → Large section spacing
48px  → Major section breaks
```

## Button Styles

```
Primary Button:
  Background: #e50914
  Text: white
  Padding: 12px 32px
  Border-radius: 4px
  Font-weight: 600
  Hover: #ff1f29, shadow, -2px transform

Secondary Button:
  Background: rgba(255,255,255,0.2)
  Border: 1px rgba(255,255,255,0.3)
  Text: white
  Padding: 12px 32px
  Border-radius: 4px
  Font-weight: 600
  Hover: rgba(255,255,255,0.3)
```

## Mobile Responsive Breakpoints

```
Mobile:    < 640px    Hero 50vh, Single column
Tablet:    640-1024px Hero 65vh, 2-3 columns
Desktop:   > 1024px   Hero 80vh, 4-5 columns
Ultra:     > 1440px   Hero 80vh, 5-6 columns
```

---

This visual guide represents the complete Netflix-style design implemented across all pages. The dark theme with red accents creates a premium, immersive experience for video streaming.
