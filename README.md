# Trailer Rental Management System

A production-ready Laravel 11 application for managing trailer rentals, bookings, payments, inspections, and contracts.

## Tech Stack

- **Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL/PostgreSQL (SQLite for development)
- **Frontend**: Blade + Tailwind CSS
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **PDF Generation**: DomPDF
- **Testing**: Pest PHP
- **Queue**: Database driver

## Features

### ✅ Completed

1. **Database Schema**
   - Complete migrations for all tables
   - Proper relationships and indexes
   - Soft deletes for trailers and customers
   - UUID support for booking/invoice numbers

2. **Models & Relationships**
   - All Eloquent models with relationships
   - Soft deletes where needed
   - Helper methods for business logic
   - Status transition methods for bookings

3. **Authentication & Authorization**
   - Laravel Breeze installed
   - Spatie permissions configured
   - Three roles: Admin, Staff, Customer
   - Permission-based access control

4. **Seeders & Factories**
   - Role and permission seeder
   - User seeder (admin, staff, customer)
   - Trailer seeder (3 initial trailers)
   - Settings seeder
   - Factories for testing

5. **Core Services**
   - BookingService with transaction handling
   - Availability checking with database locks
   - Status transition management

6. **Form Requests**
   - Booking validation rules
   - Authorization checks

### 🚧 In Progress / To Be Completed

1. **Controllers**
   - TrailerController (CRUD)
   - CustomerController (CRUD)
   - BookingController (full workflow)
   - PaymentController
   - InspectionController
   - InvoiceController
   - ReportController

2. **Policies**
   - BookingPolicy
   - CustomerPolicy
   - TrailerPolicy
   - InvoicePolicy

3. **Events & Listeners**
   - BookingConfirmed event
   - BookingCancelled event
   - PaymentReceived event
   - ReturnReminder event

4. **Notifications**
   - Email notifications (Mailable classes)
   - SMS placeholder (for future integration)

5. **PDF Generation**
   - Rental agreement PDF
   - Invoice PDF
   - Receipt PDF
   - E-signature support

6. **UI Pages**
   - Dashboard (admin/staff)
   - Trailer listing & management
   - Customer portal
   - Booking creation/management
   - Calendar view for availability
   - Inspection forms
   - Payment recording
   - Reports page

7. **Tests**
   - Booking overlap prevention tests
   - Cost calculation tests
   - Payment balance tests
   - Status transition tests
   - Authorization tests
   - Utilization computation tests

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL (or SQLite for development)

### Steps

1. **Clone the repository**
   ```bash
   cd "Trailer Renting System"
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=trailer_rental
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   # or for development:
   npm run dev
   ```

7. **Start the server**
   ```bash
   php artisan serve
   ```

### Default Login Credentials

After seeding:
- **Admin**: admin@trailerrental.com / password
- **Staff**: staff@trailerrental.com / password
- **Customer**: customer@example.com / password

## Database Schema

### Core Tables

- `trailers` - Trailer inventory
- `trailer_photos` - Trailer images
- `trailer_documents` - Registration, roadworthy certificates
- `customers` - Customer information
- `customer_documents` - ID copies, proof of address
- `bookings` - Rental bookings
- `booking_addons` - Additional services (delivery, straps, etc.)
- `payments` - Payment records
- `inspections` - Pre-pickup and return inspections
- `inspection_photos` - Inspection images
- `damage_items` - Damage tracking
- `invoices` - Invoices
- `invoice_items` - Invoice line items
- `settings` - System settings (key-value)
- `audit_logs` - Activity tracking

## Initial Trailer Data

The seeder creates three trailers:

1. **Trailer A**: 2.5m Single Axle - N$600/day
2. **Trailer B**: 3.0m Double Axle - N$900/day
3. **Trailer C**: 3.7m Double Axle - N$900/day

## Booking Workflow

1. **Draft** → Created but not confirmed
2. **Pending** → Awaiting confirmation
3. **Confirmed** → Ready for pickup
4. **Active** → Trailer picked up
5. **Returned** → Trailer returned
6. **Cancelled** → Booking cancelled

## Key Features Implementation Notes

### Availability Checking

Bookings prevent double-booking using:
- Database transactions with locks
- Application-level validation
- Date range overlap detection

### Cost Calculation

- Days calculated inclusively (start + end dates)
- Rental cost = days × rate_per_day
- Subtotal includes addons (delivery, straps, damage waiver)
- Balance = total_amount - paid_amount

### Deposit Management

- Can be set per trailer or use global default
- Tracked separately from rental cost
- Used for damage assessment

### Inspections

- Pre-pickup inspection (before rental starts)
- Return inspection (when trailer returned)
- Damage items tracked with cost estimates
- Photos attached to inspections/damages

## Testing

Run tests with Pest:
```bash
php artisan test
# or
./vendor/bin/pest
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/     # To be created
│   ├── Requests/        # Form validation
│   └── Policies/        # Authorization policies
├── Models/              # Eloquent models ✅
├── Services/            # Business logic ✅
├── Events/              # Event classes (to be created)
├── Listeners/           # Event listeners (to be created)
└── Mail/                # Mailable classes (to be created)

database/
├── migrations/          # Database schema ✅
├── seeders/             # Data seeders ✅
└── factories/           # Model factories ✅

resources/
├── views/               # Blade templates (to be created)
└── js/                  # Frontend assets

routes/
└── web.php              # Web routes (to be created)
```

## Next Steps

1. Create controllers for all modules
2. Implement policies for authorization
3. Build Blade views with Tailwind CSS
4. Create PDF templates for contracts/invoices
5. Implement event listeners for notifications
6. Write comprehensive Pest tests
7. Add calendar view for availability
8. Implement reporting dashboard

## License

MIT

## Support

For issues or questions, please create an issue in the repository.
