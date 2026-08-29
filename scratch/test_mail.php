<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Test DynamicMail
$customerMail = new \App\Mail\DynamicMail('order_success', [
    'user_name'        => 'Kaan Türkoğlu',
    'order_id'         => 23,
    'tracking_code'    => 'AHS-23',
    'total_amount'     => '450,00',
    'delivery_address' => 'Manisa Merkez',
    'product_details'  => 'Masif Çerçeve 1 Adet - ₺450,00',
]);

$renderedCust = $customerMail->render();
echo "[DynamicMail (Customer)] Rendered: " . strlen($renderedCust) . " bytes | Subject: " . $customerMail->subject . "\n";

// 2. Test AdminNewOrderMail
$order = \App\Models\Order::latest()->first();
if ($order) {
    $adminMail = new \App\Mail\AdminNewOrderMail($order);
    $renderedAdmin = $adminMail->render();
    echo "[AdminNewOrderMail] Rendered: " . strlen($renderedAdmin) . " bytes | Subject: " . $adminMail->subject . "\n";
} else {
    echo "[AdminNewOrderMail] No order found in DB to test.\n";
}
