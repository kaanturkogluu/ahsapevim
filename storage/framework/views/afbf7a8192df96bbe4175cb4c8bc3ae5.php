<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sipariş Onayı - AhşapEvim</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F7F5F0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2E251E;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F7F5F0; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #EFEAE0; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #FAF3EE; padding: 25px; border-bottom: 2px solid #E6DFD5;">
                            <h1 style="margin: 0; color: #C87A53; font-size: 26px; font-weight: 800; letter-spacing: 0.5px;">AhşapEvim</h1>
                            <p style="margin: 5px 0 0 0; color: #8C6239; font-size: 13px;">Masif Ahşap El İşçiliği ve Kişiye Özel Tasarımlar</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 10px 0; color: #2E251E; font-size: 20px;">Sayın <?php echo e($order->name); ?>,</h2>
                            <p style="margin: 0 0 20px 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                Siparişiniz başarıyla alınmıştır! El işçiliği ile ürettiğimiz ürünleriniz özenle hazırlanıp en kısa sürede kargoya teslim edilecektir.
                            </p>

                            <!-- Order Summary Box -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #FAF9F6; border-radius: 12px; padding: 15px; margin-bottom: 25px; border: 1px solid #EFEAE0;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 6px 0; font-size: 13px; color: #888888;">Sipariş Numarası: <strong style="color: #2E251E;">#<?php echo e($order->id); ?></strong></p>
                                        <?php if($order->tracking_code): ?>
                                            <p style="margin: 0 0 6px 0; font-size: 13px; color: #888888;">Sipariş Takip Kodu: <strong style="color: #C87A53; font-family: monospace; font-size: 14px;"><?php echo e($order->tracking_code); ?></strong></p>
                                        <?php endif; ?>
                                        <p style="margin: 0 0 6px 0; font-size: 13px; color: #888888;">Sipariş Tarihi: <strong style="color: #2E251E;"><?php echo e($order->created_at->format('d.m.Y H:i')); ?></strong></p>
                                        <p style="margin: 0; font-size: 13px; color: #888888;">Teslimat Adresi: <strong style="color: #2E251E;"><?php echo e($order->address); ?> <?php echo e($order->district ? $order->district . '/' : ''); ?><?php echo e($order->city); ?></strong></p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Items Table -->
                            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #C87A53; border-bottom: 1px solid #EFEAE0; padding-bottom: 8px;">Sipariş Edilen Ürünler</h3>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 25px;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #EFEAE0; text-align: left; font-size: 12px; color: #888888; text-transform: uppercase;">
                                        <th style="padding: 8px 0;">Ürün</th>
                                        <th style="padding: 8px 0; text-align: center;">Adet</th>
                                        <th style="padding: 8px 0; text-align: right;">Fiyat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr style="border-bottom: 1px solid #FAF9F6; font-size: 13px; color: #333333;">
                                            <td style="padding: 12px 0;">
                                                <strong><?php echo e($item->product ? $item->product->name : 'Ahşap Ürün'); ?></strong>
                                                <?php if(!empty($item->features['front_image'])): ?>
                                                    <span style="display: block; font-size: 11px; color: #C87A53; margin-top: 3px;">✓ Özel Ön & Arka Yüz Fotoğraflı</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 12px 0; text-align: center;"><?php echo e($item->quantity); ?></td>
                                            <td style="padding: 12px 0; text-align: right; font-weight: bold;">₺<?php echo e(number_format($item->price * $item->quantity, 2, ',', '.')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>

                            <!-- Total -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #FAF3EE; border-radius: 10px; padding: 15px;">
                                <tr>
                                    <td style="font-size: 15px; font-weight: bold; color: #2E251E;">Toplam Ödenen Tutar:</td>
                                    <td style="font-size: 20px; font-weight: 800; color: #C87A53; text-align: right;">₺<?php echo e(number_format($order->total_amount, 2, ',', '.')); ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #2E251E; color: #E6DFD5; padding: 20px; font-size: 12px; line-height: 1.6;">
                            <p style="margin: 0;"><strong>AhşapEvim Manisa Atölyesi</strong></p>
                            <p style="margin: 4px 0 0 0; opacity: 0.8;">Bizi tercih ettiğiniz için teşekkür ederiz!</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/emails/order_success.blade.php ENDPATH**/ ?>