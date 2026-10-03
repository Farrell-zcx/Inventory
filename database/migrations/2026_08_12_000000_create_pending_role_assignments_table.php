<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pending_role_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('role');
            $table->integer('assigned_by');
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_role_assignments');
    }
};
