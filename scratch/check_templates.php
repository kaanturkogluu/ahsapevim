<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$templates = \App\Models\EmailTemplate::all();
echo "Templates count: " . $templates->count() . "\n";
foreach ($templates as $t) {
    echo "- ID: {$t->id} | Slug: {$t->slug} | Name: {$t->name} | Active: " . ($t->is_active ? 'Yes' : 'No') . "\n";
    echo "  Subject: {$t->subject}\n";
}
