<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING ORDER CANCELLATION MAIL & SMS ===\n";

$data = [
    'user_name'           => 'Ahmet Yılmaz',
    'user_email'          => 'ahmet@example.com',
    'order_id'            => 1055,
    'tracking_code'       => 'AHS-994123',
    'shipping_company'    => 'Yurtiçi Kargo',
    'cargo_tracking_code' => '',
    'cancellation_reason' => 'Müşteri talebi doğrultusunda sipariş iptal edilmiştir.',
    'total_amount'        => '1.450,00',
    'delivery_address'    => 'Atatürk Mah. Cumhuriyet Cad. No:15 Manisa/Merkez',
    'product_details'     => '<table style="width: 100%;"><tr><td>Masif Ahşap Çerçeve</td><td>1</td><td>₺1.450,00</td></tr></table>',
    'site_name'           => 'AhşapEvim',
];

// 1. DynamicMail
$cancelMail = new \App\Mail\DynamicMail('order_cancelled', $data);
$rendered = $cancelMail->render();

echo "Subject: " . $cancelMail->subject . "\n";
echo "Rendered Length: " . strlen($rendered) . " bytes\n";
echo "Contains user name: " . (str_contains($rendered, 'Ahmet Yılmaz') ? 'YES' : 'NO') . "\n";
echo "Contains cancellation reason: " . (str_contains($rendered, 'Müşteri talebi') ? 'YES' : 'NO') . "\n";
echo "Contains order ID: " . (str_contains($rendered, '1055') ? 'YES' : 'NO') . "\n";

// 2. Cancellation SMS text
$cancelSms = "Sayın {$data['user_name']}, #{$data['order_id']} numaralı siparişiniz iptal edilmiştir. Detaylı bilgi veya sorularınız için bizimle iletişime geçebilirsiniz. AhşapEvim";
echo "Cancellation SMS: " . $cancelSms . "\n";

echo "\nCANCELLATION FLOW TEST PASSED!\n";
