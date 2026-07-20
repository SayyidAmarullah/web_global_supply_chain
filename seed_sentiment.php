<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$positive = ['growth', 'increase', 'profit', 'stable', 'improve', 'surge', 'record', 'highest', 'recovery', 'boost', 'strong', 'resilient', 'boom', 'opportunity', 'success'];
$negative = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decrease', 'fall', 'drop', 'crash', 'risk', 'shortage', 'block', 'tension', 'conflict', 'storm', 'bottleneck', 'warning', 'decline', 'loss'];

foreach ($positive as $w) {
    \Illuminate\Support\Facades\DB::table('positive_words')->insertOrIgnore(['word' => $w, 'created_at' => now(), 'updated_at' => now()]);
}
foreach ($negative as $w) {
    \Illuminate\Support\Facades\DB::table('negative_words')->insertOrIgnore(['word' => $w, 'created_at' => now(), 'updated_at' => now()]);
}
echo "Seeded successfully.\n";
