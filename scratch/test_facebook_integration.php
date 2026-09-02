<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== 1. Checking Facebook Settings ===\n";
echo "Pixel ID: " . \App\Models\Setting::get('facebook_pixel_id') . "\n";
echo "Access Token: " . substr(\App\Models\Setting::get('facebook_access_token'), 0, 20) . "...\n";

echo "\n=== 2. Testing Facebook CAPI Service Instance ===\n";
$fbService = app(\App\Services\FacebookCapiService::class);
echo "Is Configured: " . ($fbService->isConfigured() ? "YES" : "NO") . "\n";
echo "Service Pixel ID: " . $fbService->getPixelId() . "\n";

echo "\n=== 3. Testing Facebook Catalog XML Feed Generation ===\n";
$seoController = new \App\Http\Controllers\SeoController();
$xml = $seoController->generateFacebookCatalogXml();
echo "XML Length: " . strlen($xml) . " bytes\n";
echo "XML Snippet:\n";
echo substr($xml, 0, 700) . "\n...\n";

// Verify XML valid syntax
libxml_use_internal_errors(true);
$doc = simplexml_load_string($xml);
if ($doc === false) {
    echo "XML Error: Invalid XML generated!\n";
    foreach(libxml_get_errors() as $error) {
        echo "\t", $error->message;
    }
} else {
    echo "XML Syntax: PERFECT! Found " . count($doc->channel->item) . " products in feed.\n";
}

echo "\n=== 4. Clearing View & Config Cache ===\n";
\Illuminate\Support\Facades\Artisan::call('config:clear');
\Illuminate\Support\Facades\Artisan::call('view:clear');
\Illuminate\Support\Facades\Artisan::call('cache:clear');
echo "Caches successfully cleared!\n";
