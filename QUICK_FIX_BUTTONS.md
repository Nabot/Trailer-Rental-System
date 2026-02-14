# Quick Fix: Buttons Visibility Issue

## Current Status
The buttons ARE in the code in **TWO locations**:

1. **Header** (top-right): "Add Lead" / "Create Quote" buttons
2. **Main Content** (below header): Large "➕ Add Lead" / "➕ Create Quote" buttons

## If You Still Can't See Buttons

### Step 1: Verify Pages Load
Visit these URLs directly:
- `http://localhost:8000/inquiries` (or your app URL)
- `http://localhost:8000/quotes`

**Expected**: You should see:
- Page title "Leads / Inquiries" or "Quotes"
- A large orange button saying "➕ Add Lead" or "➕ Create Quote" right below the header
- Filters section below that

### Step 2: Check for Errors
1. Open browser console (F12)
2. Visit `/inquiries` page
3. Look for any red errors in console
4. Check Network tab for failed requests (status 500, 404, etc.)

### Step 3: Verify Database
```bash
php artisan migrate
```

### Step 4: Clear All Caches
```bash
php artisan optimize:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

### Step 5: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```
Then visit the pages and watch for errors

## Alternative Access

### From Dashboard:
1. Go to Dashboard (`/dashboard`)
2. Scroll to "Quick Actions" section (left column)
3. Click "Add Lead" or "Create Quote" buttons

### Direct URLs:
- Create Lead: `/inquiries/create`
- Create Quote: `/quotes/create`

## What Should Be Visible

### On `/inquiries` page:
- **Header**: "Leads / Inquiries" title + "Add Lead" button (top-right)
- **Main Content**: Large "➕ Add Lead" button (top of content area, right-aligned)
- **Empty State**: "Add Lead" button if no leads exist

### On `/quotes` page:
- **Header**: "Quotes" title + "Create Quote" button (top-right)
- **Main Content**: Large "➕ Create Quote" button (top of content area, right-aligned)
- **Empty State**: "Create Quote" button if no quotes exist

## If Nothing Works

Please check:
1. Are the pages loading at all? (Do you see the page title?)
2. What do you see when you visit `/inquiries`?
3. What do you see when you visit `/quotes`?
4. Any errors in browser console?
5. Any errors in Laravel logs?
