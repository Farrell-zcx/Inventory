<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat']);
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->string('unit');
            $table->integer('current_stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
