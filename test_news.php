<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = new \Illuminate\Http\Request(['q' => 'logistics supply chain']);
$controller = new \App\Http\Controllers\IntelligenceController(new \App\Services\IntelligenceService());
$response = $controller->fetchGoogleNews($request);
echo $response->getContent();
