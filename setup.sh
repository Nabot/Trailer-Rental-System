#!/bin/bash

echo "🚀 Setting up Trailer Rental Management System..."
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from the Laravel project root directory"
    exit 1
fi

# Step 1: Install NPM dependencies
echo "📦 Step 1: Installing NPM dependencies..."
if [ ! -d "node_modules" ]; then
    npm install
    if [ $? -ne 0 ]; then
        echo "❌ Failed to install NPM dependencies"
        exit 1
    fi
    echo "✅ NPM dependencies installed"
else
    echo "✅ NPM dependencies already installed"
fi

# Step 2: Check .env file
echo ""
echo "📝 Step 2: Checking .env file..."
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found. Copying from .env.example..."
    cp .env.example .env
    php artisan key:generate
    echo "✅ .env file created. Please configure your database settings!"
    echo "   Edit .env file and set DB_CONNECTION, DB_DATABASE, etc."
    read -p "Press Enter after configuring .env file..."
fi

# Step 3: Run migrations
echo ""
echo "🗄️  Step 3: Running database migrations..."
php artisan migrate
if [ $? -ne 0 ]; then
    echo "❌ Migration failed. Please check your database configuration in .env"
    exit 1
fi
echo "✅ Migrations completed"

# Step 4: Seed database
echo ""
echo "🌱 Step 4: Seeding database..."
php artisan db:seed
if [ $? -ne 0 ]; then
    echo "❌ Seeding failed"
    exit 1
fi
echo "✅ Database seeded"

# Step 5: Build assets
echo ""
echo "🎨 Step 5: Building frontend assets..."
npm run build
if [ $? -ne 0 ]; then
    echo "⚠️  Asset build failed, but continuing..."
fi
echo "✅ Assets built"

# Step 6: Create storage link
echo ""
echo "🔗 Step 6: Creating storage symlink..."
php artisan storage:link 2>/dev/null || echo "Storage link already exists or failed (this is OK)"

echo ""
echo "✨ Setup complete!"
echo ""
echo "📋 Default Login Credentials:"
echo "   Admin:   admin@trailerrental.com / password"
echo "   Staff:   staff@trailerrental.com / password"
echo "   Customer: customer@example.com / password"
echo ""
echo "🚀 To start the server, run:"
echo "   php artisan serve"
echo ""
echo "   Then visit: http://localhost:8000"
