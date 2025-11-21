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
        Schema::create('aircraft_type_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aircraft_type_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('aircraft_type_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_type_group_items');
    }
};
