@extends('layouts.admin')

@section('title', 'Cari Ekstre - ' . $user->name)

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <a href="{{ route('admin.cari.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition"><i class="fa-solid fa-arrow-left mr-1"></i> Cari Hesaplara Dön</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><i class="fa-solid fa-file-invoice text-[#C87A53] mr-2"></i>Cari Ekstre: {{ $user->name }}</h1>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Sol Sütun: Müşteri Özeti ve İşlem Formu -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Müşteri Özeti -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Müşteri Bilgileri</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Ad Soyad:</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">E-posta:</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Telefon:</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $user->phone ?? '-' }}</span>
                </div>
                
                <div class="pt-4 mt-2 border-t border-gray-100">
                    <div class="text-sm text-gray-500 mb-1">Güncel Bakiye</div>
                    @if($user->balance > 0)
                        <div class="text-3xl font-black text-red-600">{{ number_format($user->balance, 2, ',', '.') }} ₺</div>
                        <div class="text-xs text-red-500 font-medium mt-1"><i class="fa-solid fa-circle-info"></i> Müşteri firmamıza borçlu.</div>
                    @elseif($user->balance < 0)
                        <div class="text-3xl font-black text-green-600">{{ number_format(abs($user->balance), 2, ',', '.') }} ₺</div>
                        <div class="text-xs text-green-500 font-medium mt-1"><i class="fa-solid fa-circle-info"></i> Müşteri alacaklı (Fazla ödeme).</div>
                    @else
                        <div class="text-3xl font-black text-gray-700">0,00 ₺</div>
                        <div class="text-xs text-gray-500 font-medium mt-1">Borç veya alacak bulunmuyor.</div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Yeni İşlem Ekleme Formu -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-plus-circle text-[#C87A53] mr-1"></i> Yeni İşlem Ekle</h2>
            
            <form action="{{ route('admin.cari.store', $user->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">İşlem Türü</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="debit" class="peer sr-only" required>
                            <div class="w-full text-center px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 peer-checked:bg-red-50 peer-checked:border-red-500 peer-checked:text-red-700 transition">
                                Borçlandır (Müşteriye Borç Yaz)
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="credit" class="peer sr-only" required>
                            <div class="w-full text-center px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-700 transition">
                                Tahsilat (Ödeme / Alacak Ekle)
                            </div>
                        </label>
                    </div>
                    @error('type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Tutar (₺)</label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#C87A53] focus:border-[#C87A53]">
                    @error('amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <input type="text" name="description" id="description" placeholder="Örn: Sipariş #1005, Nakit Tahsilat, Havale..." required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#C87A53] focus:border-[#C87A53]">
                    @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-5">
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tarih (Opsiyonel)</label>
                    <input type="datetime-local" name="date" id="date" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#C87A53] focus:border-[#C87A53]">
                    <p class="text-xs text-gray-500 mt-1">Geçmişe dönük işlem girmek isterseniz tarihi değiştirebilirsiniz.</p>
                </div>
                
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-sm">
                    İşlemi Kaydet
                </button>
            </form>
        </div>
    </div>
    
    <!-- Sağ Sütun: İşlem Geçmişi (Ekstre) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-list text-gray-500 mr-1"></i> Hesap Ekstresi (İşlem Geçmişi)</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-white border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">Tarih</th>
                            <th scope="col" class="px-6 py-4">İşlem / Açıklama</th>
                            <th scope="col" class="px-6 py-4 text-right">Borç</th>
                            <th scope="col" class="px-6 py-4 text-right">Alacak/Ödeme</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $transaction->date->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-medium">{{ $transaction->description }}</div>
                                    @if($transaction->order_id)
                                        <a href="{{ route('admin.orders.show', $transaction->order_id) }}" class="text-[11px] text-[#C87A53] hover:underline block mt-0.5"><i class="fa-solid fa-link"></i> Siparişi Gör (#{{ $transaction->order_id }})</a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-medium">
                                    @if($transaction->type === 'debit')
                                        <span class="text-red-600">{{ number_format($transaction->amount, 2, ',', '.') }} ₺</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-medium">
                                    @if($transaction->type === 'credit')
                                        <span class="text-green-600">{{ number_format($transaction->amount, 2, ',', '.') }} ₺</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Bu müşteriye ait herhangi bir hesap hareketi bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
    
</div>
@endsection
