{!! '<?xml version="1.0" encoding="utf-8"?>' !!}
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
  <channel>
    <title>{{ config('app.name', 'Ahşap Evim Manisa') }} — Facebook &amp; Instagram Ürün Kataloğu</title>
    <link>{{ url('/') }}</link>
    <description>{{ config('app.name', 'Ahşap Evim Manisa') }} - Masif Ahşap Kişiye Özel Çerçeveler Meta Catalog Feed</description>
    @foreach ($products as $product)
    <item>
      <g:id>{{ $product->sku ?? $product->id }}</g:id>
      <g:title><![CDATA[{{ $product->name }}]]></g:title>
      <g:description><![CDATA[{!! strip_tags($product->description ?: ($product->name . ' - Kişiye özel fotoğraflı el yapımı masif ahşap dönen çerçeve.')) !!}]]></g:description>
      <g:link>{{ $product->url }}</g:link>
      
      @php
          $images = $product->images;
          $mainImage = $images->first();
          $additionalImages = $images->skip(1)->take(10);
          $mainImageUrl = ($mainImage && !empty($mainImage->url)) ? (str_starts_with($mainImage->url, 'http') ? $mainImage->url : url($mainImage->url)) : url('/cerceve.png');
      @endphp
      <g:image_link>{{ $mainImageUrl }}</g:image_link>
      @if($additionalImages->count() > 0)
          @foreach($additionalImages as $image)
              @if(!empty($image->url))
              @php $addUrl = str_starts_with($image->url, 'http') ? $image->url : url($image->url); @endphp
              <g:additional_image_link>{{ $addUrl }}</g:additional_image_link>
              @endif
          @endforeach
      @endif

      <g:availability>{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}</g:availability>
      
      @if($product->original_price && $product->original_price > $product->price)
        <g:price>{{ number_format($product->original_price, 2, '.', '') }} TRY</g:price>
        <g:sale_price>{{ number_format($product->price, 2, '.', '') }} TRY</g:sale_price>
      @else
        <g:price>{{ number_format($product->price, 2, '.', '') }} TRY</g:price>
      @endif

      <g:brand><![CDATA[{{ $product->brand_name ?? 'Ahşap Evim Manisa' }}]]></g:brand>
      <g:condition>new</g:condition>
      
      @if($product->barcode)
      <g:gtin>{{ $product->barcode }}</g:gtin>
      @endif

      <g:google_product_category>536</g:google_product_category>
      <g:product_type><![CDATA[{{ $product->category?->name ?? 'Masif Ahşap Çerçeve' }}]]></g:product_type>
      <g:fb_product_category>home_and_garden</g:fb_product_category>
      <g:custom_label_0><![CDATA[El Yapımı Ahşap]]></g:custom_label_0>
      <g:custom_label_1><![CDATA[{{ $product->stock > 0 ? 'Stokta Var' : 'Tükendi' }}]]></g:custom_label_1>
      <g:custom_label_2><![CDATA[{{ $product->category?->name ?? 'Dekoratif Ürün' }}]]></g:custom_label_2>
    </item>
    @endforeach
  </channel>
</rss>
