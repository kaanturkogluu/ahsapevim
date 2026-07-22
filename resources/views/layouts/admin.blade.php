<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahşap Evim Manisa - Yönetim</title>
    <!-- Tailwind CSS CDN for admin -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <div class="w-64 bg-slate-800 text-white flex flex-col">
        <div class="p-4 bg-slate-900 text-center font-bold text-xl border-b border-slate-700">
            Ahşap Evim Admin
        </div>
        <nav class="flex-1 overflow-y-auto mt-4">
            <a href="/admin/orders" class="block p-4 hover:bg-slate-700"><i class="fa-solid fa-shopping-cart mr-2"></i> Siparişler</a>
            <a href="/admin/products" class="block p-4 hover:bg-slate-700"><i class="fa-solid fa-box mr-2"></i> Ürünler</a>
            <a href="/admin/categories" class="block p-4 hover:bg-slate-700"><i class="fa-solid fa-list mr-2"></i> Kategoriler</a>
            <a href="/" class="block p-4 hover:bg-slate-700 mt-4 border-t border-slate-700"><i class="fa-solid fa-eye mr-2"></i> Siteyi Görüntüle</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <header class="bg-white p-4 shadow flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">@yield('header', 'Yönetim Paneli')</h2>
            <div>Admin</div>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
