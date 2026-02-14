# Setup Commands

## Important: Use the correct directory

Make sure you're in the directory **without** a trailing space:
```bash
cd "/Users/byteable/Documents/APPS/Trailer Renting System"
```

## Installation Steps

### 1. Navigate to project directory
```bash
cd "/Users/byteable/Documents/APPS/Trailer Renting System"
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node dependencies
```bash
npm install
```

### 4. Copy environment file (if not already done)
```bash
cp .env.example .env
```

### 5. Generate application key
```bash
php artisan key:generate
```

### 6. Configure database in .env
Edit `.env` and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trailer_rental
DB_USERNAME=root
DB_PASSWORD=your_password
```

Or use SQLite for development:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

### 7. Run migrations
```bash
php artisan migrate
```

### 8. Seed database
```bash
php artisan db:seed
```

### 9. Build assets
For production:
```bash
npm run build
```

For development (with hot reload):
```bash
npm run dev
```

### 10. Start the server
```bash
php artisan serve
```

Visit: http://localhost:8000

## Default Login Credentials

After seeding:
- **Admin**: admin@trailerrental.com / password
- **Staff**: staff@trailerrental.com / password  
- **Customer**: customer@example.com / password

## Troubleshooting

### If you get "Could not open input file: artisan"
- Make sure you're in the correct directory
- Check: `ls -la artisan` should show the file exists

### If npm can't find package.json
- Verify you're in: `/Users/byteable/Documents/APPS/Trailer Renting System`
- Check: `ls -la package.json` should show the file

### If .env.example is missing
- It should exist in the project root
- If missing, copy from Laravel's default .env.example
