<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>

<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
  <channel>
    <title><?php echo e(config('app.name', 'Laravel')); ?></title>
    <link><?php echo e(url('/')); ?></link>
    <description><?php echo e(config('app.name', 'Laravel')); ?> - Google Merchant Feed</description>
    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <item>
      <g:id><?php echo e($product->sku ?? $product->id); ?></g:id>
      <title><![CDATA[<?php echo e($product->name); ?>]]></title>
      <description><![CDATA[<?php echo strip_tags($product->description); ?>]]></description>
      <link><?php echo e(route('product.show', $product->slug ?? $product->id)); ?></link>
      
      <?php
          $images = $product->images;
          $mainImage = $images->first();
          $additionalImages = $images->skip(1)->take(5);
      ?>
      <g:image_link><?php echo e(($mainImage && !empty($mainImage->url)) ? asset($mainImage->url) : url('images/default-product.jpg')); ?></g:image_link>
      <?php if($additionalImages->count() > 0): ?>
          <?php $__currentLoopData = $additionalImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if(!empty($image->url)): ?>
              <g:additional_image_link><?php echo e(asset($image->url)); ?></g:additional_image_link>
              <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>

      <g:availability><?php echo e($product->stock > 0 ? 'in_stock' : 'out_of_stock'); ?></g:availability>
      <g:price><?php echo e(number_format($product->price, 2, '.', '')); ?> TRY</g:price>
      <?php if($product->brand_name || $product->brand): ?>
      <g:brand><![CDATA[<?php echo e($product->brand_name ?? $product->brand->name); ?>]]></g:brand>
      <?php endif; ?>
      
      <?php if($product->barcode): ?>
      <g:gtin><?php echo e($product->barcode); ?></g:gtin>
      <?php endif; ?>
      
      <?php if($product->category_name || $product->category): ?>
      <g:product_type><![CDATA[<?php echo e($product->category_name ?? $product->category->name); ?>]]></g:product_type>
      <?php endif; ?>

      <g:condition>new</g:condition>
    </item>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </channel>
</rss>
<?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/seo/urunler_xml.blade.php ENDPATH**/ ?>