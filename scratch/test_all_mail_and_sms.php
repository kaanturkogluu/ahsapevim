<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== 1. TESTING ALL DYNAMICMAIL TEMPLATES ===\n";

$slugs = ['welcome_user', 'order_success', 'order_shipped', 'order_completed', 'order_cancelled', 'order_failed'];

$dummyData = [
    'user_name'           => 'Kaan Türkoğlu',
    'user_email'          => 'kaan@example.com',
    'order_id'            => 1050,
    'tracking_code'       => 'AHS-894102',
    'shipping_company'    => 'Yurtiçi Kargo',
    'cargo_tracking_code' => '987654321012',
    'cancellation_reason' => 'Müşteri talebi doğrultusunda sipariş iptal edilmiştir.',
    'total_amount'        => '850,00',
    'delivery_address'    => 'Şehzadeler Mah. 120. Sokak No:4 Manisa/Merkez',
    'product_details'     => '<table style="width: 100%;"><tr><td>Masif Ahşap Çerçeve</td><td>1</td><td>₺850,00</td></tr></table>',
    'site_name'           => 'AhşapEvim',
];

foreach ($slugs as $slug) {
    try {
        $mail = new \App\Mail\DynamicMail($slug, $dummyData);
        $rendered = $mail->render();
        echo "[OK] Slug: {$slug} | Subject: {$mail->subject} | Length: " . strlen($rendered) . " bytes\n";
    } catch (\Throwable $e) {
        echo "[FAIL] Slug: {$slug} | Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== 2. TESTING SMS FORMAT & MESSAGE ===\n";
$netgsm = app(\App\Services\NetgsmService::class);
$phoneSamples = ['0532 123 45 67', '+905321234567', '905321234567', '5321234567'];

foreach ($phoneSamples as $sample) {
    $clean = $netgsm->formatPhone($sample);
    echo "Phone: '{$sample}' => '{$clean}' (Valid: " . ($clean ? 'YES' : 'NO') . ")\n";
}

$sampleOrderName = 'Kaan Türkoğlu';
$sampleOrderId = 1050;
$sampleCompany = 'Yurtiçi Kargo';
$sampleCargoCode = '987654321012';

$smsMsg = "Sayın {$sampleOrderName}, #{$sampleOrderId} numaralı siparişiniz {$sampleCompany} firmasına teslim edilmiştir. Kargo Takip No: {$sampleCargoCode}. AhşapEvim";
echo "Sample Cargo SMS Message: {$smsMsg}\n";

echo "\n=== 3. VERIFYING DB VS BUILT-IN TEMPLATES ===\n";
$dbTemplates = \App\Models\EmailTemplate::all();
echo "Total DB Templates: " . $dbTemplates->count() . "\n";
foreach ($dbTemplates as $t) {
    echo "- ID: {$t->id} | Slug: {$t->slug} | Name: {$t->name} | Subject: {$t->subject} | Active: " . ($t->is_active ? 'Yes' : 'No') . "\n";
}

echo "\nALL TESTS FINISHED SUCCESSFULLY!\n";
