# Backgrounds

Two source images, each with a 180° flip for seamless cycling.

## Files

| File | Purpose | Cycle Direction |
|------|---------|-----------------|
| `mobile-original.png` | Mobile background | Left → Right |
| `mobile-flipped.png` | Mobile background (180° rotation) | Right → Left |
| `gutter-original.png` | Desktop gutters | Left → Right |
| `gutter-flipped.png` | Desktop gutters (180° rotation) | Right → Left |

## Usage

### Mobile
- Single full-width background
- Fades between `mobile-original.png` ↔ `mobile-flipped.png`
- Creates seamless left-to-right cycling effect

### Desktop Gutters
- Left and right side columns (outside main content)
- Fades between `gutter-original.png` ↔ `gutter-flipped.png`
- Synchronized cycling left-to-right and back

## CSS Implementation

```css
/* Mobile: crossfade between original and flipped */
@keyframes bg-cycle-mobile {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}

/* Gutter: synchronized fade */
.gutter-left { background-image: url('./backgrounds/gutter-original.png'); }
.gutter-right { background-image: url('./backgrounds/gutter-flipped.png'); }
```

## Regenerating Flips

If you replace the original images:

```bash
cd backgrounds
sips -r 180 mobile-original.png --out mobile-flipped.png
sips -r 180 gutter-original.png --out gutter-flipped.png
```
