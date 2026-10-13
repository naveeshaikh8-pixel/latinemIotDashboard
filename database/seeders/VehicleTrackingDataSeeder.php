<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleTrackingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetVehicleIds = ['BL-08', 'Q-47898', 'D-26653'];

        // Safety: Idempotent execution - removes only previous dummy records for these 3 vehicles
        // without touching other tables or data in the database.
        DB::table('vehicleTrackingData')->whereIn('vehicleId', $targetVehicleIds)->delete();

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

        $records = [];
        $now = Carbon::now();

        // 1. INTRADAY TIMELINE TELEMETRY RECORDS (Supports Day Overview timeline and live stats)
        $timelineDay = Carbon::create(2026, 3, 23, 0, 0, 0);

        // Define activities per vehicle corresponding to Screenshot 1 timeline
        $vehicleActivities = [
            'BL-08' => [
                ['start' => '10:35', 'end' => '11:34', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '11:34', 'end' => '11:54', 'type' => 'ignition_on', 'speed' => 19.5, 'ignition' => true],
                ['start' => '11:54', 'end' => '12:08', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '12:08', 'end' => '12:20', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
                ['start' => '12:20', 'end' => '15:20', 'type' => 'ignition_on', 'speed' => 24.0, 'ignition' => true],
                ['start' => '15:20', 'end' => '15:25', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
                ['start' => '15:25', 'end' => '15:42', 'type' => 'ignition_on', 'speed' => 16.0, 'ignition' => true],
                ['start' => '15:42', 'end' => '16:02', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '16:02', 'end' => '16:24', 'type' => 'ignition_on', 'speed' => 18.2, 'ignition' => true],
                ['start' => '16:24', 'end' => '16:50', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
                ['start' => '16:50', 'end' => '17:08', 'type' => 'ignition_on', 'speed' => 14.5, 'ignition' => true],
                ['start' => '17:08', 'end' => '18:15', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
            ],
            'Q-47898' => [
                ['start' => '10:35', 'end' => '12:35', 'type' => 'ignition_on', 'speed' => 21.0, 'ignition' => true],
                ['start' => '12:35', 'end' => '12:40', 'type' => 'auxiliary', 'speed' => 0.0, 'ignition' => true],
                ['start' => '12:40', 'end' => '13:25', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
                ['start' => '13:25', 'end' => '15:20', 'type' => 'ignition_on', 'speed' => 22.5, 'ignition' => true],
                ['start' => '15:20', 'end' => '15:25', 'type' => 'auxiliary', 'speed' => 0.0, 'ignition' => true],
                ['start' => '15:25', 'end' => '16:15', 'type' => 'ignition_on', 'speed' => 20.0, 'ignition' => true],
                ['start' => '16:15', 'end' => '16:20', 'type' => 'auxiliary', 'speed' => 0.0, 'ignition' => true],
                ['start' => '16:20', 'end' => '17:40', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
                ['start' => '17:40', 'end' => '17:58', 'type' => 'ignition_on', 'speed' => 17.0, 'ignition' => true],
                ['start' => '17:58', 'end' => '18:15', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
            ],
            'D-26653' => [
                ['start' => '10:35', 'end' => '10:55', 'type' => 'ignition_on', 'speed' => 15.0, 'ignition' => true],
                ['start' => '10:55', 'end' => '12:35', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '12:35', 'end' => '12:39', 'type' => 'ignition_on', 'speed' => 12.0, 'ignition' => true],
                ['start' => '12:39', 'end' => '13:19', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '13:19', 'end' => '13:24', 'type' => 'ignition_on', 'speed' => 14.0, 'ignition' => true],
                ['start' => '13:24', 'end' => '13:35', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
                ['start' => '13:35', 'end' => '13:50', 'type' => 'ignition_on', 'speed' => 18.0, 'ignition' => true],
                ['start' => '13:50', 'end' => '14:15', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '14:15', 'end' => '14:40', 'type' => 'ignition_on', 'speed' => 20.0, 'ignition' => true],
                ['start' => '14:40', 'end' => '14:50', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '14:50', 'end' => '15:45', 'type' => 'ignition_on', 'speed' => 22.0, 'ignition' => true],
                ['start' => '15:45', 'end' => '16:02', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '16:02', 'end' => '16:12', 'type' => 'ignition_on', 'speed' => 16.0, 'ignition' => true],
                ['start' => '16:12', 'end' => '16:20', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
                ['start' => '16:20', 'end' => '16:35', 'type' => 'ignition_on', 'speed' => 15.0, 'ignition' => true],
                ['start' => '16:35', 'end' => '16:55', 'type' => 'idling', 'speed' => 0.0, 'ignition' => true],
                ['start' => '16:55', 'end' => '17:28', 'type' => 'ignition_on', 'speed' => 19.0, 'ignition' => true],
                ['start' => '17:28', 'end' => '18:15', 'type' => 'ignition_off', 'speed' => 0.0, 'ignition' => false],
            ],
        ];

        foreach ($vehicles as $vehicle) {
            $vId = $vehicle['id'];
            $acts = $vehicleActivities[$vId] ?? [];
            $accumulatedDist = 0.0;
            $accumulatedEngineHours = 100.0;

            foreach ($acts as $act) {
                [$startH, $startM] = explode(':', $act['start']);
                [$endH, $endM] = explode(':', $act['end']);

                $startTime = $timelineDay->copy()->setTime((int)$startH, (int)$startM);
                $endTime = $timelineDay->copy()->setTime((int)$endH, (int)$endM);
                $durationMins = $startTime->diffInMinutes($endTime);

                $stepMins = max(5, min(15, (int)$durationMins));
                $currentTime = $startTime->copy();

                while ($currentTime <= $endTime) {
                    $isMoving = ($act['type'] === 'ignition_on' && $act['speed'] > 0);
                    $distDelta = $isMoving ? round(($act['speed'] * ($stepMins / 60)), 2) : 0.00;
                    $accumulatedDist += $distDelta;

                    if ($act['ignition']) {
                        $accumulatedEngineHours += round($stepMins / 60, 2);
                    }

                    $latOffset = $isMoving ? (mt_rand(-50, 50) / 100000) : 0;
                    $lngOffset = $isMoving ? (mt_rand(-50, 50) / 100000) : 0;

                    $records[] = [
                        'vehicleId' => $vId,
                        'vehicleName' => $vehicle['name'],
                        'registrationNumber' => $vehicle['registration'],
                        'latitude' => round($vehicle['baseLat'] + $latOffset, 7),
                        'longitude' => round($vehicle['baseLng'] + $lngOffset, 7),
                        'recordedAt' => $currentTime->format('Y-m-d H:i:s'),
                        'travelledDistance' => round($distDelta, 2),
                        'engineHours' => round($accumulatedEngineHours, 2),
                        'ignitionStatus' => $act['ignition'],
                        'idlingDuration' => $act['type'] === 'idling' ? $stepMins : 0.00,
                        'speed' => $act['speed'],
                        'activityType' => $act['type'],
                        'messagesReceived' => mt_rand(8, 25),
                        'createdAt' => $now,
                        'updatedAt' => $now,
                    ];

                    $currentTime->addMinutes($stepMins);
                }
            }
        }

        // 2. DAILY HISTORICAL TELEMETRY RECORDS (Supports 20 Mar - 4 Apr Chart.js series)
        $dailyMetrics = [
            '2026-03-20' => ['distCurr' => 12.0, 'engIgn' => 1.2,  'engIdle' => 11.2, 'msgs' => 2400],
            '2026-03-21' => ['distCurr' => 18.5, 'engIgn' => 17.5, 'engIdle' => 12.8, 'msgs' => 2700],
            '2026-03-22' => ['distCurr' => 13.5, 'engIgn' => 16.5, 'engIdle' => 9.2,  'msgs' => 2900],
            '2026-03-23' => ['distCurr' => 29.0, 'engIgn' => 15.2, 'engIdle' => 10.8, 'msgs' => 3500],
            '2026-03-24' => ['distCurr' => 28.0, 'engIgn' => 11.2, 'engIdle' => 9.8,  'msgs' => 3700],
            '2026-03-25' => ['distCurr' => 27.5, 'engIgn' => 21.5, 'engIdle' => 8.8,  'msgs' => 3800],
            '2026-03-26' => ['distCurr' => 25.0, 'engIgn' => 14.8, 'engIdle' => 10.0, 'msgs' => 4300],
            '2026-03-27' => ['distCurr' => 23.0, 'engIgn' => 20.2, 'engIdle' => 11.0, 'msgs' => 3200],
            '2026-03-28' => ['distCurr' => 0.0,  'engIgn' => 16.8, 'engIdle' => 10.5, 'msgs' => 3500],
            '2026-03-29' => ['distCurr' => 0.0,  'engIgn' => 4.0,  'engIdle' => 15.2, 'msgs' => 3350],
            '2026-03-30' => ['distCurr' => 12.0, 'engIgn' => 0.0,  'engIdle' => 0.0,  'msgs' => 3200],
            '2026-03-31' => ['distCurr' => 15.0, 'engIgn' => 0.0,  'engIdle' => 0.0,  'msgs' => 60],
            '2026-04-01' => ['distCurr' => 26.5, 'engIgn' => 3.8,  'engIdle' => 0.0,  'msgs' => 80],
            '2026-04-02' => ['distCurr' => 23.5, 'engIgn' => 13.5, 'engIdle' => 11.0, 'msgs' => 950],
            '2026-04-03' => ['distCurr' => 20.0, 'engIgn' => 15.5, 'engIdle' => 22.0, 'msgs' => 4250],
            '2026-04-04' => ['distCurr' => 18.0, 'engIgn' => 15.5, 'engIdle' => 19.5, 'msgs' => 3900],
        ];

        foreach ($dailyMetrics as $dateStr => $metrics) {
            foreach ($vehicles as $vIdx => $vehicle) {
                $weight = ($vIdx === 0) ? 0.45 : (($vIdx === 1) ? 0.30 : 0.25);
                $vDist = round($metrics['distCurr'] * $weight, 2);
                $vIgnHours = round($metrics['engIgn'] * $weight, 2);
                $vIdleHours = round($metrics['engIdle'] * $weight, 2);
                $vMsgs = (int)round($metrics['msgs'] * $weight);

                // Morning active session record
                $records[] = [
                    'vehicleId' => $vehicle['id'],
                    'vehicleName' => $vehicle['name'],
                    'registrationNumber' => $vehicle['registration'],
                    'latitude' => $vehicle['baseLat'],
                    'longitude' => $vehicle['baseLng'],
                    'recordedAt' => "{$dateStr} 09:00:00",
                    'travelledDistance' => $vDist,
                    'engineHours' => $vIgnHours,
                    'ignitionStatus' => ($vIgnHours > 0),
                    'idlingDuration' => 0.00,
                    'speed' => ($vDist > 0) ? 22.5 : 0.0,
                    'activityType' => ($vDist > 0) ? 'ignition_on' : 'ignition_off',
                    'messagesReceived' => (int)($vMsgs * 0.6),
                    'createdAt' => $now,
                    'updatedAt' => $now,
                ];

                // Afternoon session
                if ($vIdleHours > 0 || $vDist == 0) {
                    $records[] = [
                        'vehicleId' => $vehicle['id'],
                        'vehicleName' => $vehicle['name'],
                        'registrationNumber' => $vehicle['registration'],
                        'latitude' => $vehicle['baseLat'],
                        'longitude' => $vehicle['baseLng'],
                        'recordedAt' => "{$dateStr} 14:30:00",
                        'travelledDistance' => 0.00,
                        'engineHours' => $vIdleHours,
                        'ignitionStatus' => ($vIdleHours > 0),
                        'idlingDuration' => round($vIdleHours * 60, 2),
                        'speed' => 0.0,
                        'activityType' => ($vIdleHours > 0) ? 'idling' : 'ignition_off',
                        'messagesReceived' => (int)($vMsgs * 0.4),
                        'createdAt' => $now,
                        'updatedAt' => $now,
                    ];
                }
            }
        }

        // Insert in safe batches of 100 records
        foreach (array_chunk($records, 100) as $chunk) {
            DB::table('vehicleTrackingData')->insert($chunk);
        }
    }
}
