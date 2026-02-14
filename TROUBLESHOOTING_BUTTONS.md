# Troubleshooting: Buttons Not Visible

## Quick Checks

1. **Are you on the correct pages?**
   - Navigate to: `/inquiries` - Should show "Add Lead" button in header
   - Navigate to: `/quotes` - Should show "Create Quote" button in header

2. **Check Browser Console (F12)**
   - Look for any JavaScript errors
   - Check Network tab for failed requests

3. **Check Laravel Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Then visit `/inquiries` and `/quotes` to see if errors appear

4. **Verify Routes Work**
   ```bash
   php artisan route:list | grep inquiries
   php artisan route:list | grep quotes
   ```

5. **Direct URL Test**
   Try accessing these URLs directly:
   - `http://your-app-url/inquiries/create`
   - `http://your-app-url/quotes/create`

## Alternative Access Methods

### Method 1: Dashboard Quick Actions
- Go to Dashboard
- Look for "Quick Actions" section (left column)
- You should see "Add Lead" and "Create Quote" buttons there

### Method 2: Navigation Menu
- Click "Leads" in navigation → Page loads → Button should be in header (top-right)
- Click "Quotes" in navigation → Page loads → Button should be in header (top-right)

### Method 3: Empty State
- If no leads/quotes exist, there's an "Add Lead" / "Create Quote" button in the empty state message

## If Pages Don't Load

Check for these common issues:

1. **Database Tables Missing**
   ```bash
   php artisan migrate
   ```

2. **View Cache**
   ```bash
   php artisan view:clear
   php artisan optimize:clear
   ```

3. **Model Issues**
   - Check if `Inquiry` and `Quote` models exist
   - Verify namespace is correct

4. **Route Cache**
   ```bash
   php artisan route:clear
   ```

## Expected Button Locations

### Leads Page (`/inquiries`)
- **Header (top-right)**: Orange "Add Lead" button
- **Empty State**: "Add Lead" button if no leads exist
- **Dashboard**: "Add Lead" button in Quick Actions

### Quotes Page (`/quotes`)
- **Header (top-right)**: Orange "Create Quote" button  
- **Empty State**: "Create Quote" button if no quotes exist
- **Dashboard**: "Create Quote" button in Quick Actions

## CSS/Display Issues

If buttons exist but aren't visible:

1. Check if CSS is loading: View page source, look for `<link>` tags
2. Check browser zoom level
3. Try different browser
4. Check if buttons are hidden by CSS (inspect element)

## Debug Steps

1. **View Source**: Right-click → View Page Source
   - Search for "Add Lead" or "Create Quote"
   - If found: CSS/display issue
   - If not found: View rendering issue

2. **Inspect Element**: Right-click where button should be → Inspect
   - Check if element exists in DOM
   - Check computed styles
   - Check for `display: none` or `visibility: hidden`

3. **Check Laravel Errors**
   ```bash
   php artisan serve
   ```
   Visit pages and check terminal for errors
