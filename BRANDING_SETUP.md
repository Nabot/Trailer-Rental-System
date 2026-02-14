# IronAxle Rentals Branding Setup

## Logo Installation

To complete the branding setup, please place your IronAxle Rentals logo file in one of the following locations:

1. **Preferred:** `public/images/ironaxle-logo.png` (PNG format with transparency)
2. **Alternative:** `public/images/ironaxle-logo.svg` (SVG format)

### Logo Specifications

- **Recommended size:** 200-300px width (height will scale proportionally)
- **Format:** PNG (with transparency) or SVG
- **Background:** Transparent preferred
- **Display sizes:**
  - Navigation bar: h-10 (40px height)
  - Login/Register screens: h-24 (96px height) - prominently displayed
  - Favicon: Automatically used from logo file

### Current Status

The application is fully configured to use the IronAxle Rentals logo. Until the logo file is uploaded, a text-based fallback will be displayed showing "IRONAXLE RENTALS" with orange accent colors matching the brand.

## Brand Colors

The application uses the following color scheme based on the IronAxle Rentals logo:

- **Primary Accent:** Orange (#EA580C / orange-600)
- **Primary Hover:** Dark Orange (#C2410C / orange-700)
- **Text Primary:** Dark Gray/Black
- **Text Secondary:** Medium Gray
- **Background:** White (light mode) / Dark Gray (dark mode)

### Color Usage

- **Primary buttons:** Orange background (orange-600)
- **Links and accents:** Orange text (orange-600)
- **Form focus states:** Orange borders and rings (orange-500)
- **Active navigation:** Orange underline/border (orange-400/600)

## Where Branding Appears

1. **Login/Register Screens:** Large logo prominently displayed at the top with company name
2. **Navigation Bar:** Logo displayed in the top-left corner (40px height)
3. **Browser Tab:** Application name and favicon
4. **PDF Documents:** Company name appears on invoices and receipts
5. **Email Templates:** Company name used in email communications
6. **All Primary Actions:** Orange buttons and links throughout the application

## Implementation Details

### Updated Components

- ✅ Application logo component (`resources/views/components/application-logo.blade.php`)
- ✅ Guest layout for login/register screens (`resources/views/layouts/guest.blade.php`)
- ✅ Primary button component (orange styling)
- ✅ Form input components (orange focus states)
- ✅ Navigation links (orange active states)
- ✅ All primary action buttons throughout the application

### Files Updated

- Navigation bar logo display
- Login screen with prominent logo
- Register screen branding
- All form components
- Primary/secondary buttons
- Navigation active states
- Customer report avatars
- Dashboard action buttons
- Calendar action buttons
- Invoice action buttons
- Booking action buttons

## Next Steps

1. **Upload your logo file:**
   - Place `ironaxle-logo.png` or `ironaxle-logo.svg` in `public/images/`
   - Ensure the file has a transparent background

2. **Clear the application cache:**
   ```bash
   php artisan optimize:clear
   php artisan view:clear
   ```

3. **Refresh your browser** to see the logo

4. **Verify branding:**
   - Check login screen for prominent logo display
   - Verify navigation bar logo
   - Confirm orange accent colors throughout the UI
   - Test PDF generation (invoices/receipts) for company name

## Customization

### Logo Size Adjustments

- **Navigation bar:** Edit `resources/views/layouts/navigation.blade.php` and modify `h-10` class
- **Login screen:** Edit `resources/views/layouts/guest.blade.php` and modify `h-24` class

### Color Customization

To adjust orange shades, update Tailwind classes throughout:
- `orange-600` → Primary color
- `orange-700` → Hover states
- `orange-500` → Focus rings
- `orange-400` → Active navigation

## Brand Consistency

The application now consistently uses:
- ✅ Orange as the primary accent color
- ✅ IronAxle Rentals branding throughout
- ✅ Professional, industrial aesthetic
- ✅ Consistent logo placement and sizing
