<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$shipments = App\Models\Shipment::orderBy('id')->get();
$seen = [];
$deleted = 0;
foreach($shipments as $shipment) {
    // Highly aggressive duplicate check: same origin, same destination, same commodity
    $hash = md5($shipment->origin_country . $shipment->destination_country . $shipment->commodity);
    
    if(isset($seen[$hash])) {
        // Delete related records
        $shipment->activities()->delete();
        $shipment->redirects()->delete();
        $shipment->delete();
        $deleted++;
    } else {
        $seen[$hash] = true;
    }
}
echo "Aggressive Cleanup: Deleted $deleted duplicate shipments.\n";
