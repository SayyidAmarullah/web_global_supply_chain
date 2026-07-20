<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

$controller = new App\Http\Controllers\MapController(new App\Services\IntelligenceService());
$response = $controller->getMapData();

$data = json_decode($response->getContent(), true);

foreach($data['shipments'] as $ship) {
    if (!is_array($ship['route'])) {
        echo "ERROR: route is not array for shipment {$ship['id']}\n";
        continue;
    }
    foreach($ship['route'] as $point) {
        if (!isset($point[0]) || !isset($point[1])) {
            echo "ERROR: Invalid point in route for shipment {$ship['id']}\n";
        }
    }
}
echo "Checked all shipments successfully.\n";
