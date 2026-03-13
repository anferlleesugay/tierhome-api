<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_properties', function (Blueprint $table) {
            $table->id();

            // Landlord who owns this property
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Basic Info
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('barangay')->nullable();

            // Pricing
            $table->decimal('monthly_rent', 10, 2);
            $table->decimal('deposit', 10, 2)->nullable();

            // Property Details
            $table->enum('type', ['apartment', 'house', 'room', 'condo', 'studio']);
            $table->integer('bedrooms')->default(1);
            $table->integer('bathrooms')->default(1);
            $table->decimal('area_sqm', 8, 2)->nullable();

            // Amenities (for ranking score)
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_aircon')->default(false);
            $table->boolean('has_water')->default(false);
            $table->boolean('has_electricity')->default(false);
            $table->boolean('is_pet_friendly')->default(false);
            $table->boolean('is_furnished')->default(false);

            // Availability
            $table->boolean('is_available')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_properties');
    }
};