<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE stock_transactions MODIFY COLUMN status ENUM('dipinjam', 'dikembalikan', 'dikembalikan_rusak', 'hilang', 'habis') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE stock_transactions MODIFY COLUMN status ENUM('dipinjam', 'dikembalikan', 'habis') NULL");
    }
};
