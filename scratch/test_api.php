<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/yonetim/api/son-siparisler', 'GET');
$controller = new \App\Http\Controllers\Admin\SettingController();
$response = $controller->recentOrdersApi($request);

echo $response->getContent() . PHP_EOL;
