<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use Laravel HTTP Client to fetch the global sea ports JSON dataset
        $response = \Illuminate\Support\Facades\Http::timeout(60)->get('https://raw.githubusercontent.com/marchah/sea-ports/master/lib/ports.json');
        $allPorts = $response->json();

        if (!$allPorts) {
            $this->command->error("Failed to download ports data. Falling back to basic list.");
            return;
        }

        $weatherOptions = ['Clear', 'Cloudy', 'Rain', 'Storm'];
        $congestionOptions = ['Low', 'Low', 'Low', 'Medium', 'Medium', 'High']; // Weighted

        // Shuffle the array to get a random global distribution, take 500 ports to prevent UI lag
        $portKeys = array_keys($allPorts);
        shuffle($portKeys);
        $selectedKeys = array_slice($portKeys, 0, 500);

        $insertData = [];
        foreach ($selectedKeys as $key) {
            $port = $allPorts[$key];
            
            // Validate coordinates
            if (!isset($port['coordinates']) || !is_array($port['coordinates']) || count($port['coordinates']) < 2) {
                continue;
            }

            $lng = $port['coordinates'][0];
            $lat = $port['coordinates'][1];

            $congestion = $congestionOptions[array_rand($congestionOptions)];
            $waitTime = 0;
            if ($congestion === 'Low') $waitTime = rand(2, 12);
            if ($congestion === 'Medium') $waitTime = rand(12, 36);
            if ($congestion === 'High') $waitTime = rand(36, 120);

            $randWeather = rand(1, 100);
            if ($randWeather < 50) $weather = 'Clear';
            elseif ($randWeather < 80) $weather = 'Cloudy';
            elseif ($randWeather < 95) $weather = 'Rain';
            else $weather = 'Storm';

            $insertData[] = [
                'name' => 'Port of ' . ($port['name'] ?? 'Unknown'),
                'country' => $port['country'] ?? 'Unknown',
                'code' => $key,
                'latitude' => $lat,
                'longitude' => $lng,
                'congestion' => $congestion,
                'wait_time_hours' => $waitTime,
                'weather' => $weather,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Bulk insert
        $chunks = array_chunk($insertData, 100);
        foreach ($chunks as $chunk) {
            \App\Models\Port::insert($chunk);
        }

        $this->command->info("Successfully seeded " . count($insertData) . " global ports.");
    }
}
