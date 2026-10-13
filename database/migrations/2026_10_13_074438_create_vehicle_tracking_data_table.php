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
        Schema::create('vehicleTrackingData', function (Blueprint $table) {
            $table->id();
            $table->string('vehicleId', 50)->index()->comment('Vehicle/asset identifier e.g. BL-08, Q-47898, D-26653');
            $table->string('vehicleName', 100)->comment('Vehicle model or descriptive name');
            $table->string('registrationNumber', 50)->nullable()->comment('License plate or official registration number');
            $table->decimal('latitude', 10, 7)->nullable()->comment('GPS Latitude coordinate');
            $table->decimal('longitude', 10, 7)->nullable()->comment('GPS Longitude coordinate');
            $table->dateTime('recordedAt')->index()->comment('Timestamp of recorded telemetry event');
            $table->decimal('travelledDistance', 8, 2)->default(0.00)->comment('Travelled distance in kilometers (km)');
            $table->decimal('engineHours', 8, 2)->default(0.00)->comment('Engine operating hours (hrs)');
            $table->boolean('ignitionStatus')->default(false)->comment('Ignition status: 1 = ON, 0 = OFF');
            $table->decimal('idlingDuration', 8, 2)->default(0.00)->comment('Idling duration in minutes');
            $table->decimal('speed', 6, 2)->default(0.00)->comment('Current vehicle speed in km/h');
            $table->string('activityType', 50)->default('ignition_off')->index()->comment('Activity state: ignition_off, ignition_on, idling, auxiliary');
            $table->unsignedInteger('messagesReceived')->default(1)->comment('Count of telemetry packets received');
            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();

            // Composite index for fast vehicle time-series queries
            $table->index(['vehicleId', 'recordedAt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicleTrackingData');
    }
};
