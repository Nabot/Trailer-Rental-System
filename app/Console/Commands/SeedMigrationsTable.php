<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-time fix: when the database has tables but the migrations table is empty
 * (e.g. DB file was copied), this marks all migrations as run EXCEPT the ones
 * that add new columns, so "php artisan migrate" only runs the pending ones.
 */
class SeedMigrationsTable extends Command
{
    protected $signature = 'migrate:seed-table';
    protected $description = 'Mark existing migrations as run so migrate only runs pending ones (fix "table already exists")';

    public function handle(): int
    {
        if (! Schema::hasTable('migrations')) {
            $this->error('Migrations table does not exist. Run: php artisan migrate');
            return 1;
        }

        $migrationsDir = database_path('migrations');
        $files = glob($migrationsDir . '/*.php');
        if ($files === false) {
            $this->error('Could not read migrations directory.');
            return 1;
        }

        // Migrations to leave unrecorded so they will run (add new columns/tables)
        $leavePending = [
            '2026_02_24_145157_add_car_registration_to_customers_table',
            '2026_02_15_100212_add_lost_reason_to_inquiries_table',
        ];

        $names = [];
        foreach ($files as $path) {
            $name = basename($path, '.php');
            if (in_array($name, $leavePending, true)) {
                continue;
            }
            $names[] = $name;
        }
        sort($names);

        $batch = (int) DB::table('migrations')->max('batch') + 1;
        $rows = array_map(fn ($migration) => ['migration' => $migration, 'batch' => $batch], $names);

        foreach ($rows as $row) {
            if (DB::table('migrations')->where('migration', $row['migration'])->exists()) {
                continue;
            }
            DB::table('migrations')->insert($row);
            $this->line('  Recorded: ' . $row['migration']);
        }

        $this->info('Done. Run: php artisan migrate');
        return 0;
    }
}
