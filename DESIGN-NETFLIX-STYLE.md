# BANTU Plus SVOD - Netflix-Style Design System

## Overview

BANTU Plus implements a professional Netflix-inspired dark theme with responsive layouts, smooth animations, and an intuitive user experience designed for video streaming.

---

## Color Palette

The design uses a carefully selected dark palette optimized for video content display:

```css
--color-background: #0a0e27      /* Deep dark blue background */
--color-surface: #1a1f3a         /* Dark surface for cards and containers */
--color-surface-light: #2d3354   /* Lighter surface for interactive elements */
--color-primary: #e50914         /* Netflix red - primary action color */
--color-accent: #221f1f          /* Dark accent for borders and shadows */
--color-text: #ffffff            /* Primary text color */
--color-text-secondary: #b3b3b3  /* Secondary text for descriptions */
--color-border: #404040          /* Border color */
```

### Color Usage:
- **Primary (#e50914)**: Call-to-action buttons, hover states, important elements
- **Background (#0a0e27)**: Page background, creates immersive dark environment
- **Surface (#1a1f3a)**: Card backgrounds, containers, layered depth
- **Text Secondary (#b3b3b3)**: Descriptions, metadata, secondary information

---

## Typography

### Font Families
- **Primary Font**: System stack (-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto)
- **Font Weights**: 400 (regular), 600 (semibold)

### Heading Sizes
```
H1: clamp(2rem, 5vw, 3.5rem)     /* Hero titles */
H2: clamp(1.5rem, 4vw, 2.5rem)   /* Section headers */
H3: clamp(1.25rem, 3vw, 1.75rem) /* Subsection headers */
H4: 1rem                           /* Card titles */
Body: 1rem / line-height: 1.6     /* Regular text */
```

### Text Properties
- **Line Height**: 1.4-1.6 for readability
- **Letter Spacing**: Standard for English text
- **Text Shadow**: Subtle shadows on hero text for readability over images

---

## Layout & Grid System

### Hero Section
- **Height**: 80vh (full viewport height)
- **Layout**: Flexbox with vertical centering
- **Background**: Featured video image with gradient overlay
- **Content Width**: max-width 500px for text legibility
- **Animations**: Fade-in-up on load (0.8s ease-out)

### Content Rows (Category Sections)
- **Layout**: Horizontal scroll (flex with overflow-x: auto)
- **Spacing**: 1.5rem gap between items
- **Card Size**: 250px width (flex: 0 0 250px)
- **Aspect Ratio**: 16:9 for video thumbnails
- **Scroll Behavior**: smooth with -webkit-overflow-scrolling

### Video Grid (Archive)
- **Layout**: CSS Grid with auto-fill
- **Column**: minmax(250px, 1fr) - responsive columns
- **Gap**: 1.5rem
- **Padding**: 2rem on sides
- **Breakpoints**: Auto-adjusts on all screen sizes

---

## Component Styles

### Hero Section
```
Hero Container:
  - Background: Featured image with 90% left to 70% center to 0% right gradient overlay
  - Height: 80vh
  - Padding: 2rem
  - Position: Relative with overflow hidden
  - Fade overlay at bottom (200px gradient)

Hero Content:
  - Max-width: 500px
  - Animation: fadeInUp 0.8s ease-out
  - Text colors: #ffffff for headings, #b3b3b3 for body

New Release Badge:
  - Background: #e50914
  - Color: #ffffff
  - Padding: 0.5rem 1rem
  - Font-size: 0.75rem
  - Font-weight: 700
  - Text-transform: uppercase
  - Letter-spacing: 2px
  - Border-radius: 4px
```

### Video Cards (Grid View)
```
Card:
  - Background: #1a1f3a
  - Border-radius: 8px
  - Overflow: hidden
  - Aspect-ratio: 16/9
  - Cursor: pointer
  - Transition: transform 0.3s, box-shadow 0.3s

Card:hover:
  - Transform: scale(1.08)
  - Box-shadow: 0 12px 24px rgba(229, 9, 20, 0.5)

Card Image:
  - Width: 100%
  - Height: 100%
  - Object-fit: cover
  - Position: absolute (fills container)

Card Overlay (on hover):
  - Background: Linear gradient (transparent to 95% black)
  - Opacity: 0 → 1 on hover (0.3s transition)
  - Padding: 1.5rem
  - Flexbox: flex-end alignment
  - Z-index: 2

Card Title:
  - Font-size: 1rem
  - Font-weight: 600
  - Color: #ffffff
  - Clamp: 2 lines max (-webkit-line-clamp)
  - Margin: 0 0 0.5rem 0

Card Meta:
  - Font-size: 0.85rem
  - Color: #b3b3b3
  - Margin: 0
```

### Video Cards (Horizontal Scroll)
```
Card Size: 250px × 140px (flex: 0 0 250px)
Aspect Ratio: 16:9
Border-radius: 8px
Hover Effect: scale(1.05) + shadow
Same overlay as grid cards
```

### Buttons

#### Primary Button
```
Background: #e50914
Color: #ffffff
Padding: 0.75rem 2rem
Border-radius: 4px
Font-weight: 600
Font-size: 1rem
Transition: all 0.3s ease

:hover
  Background: #ff1f29
  Box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4)
  Transform: translateY(-2px)
```

#### Secondary Button
```
Background: rgba(255, 255, 255, 0.2)
Color: #ffffff
Border: 1px solid rgba(255, 255, 255, 0.3)
Padding: 0.75rem 2rem
Border-radius: 4px
Font-weight: 600
Font-size: 1rem
Transition: all 0.3s ease

:hover
  Background: rgba(255, 255, 255, 0.3)
  Border-color: rgba(255, 255, 255, 0.5)
```

### Video Player
```
Container:
  - Width: 100%
  - Height: auto
  - Aspect-ratio: 16/9
  - Background: #1a1f3a
  - Border-radius: 8px
  - Overflow: hidden
  - Margin-bottom: 2rem

Video Element:
  - Width: 100%
  - Height: 100%
  - Controls enabled
  - Playsinline for mobile
  - Crossorigin: anonymous
```

### Search Bar
```
Input:
  - Flex: 1 (grows to fill container)
  - Min-width: 250px
  - Padding: 0.75rem 1rem
  - Background: #1a1f3a
  - Border: 1px solid #404040
  - Border-radius: 4px
  - Color: #ffffff
  - Font-size: 1rem

:focus
  - Outline: 2px solid #e50914
  - Outline-offset: 2px
```

### Paywall Section
```
Container:
  - Padding: 4rem 2rem
  - Text-align: center
  - Background: #1a1f3a
  - Margin-bottom: 2rem
  - Border-radius: 8px

Heading:
  - Color: #e50914
  - Margin-bottom: 1rem
  - Font-size: 2rem

Description:
  - Color: #b3b3b3
  - Margin-bottom: 2rem
  - Font-size: 1.1rem

Button Container:
  - Display: flex
  - Gap: 1rem
  - Justify-content: center
  - Flex-wrap: wrap
```

---

## Animations & Transitions

### Fade In Up
Used for hero content and initial page load:
```css
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
```

### Card Hover Scale
```css
transform: scale(1.08);
box-shadow: 0 12px 24px rgba(229, 9, 20, 0.5);
transition: 0.3s ease;
```

### Overlay Fade
```css
opacity: 0 → 1;
transition: opacity 0.3s ease;
```

### Button Hover
```css
transform: translateY(-2px);
box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4);
background-color: lighter shade;
```

---

## Responsive Design

### Breakpoints
The design uses CSS `clamp()` for fluid typography and flexbox/grid for layout flexibility.

### Mobile (< 768px)
- Hero height: 50vh
- Video card size: flex-basis 200px
- Padding: 1rem
- Text size: reduced via clamp()
- Horizontal scroll more prominent
- Search bar full-width

### Tablet (768px - 1024px)
- Hero height: 65vh
- Video card size: flex-basis 220px
- Padding: 1.5rem
- Normal text sizes

### Desktop (> 1024px)
- Hero height: 80vh
- Video card size: 250px
- Padding: 2rem
- Full text sizes via clamp()

---

## Interactive Features

### Smooth Scrolling
- Content rows support horizontal smooth scroll
- Arrow keys navigate left/right (250px increments)
- Touch-friendly with -webkit-overflow-scrolling
- Scroll indicators on desktop

### Lazy Loading
- Images with `data-src` loaded on intersection
- Intersection Observer for performance
- 50px rootMargin for early loading

### Progress Tracking
- Video progress saved to localStorage
- Auto-resume from last position
- Saved every 10 seconds + on pause

### Search & Filter
- Real-time AJAX search with 500ms debounce
- Results displayed in same grid
- Minimum 2 character query

---

## Accessibility

### Keyboard Navigation
- Tab through video cards
- Enter/Space to select card
- Arrow keys for horizontal scroll rows
- Skip links to main content

### Color Contrast
- All text meets WCAG AA standards
- Primary red (#e50914) on white: 4.5:1 ratio
- Secondary text (#b3b3b3) on background: 4.1:1 ratio

### Semantic HTML
- `<main>`, `<header>`, `<section>` semantic elements
- ARIA labels for interactive elements
- Alt text on all images
- Proper heading hierarchy (H1 → H2 → H3)

### Focus Management
- Visible focus indicators on buttons
- Focus trap in modals
- Logical tab order

---

## Performance Optimizations

1. **CSS Grid/Flexbox**: Hardware-accelerated layout
2. **Hardware Acceleration**: `will-change` on hover elements
3. **Lazy Loading**: Images load on demand
4. **Debounced Search**: 500ms delay prevents excessive AJAX calls
5. **LocalStorage Caching**: Progress tracking without database hits
6. **CDN Resources**: HLS.js from CDN (not bundled)

---

## Dark Mode Implementation

The entire design is built as dark-first. No light mode toggle needed currently. All colors use CSS custom properties for easy theme switching in future.

```css
:root {
  --color-background: #0a0e27;
  --color-surface: #1a1f3a;
  /* ... more colors ... */
}
```

---

## Usage Examples

### Hero Section
```html
<?php get_template_part( 'template-parts/home-hero' ); ?>
```

### Content Rows by Category
```html
<?php get_template_part( 'template-parts/content-rows' ); ?>
```

### Video Grid (Archive)
Single video template or archive template automatically handles grid layout.

### Custom Styling
Override any color by updating CSS custom properties in `globals.css` or `bantu-plus.css`.

---

## Browser Support

- **Modern Chrome/Edge/Firefox**: Full support
- **Safari**: Full support including native HLS
- **Mobile Safari/iOS**: Native HLS with fallback
- **IE 11**: Graceful degradation (no Grid, fallback to flexbox)

---

## Future Enhancements

- Light mode toggle with CSS custom properties
- Additional color themes (user preferences)
- Advanced animations on page transitions
- Video preview on hover (short clips)
- Personalized recommendations based on viewing history
- Subtitles and multi-language support UI
- Download option for offline viewing
