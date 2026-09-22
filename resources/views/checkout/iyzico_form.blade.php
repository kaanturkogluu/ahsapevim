@extends('layouts.app')

@section('title', 'Güvenli Kart ile Ödeme - AhşapEvim')

@section('content')
<div class="bg-[#F7F5F0] py-10 min-h-screen">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Breadcrumb / Header -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-[#C87A53] transition mb-1">
                    <i class="fa-solid fa-arrow-left"></i> Sepete veya Adres Bilgilerine Dön
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 font-serif">Güvenli Kart ile Ödeme</h1>
            </div>

            <!-- Trust Badge Pill -->
            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 px-3.5 py-1.5 rounded-full text-xs font-bold text-emerald-800 shadow-sm">
                <i class="fa-solid fa-shield-halved text-emerald-600 text-sm"></i>
                <span>256-Bit SSL & 3D Secure Koruması</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side: Order Summary Card -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200/80 p-5 md:p-6">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-3 mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Sipariş Detayı</span>
                        <span class="px-2.5 py-0.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-md font-mono text-xs font-extrabold">
                            #{{ $order->id }}
                        </span>
                    </div>

                    <!-- Customer & Delivery -->
                    <div class="space-y-2.5 text-xs text-gray-600 mb-5">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Alıcı:</span>
                            <span class="font-bold text-gray-800">{{ $order->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">E-Posta:</span>
                            <span class="font-medium text-gray-700">{{ $order->email }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Telefon:</span>
                            <span class="font-mono text-gray-700">{{ $order->phone }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-2 pt-1 border-t border-stone-100">
                            <span class="text-gray-400 shrink-0">Teslimat:</span>
                            <span class="font-medium text-gray-700 text-right truncate max-w-[200px]" title="{{ $order->address }}">
                                {{ $order->city }} / {{ $order->district }}
                            </span>
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <div class="border-t border-stone-100 pt-3 mb-4">
                        <span class="text-[11px] font-bold text-gray-400 block mb-2 uppercase tracking-wider">Sepet Kalemleri</span>
                        <div class="max-h-48 overflow-y-auto divide-y divide-stone-100 pr-1 text-xs">
                            @foreach($order->items as $item)
                                <div class="py-2 flex items-center justify-between gap-3">
                                    <div class="truncate flex-1">
                                        <div class="font-semibold text-gray-800 truncate">{{ $item->product ? $item->product->name : 'Ahşap Ürün' }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $item->quantity }} adet</div>
                                    </div>
                                    <div class="font-bold text-gray-800 shrink-0">
                                        {{ number_format($item->price * $item->quantity, 2, ',', '.') }} TL
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Total Amount Display -->
                    <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-gray-500 block">Toplam Tutar</span>
                            <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-truck-fast"></i> Kargo Ücretsiz
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-black text-[#C87A53]">
                                {{ number_format($order->total_amount, 2, ',', '.') }} TL
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Trust Guarantee Box -->
                <div class="bg-white rounded-2xl border border-stone-200/80 p-4 text-xs text-gray-500 space-y-2 shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-gray-700">
                        <i class="fa-solid fa-lock text-[#C87A53]"></i>
                        <span>İyzico Güvenli Ödeme Altyapısı</span>
                    </div>
                    <p class="leading-relaxed text-[11px]">
                        Kart bilgileriniz doğrudan banka altyapısına şifreli iletilir ve sunucularımızda saklanmaz. Tüm banka kartları, kredi kartları ve taksit seçenekleri desteklenmektedir.
                    </p>
                    <div class="flex items-center gap-3 pt-2 border-t border-stone-100">
                        <img src="https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/visa.png" class="h-4 opacity-60" alt="Visa">
                        <img src="https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/mastercard.png" class="h-4 opacity-60" alt="Mastercard">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Troy • Bonus • Maximum • World</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Iyzico Checkout Form Embed -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl shadow-md border border-stone-200 p-5 md:p-8 min-h-[560px] relative">
                    
                    <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-[#C87A53] flex items-center justify-center font-bold">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-800">Kart Bilgilerinizi Giriniz</h2>
                                <p class="text-xs text-gray-400">Tek çekim veya taksit seçeneklerini form üzerinden seçebilirsiniz.</p>
                            </div>
                        </div>

                        <!-- Iyzico Logo / Badge -->
                        <span class="px-2.5 py-1 bg-stone-100 text-stone-600 rounded-md text-[11px] font-extrabold flex items-center gap-1">
                            <i class="fa-solid fa-shield text-[#C87A53]"></i> iyzico
                        </span>
                    </div>

                    <!-- Iyzico Form Container -->
                    <div class="iyzico-embed-container w-full min-h-[440px]">
                        @if(!str_contains($checkoutFormContent, 'id="iyzipay-checkout-form"'))
                            <div id="iyzipay-checkout-form" class="responsive"></div>
                        @endif

                        {!! $checkoutFormContent !!}
                    </div>

                    @if(!empty($paymentPageUrl))
                        <div class="mt-4 pt-4 border-t border-stone-100 text-center text-xs text-gray-500">
                            Ödeme formu açılmadıysa 
                            <a href="{{ $paymentPageUrl }}" class="text-[#C87A53] underline font-bold hover:text-amber-800">
                                buraya tıklayarak İyzico ödeme sayfasına gidebilirsiniz.
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
