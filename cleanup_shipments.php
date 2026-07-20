<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$shipments = App\Models\Shipment::orderBy('id')->get();
$seen = [];
$deleted = 0;
foreach($shipments as $shipment) {
    if(isset($seen[$shipment->shipment_number])) {
        $shipment->delete();
        $deleted++;
    } else {
        $seen[$shipment->shipment_number] = true;
    }
}
echo "Deleted $deleted duplicate shipments.\n";
