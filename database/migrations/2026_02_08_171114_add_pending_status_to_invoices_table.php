<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with the new enum value
        if (DB::getDriverName() === 'sqlite') {
            // Create temporary table with new enum
            DB::statement("
                CREATE TABLE invoices_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    invoice_number TEXT NOT NULL UNIQUE,
                    booking_id INTEGER NOT NULL,
                    customer_id INTEGER NOT NULL,
                    type TEXT NOT NULL DEFAULT 'rental' CHECK(type IN ('rental', 'damage', 'other')),
                    invoice_date DATE NOT NULL,
                    due_date DATE,
                    subtotal DECIMAL(10,2) NOT NULL,
                    tax DECIMAL(10,2) NOT NULL DEFAULT 0,
                    total_amount DECIMAL(10,2) NOT NULL,
                    paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
                    balance DECIMAL(10,2) NOT NULL,
                    status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('draft', 'pending', 'sent', 'paid', 'overdue', 'cancelled')),
                    notes TEXT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
                    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
                )
            ");

            // Copy data
            DB::statement("
                INSERT INTO invoices_new 
                SELECT * FROM invoices
            ");

            // Drop old table
            Schema::drop('invoices');

            // Rename new table
            DB::statement("ALTER TABLE invoices_new RENAME TO invoices");

            // Recreate indexes
            DB::statement("CREATE INDEX invoices_invoice_number_index ON invoices(invoice_number)");
            DB::statement("CREATE INDEX invoices_booking_id_index ON invoices(booking_id)");
            DB::statement("CREATE INDEX invoices_customer_id_index ON invoices(customer_id)");
            DB::statement("CREATE INDEX invoices_status_index ON invoices(status)");
        } else {
            // For other databases, use ALTER TABLE
            Schema::table('invoices', function (Blueprint $table) {
                $table->enum('status', ['draft', 'pending', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Recreate table without 'pending'
            DB::statement("
                CREATE TABLE invoices_old (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    invoice_number TEXT NOT NULL UNIQUE,
                    booking_id INTEGER NOT NULL,
                    customer_id INTEGER NOT NULL,
                    type TEXT NOT NULL DEFAULT 'rental' CHECK(type IN ('rental', 'damage', 'other')),
                    invoice_date DATE NOT NULL,
                    due_date DATE,
                    subtotal DECIMAL(10,2) NOT NULL,
                    tax DECIMAL(10,2) NOT NULL DEFAULT 0,
                    total_amount DECIMAL(10,2) NOT NULL,
                    paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
                    balance DECIMAL(10,2) NOT NULL,
                    status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('draft', 'sent', 'paid', 'overdue', 'cancelled')),
                    notes TEXT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
                    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
                )
            ");

            // Copy data, converting 'pending' to 'draft'
            DB::statement("
                INSERT INTO invoices_old 
                SELECT id, invoice_number, booking_id, customer_id, type, invoice_date, due_date, 
                       subtotal, tax, total_amount, paid_amount, balance,
                       CASE WHEN status = 'pending' THEN 'draft' ELSE status END as status,
                       notes, created_at, updated_at
                FROM invoices
            ");

            Schema::drop('invoices');
            DB::statement("ALTER TABLE invoices_old RENAME TO invoices");

            DB::statement("CREATE INDEX invoices_invoice_number_index ON invoices(invoice_number)");
            DB::statement("CREATE INDEX invoices_booking_id_index ON invoices(booking_id)");
            DB::statement("CREATE INDEX invoices_customer_id_index ON invoices(customer_id)");
            DB::statement("CREATE INDEX invoices_status_index ON invoices(status)");
        } else {
            Schema::table('invoices', function (Blueprint $table) {
                $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft')->change();
            });
        }
    }
};
