@extends('layouts.admin')

@section('header', 'Sipariş Yönetimi')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="mb-6 pb-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">Sipariş Listesi</h3>
        <p class="text-xs text-gray-500 mt-1">Müşterileriniz tarafından oluşturulan siparişleri takip edin.</p>
    </div>

    <!-- Dummy Orders Table for completeness -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3 w-16 text-center">Sipariş #</th>
                    <th class="pb-3">Müşteri</th>
                    <th class="pb-3">Telefon</th>
                    <th class="pb-3">Tarih</th>
                    <th class="pb-3 text-right">Tutar</th>
                    <th class="pb-3 text-center">Durum</th>
                    <th class="pb-3 w-24 text-right">Detay</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <tr>
                    <td class="py-4 text-center font-bold text-gray-700">#1024</td>
                    <td class="py-4">
                        <div class="font-bold text-gray-800">Mustafa Yılmaz</div>
                        <div class="text-xs text-gray-500 mt-0.5">mustafa@example.com</div>
                    </td>
                    <td class="py-4 text-gray-600 font-medium">0555 123 45 67</td>
                    <td class="py-4 text-gray-500">29.07.2026 14:32</td>
                    <td class="py-4 text-right font-bold text-[#C87A53]">1,250.00 TL</td>
                    <td class="py-4 text-center">
                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full font-bold text-[10px]">Hazırlanıyor</span>
                    </td>
                    <td class="py-4 text-right">
                        <button class="text-blue-600 hover:text-blue-800 font-bold text-xs"><i class="fa-solid fa-eye"></i> İncele</button>
                    </td>
                </tr>
                <tr>
                    <td class="py-4 text-center font-bold text-gray-700">#1023</td>
                    <td class="py-4">
                        <div class="font-bold text-gray-800">Ahmet Demir</div>
                        <div class="text-xs text-gray-500 mt-0.5">ahmet@example.com</div>
                    </td>
                    <td class="py-4 text-gray-600 font-medium">0532 987 65 43</td>
                    <td class="py-4 text-gray-500">28.07.2026 11:15</td>
                    <td class="py-4 text-right font-bold text-[#C87A53]">850.00 TL</td>
                    <td class="py-4 text-center">
                        <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-full font-bold text-[10px]">Kargoya Verildi</span>
                    </td>
                    <td class="py-4 text-right">
                        <button class="text-blue-600 hover:text-blue-800 font-bold text-xs"><i class="fa-solid fa-eye"></i> İncele</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
