<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateHistoricalIoTData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'iot:generate-history {--days=10 : Number of past days to generate}';

    /**
     * The console command description.
     */
    protected $description = 'Generate realistic historical IoT telemetry records for the last 10 calendar days without duplicates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysCount = (int)$this->option('days') ?: 10;
        $this->info("Generating historical IoT telemetry data for the last {$daysCount} days (including today)...");

        $vehicles = [
            [
                'id' => 'BL-08',
                'name' => 'Backhoe Loader BL-08',
                'registration' => 'DXB-84920-E',
                'baseLat' => 25.0657000,
                'baseLng' => 55.1713000,
            ],
            [
                'id' => 'Q-47898',
                'name' => 'Compact Wheel Loader Q-47898',
                'registration' => 'AUH-47898-B',
                'baseLat' => 25.1124000,
                'baseLng' => 55.2045000,
            ],
            [
                'id' => 'D-26653',
                'name' => 'Heavy Wheel Loader D-26653',
                'registration' => 'SHJ-26653-C',
                'baseLat' => 25.1834000,
                'baseLng' => 55.2678000,
            ],
        ];

        $now = Carbon::now();
        $totalInserted = 0;
        $totalSkipped = 0;

        // Daily activity templates across morning, noon, afternoon, evening
        $timeSlots = [
            ['time' => '08:15:00', 'type' => 'ignition_on', 'speed' => 16.5, 'dist' => 3.2, 'idle' => 0.0, 'eng' => 0.75, 'ignition' => true],
            ['time' => '10:30:00', 'type' => 'idling',      'speed' => 0.0,  'dist' => 0.0, 'idle' => 25.0, 'eng' => 0.42, 'ignition' => true],
            ['time' => '11:45:00', 'type' => 'ignition_on', 'speed' => 22.0, 'dist' => 5.8, 'idle' => 0.0, 'eng' => 1.25, 'ignition' => true],
            ['time' => '13:00:00', 'type' => 'ignition_off','speed' => 0.0,  'dist' => 0.0, 'idle' => 0.0, 'eng' => 0.0,  'ignition' => false],
            ['time' => '14:20:00', 'type' => 'ignition_on', 'speed' => 19.5, 'dist' => 4.5, 'idle' => 0.0, 'eng' => 1.10, 'ignition' => true],
            ['time' => '15:50:00', 'type' => 'idling',      'speed' => 0.0,  'dist' => 0.0, 'idle' => 18.0, 'eng' => 0.30, 'ignition' => true],
            ['time' => '17:10:00', 'type' => 'ignition_on', 'speed' => 15.0, 'dist' => 2.9, 'idle' => 0.0, 'eng' => 0.65, 'ignition' => true],
            ['time' => '18:30:00', 'type' => 'ignition_off','speed' => 0.0,  'dist' => 0.0, 'idle' => 0.0, 'eng' => 0.0,  'ignition' => false],
        ];

        // Loop over the last $daysCount days (e.g. today down to 9 days ago)
        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $targetDate = Carbon::today()->subDays($i);
            $dateStr = $targetDate->format('Y-m-d');

            foreach ($vehicles as $vehicle) {
                // Prevent duplicate historical records: Check if records for this vehicle and date already exist
                $exists = DB::table('vehicleTrackingData')
                    ->where('vehicleId', $vehicle['id'])
                    ->whereDate('recordedAt', $dateStr)
                    ->exists();

                if ($exists) {
                    $totalSkipped++;
                    continue;
                }

                $recordsToInsert = [];
                $cumulativeOdometer = 120.0 + ($daysCount - $i) * 4.5;

                foreach ($timeSlots as $slot) {
                    $recordedAt = "{$dateStr} {$slot['time']}";

                    // If slot is in the future for today, skip future hours
                    if ($targetDate->isToday() && Carbon::parse($recordedAt)->isFuture()) {
                        continue;
                    }

                    $isMoving = ($slot['type'] === 'ignition_on');
                    $latOffset = $isMoving ? (mt_rand(-80, 80) / 100000) : 0;
                    $lngOffset = $isMoving ? (mt_rand(-80, 80) / 100000) : 0;

                    $recordsToInsert[] = [
                        'vehicleId'          => $vehicle['id'],
                        'vehicleName'        => $vehicle['name'],
                        'registrationNumber' => $vehicle['registration'],
                        'latitude'           => round($vehicle['baseLat'] + $latOffset, 7),
                        'longitude'          => round($vehicle['baseLng'] + $lngOffset, 7),
                        'recordedAt'         => $recordedAt,
                        'travelledDistance'  => $slot['dist'],
                        'engineHours'        => round($cumulativeOdometer + $slot['eng'], 2),
                        'ignitionStatus'     => $slot['ignition'],
                        'idlingDuration'     => $slot['idle'],
                        'speed'              => $slot['speed'],
                        'activityType'       => $slot['type'],
                        'messagesReceived'   => mt_rand(12, 45),
                        'createdAt'          => $now,
                        'updatedAt'          => $now,
                    ];
                }

                if (!empty($recordsToInsert)) {
                    DB::table('vehicleTrackingData')->insert($recordsToInsert);
                    $totalInserted += count($recordsToInsert);
                }
            }
        }

        $this->info("Completed: {$totalInserted} records inserted, {$totalSkipped} vehicle-date slots skipped (already populated).");
        return self::SUCCESS;
    }
}

