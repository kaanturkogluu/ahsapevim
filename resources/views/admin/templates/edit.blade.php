@extends('layouts.admin')

@section('title', '3D Çerçeve Şablonu Düzenle - AhşapEvim')

@section('header', '3D Çerçeve Şablonu Düzenle')

@section('content')
<!-- Three.js Loaders -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

<div class="bg-gray-50 min-h-[calc(100vh-140px)] flex flex-col">
    <div class="container mx-auto px-4 py-6 flex-grow flex flex-col lg:flex-row gap-6">
        
        <!-- Sidebar Controls / Form -->
        <div class="w-full lg:w-96 flex-shrink-0 flex flex-col gap-4">
            
            <form action="{{ route('admin.templates.update', $template->id) }}" method="POST" id="templateForm" class="space-y-4" onsubmit="preventSpamSubmit(this)">
                @csrf
                @method('PUT')
                
                <!-- General Info -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100"><i class="fa-solid fa-info-circle text-brand mr-2"></i>Genel Bilgiler</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Şablon Adı *</label>
                            <input type="text" name="name" required value="{{ old('name', $template->name) }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 20x25 Standart Dönen Çerçeve">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Ahşap Rengi / Renk Paleti *</label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="woodColorPicker" value="{{ str_starts_with($template->wood_type, '#') ? $template->wood_type : '#4a3319' }}" class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5 shrink-0" onchange="updateWoodColorFromPicker(this.value)">
                                <input type="text" name="wood_type" id="woodTypeSelect" required value="{{ old('wood_type', $template->wood_type) }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Renk kodu veya adı (#4a3319, Ceviz vb.)">
                            </div>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <button type="button" onclick="setWoodPreset('#4a3319', 'Ceviz')" class="px-2 py-1 bg-[#4a3319] text-white text-[10px] font-bold rounded shadow-sm hover:opacity-90">Ceviz</button>
                                <button type="button" onclick="setWoodPreset('#a8896c', 'Meşe')" class="px-2 py-1 bg-[#a8896c] text-white text-[10px] font-bold rounded shadow-sm hover:opacity-90">Meşe</button>
                                <button type="button" onclick="setWoodPreset('#e3d3bd', 'Çam')" class="px-2 py-1 bg-[#e3d3bd] text-gray-800 text-[10px] font-bold rounded shadow-sm hover:opacity-90 border">Çam</button>
                                <button type="button" onclick="setWoodPreset('#8c462b', 'Kiraz')" class="px-2 py-1 bg-[#8c462b] text-white text-[10px] font-bold rounded shadow-sm hover:opacity-90">Kiraz</button>
                                <button type="button" onclick="setWoodPreset('#2b2b2b', 'Siyah')" class="px-2 py-1 bg-[#2b2b2b] text-white text-[10px] font-bold rounded shadow-sm hover:opacity-90">Siyah</button>
                                <button type="button" onclick="setWoodPreset('#f0ede6', 'Beyaz')" class="px-2 py-1 bg-[#f0ede6] text-gray-800 text-[10px] font-bold rounded shadow-sm hover:opacity-90 border">Beyaz</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 1: OUTER FRAME -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
                    <div class="absolute -top-3 -left-3 w-8 h-8 bg-[#C87A53] text-white font-bold rounded-full flex items-center justify-center shadow">1</div>
                    <h2 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 ml-2">Dış Çerçeve Ölçüleri</h2>
                    
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Genişlik (X)</label>
                                <input type="number" name="width" id="frameWidth" value="{{ $template->width }}" min="5" max="100" step="0.5" class="w-full text-sm border-gray-300 rounded-md p-2 border focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Yükseklik (Y)</label>
                                <input type="number" name="height" id="frameHeight" value="{{ $template->height }}" min="5" max="100" step="0.5" class="w-full text-sm border-gray-300 rounded-md p-2 border focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Derinlik (Z)</label>
                                <input type="number" name="depth" id="frameDepth" value="{{ $template->depth }}" min="1" max="20" step="0.1" class="w-full text-sm border-gray-300 rounded-md p-2 border focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Et Kalınlığı</label>
                                <input type="number" name="thickness" id="frameThickness" value="{{ $template->thickness }}" min="1" max="10" step="0.1" class="w-full text-sm border-gray-300 rounded-md p-2 border focus:border-brand">
                            </div>
                        </div>

                        <div class="mt-4 border-t border-gray-100 pt-3">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Çizilecek Kenarlar</label>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                <label class="flex items-center gap-2"><input type="checkbox" name="has_top" id="partTop" value="1" {{ $template->has_top ? 'checked' : '' }} class="rounded text-brand focus:ring-brand"> Üst</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="has_bottom" id="partBottom" value="1" {{ $template->has_bottom ? 'checked' : '' }} class="rounded text-brand focus:ring-brand"> Alt</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="has_left" id="partLeft" value="1" {{ $template->has_left ? 'checked' : '' }} class="rounded text-brand focus:ring-brand"> Sol</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="has_right" id="partRight" value="1" {{ $template->has_right ? 'checked' : '' }} class="rounded text-brand focus:ring-brand"> Sağ</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: INNER FRAME -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
                    <div class="absolute -top-3 -left-3 w-8 h-8 bg-[#C87A53] text-white font-bold rounded-full flex items-center justify-center shadow">2</div>
                    <h2 class="text-sm font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 ml-2">İç Dönen Çerçeve Ölçüleri</h2>
                    
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Genişlik</label>
                                <input type="number" name="inner_width" id="innerWidth" value="{{ $template->inner_width }}" min="2" max="90" step="0.5" class="w-full text-sm border-gray-300 rounded-md p-2 border focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Yükseklik</label>
                                <input type="number" name="inner_height" id="innerHeight" value="{{ $template->inner_height }}" min="2" max="90" step="0.5" class="w-full text-sm border-gray-300 rounded-md p-2 border focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Derinlik</label>
                                <input type="number" name="inner_depth" id="innerDepth" value="{{ $template->inner_depth }}" min="0.5" max="15" step="0.1" class="w-full text-sm border-gray-300 rounded-md p-2 border focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Kenar Kalınlığı</label>
                                <input type="number" name="inner_border" id="innerBorder" value="{{ $template->inner_border }}" min="0.5" max="10" step="0.1" class="w-full text-sm border-gray-300 rounded-md p-2 border focus:border-brand">
                            </div>
                        </div>

                        <div class="mt-2 border-t border-gray-100 pt-2">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Dikey / Yatay Kaydırma (Offset)</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] text-gray-600 mb-1 flex justify-between">
                                        <span>Sağ/Sol (X)</span> <span id="posX_val" class="font-bold text-brand">{{ $template->pos_x }}</span>
                                    </label>
                                    <input type="range" name="pos_x" id="posX" min="-10" max="10" step="0.1" value="{{ $template->pos_x }}" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('posX_val').innerText = this.value">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-600 mb-1 flex justify-between">
                                        <span>Yukarı/Aşağı (Y)</span> <span id="posY_val" class="font-bold text-brand">{{ $template->pos_y }}</span>
                                    </label>
                                    <input type="range" name="pos_y" id="posY" min="-10" max="10" step="0.1" value="{{ $template->pos_y }}" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('posY_val').innerText = this.value">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">3D Ahşap Doku & Tırtık Derinliği (Bump Scale)</label>
                            <div class="flex items-center gap-3">
                                <input type="range" name="bump_scale" id="bumpScale" min="0" max="1.2" step="0.02" value="{{ $template->bump_scale ?: '0.45' }}" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('bumpVal').innerText = this.value">
                                <span id="bumpVal" class="text-xs font-bold text-gray-600 w-8">{{ $template->bump_scale ?: '0.45' }}</span>
                            </div>
                        </div>

                        <!-- 3D Ekstra Obje / Aksesuar Bölümü -->
                        <div class="border-t border-gray-100 pt-4 mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <label for="hasAccessory" class="text-xs font-bold text-gray-800 flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-lightbulb text-amber-500"></i> 3D Obje / Aksesuar Ekle
                                </label>
                                <input type="checkbox" name="has_accessory" id="hasAccessory" value="1" {{ $template->has_accessory ? 'checked' : '' }} class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand cursor-pointer" onchange="toggleAccessoryControls(); redrawModel();">
                            </div>

                            <div id="accessoryControls" class="space-y-3 {{ $template->has_accessory ? '' : 'hidden' }} bg-amber-50/70 p-3 rounded-xl border border-amber-100">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Obje Tipi</label>
                                    <select name="accessory_type" id="accessoryType" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none bg-white font-medium" onchange="redrawModel();">
                                        <option value="street_lamp" {{ ($template->accessory_type ?? 'street_lamp') === 'street_lamp' ? 'selected' : '' }}>Nostaljik Sokak Lambası (Işıklı)</option>
                                        <option value="wooden_clock" {{ ($template->accessory_type ?? '') === 'wooden_clock' ? 'selected' : '' }}>Nostaljik Ahşap Masa Saati</option>
                                        <option value="flower_vase" {{ ($template->accessory_type ?? '') === 'flower_vase' ? 'selected' : '' }}>Saksılı Mini Çiçek / Bitki</option>
                                        <option value="mini_bookshelf" {{ ($template->accessory_type ?? '') === 'mini_bookshelf' ? 'selected' : '' }}>Minyatür Ahşap Kitap Dizimi</option>
                                        <option value="candle_holder" {{ ($template->accessory_type ?? '') === 'candle_holder' ? 'selected' : '' }}>🕯️ Nostaljik Şamdan & Mum</option>
                                        <option value="abstract_sculpture" {{ ($template->accessory_type ?? '') === 'abstract_sculpture' ? 'selected' : '' }}>🗿 Modern Geometrik Heykel</option>
                                    </select>
                                    <button type="button" onclick="openAddAccessoryModal()" class="w-full mt-2 py-1.5 px-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white text-xs font-bold rounded-lg shadow-sm flex items-center justify-center gap-2 transition cursor-pointer">
                                        <i class="fa-solid fa-wand-magic-sparkles text-amber-200"></i> Yeni Obje Ekle / Tasarla (Pop-up)
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Obje Konumu</label>
                                    <select name="accessory_position" id="accessoryPos" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none bg-white font-medium" onchange="redrawModel();">
                                        <option value="right" {{ ($template->accessory_position ?? 'right') === 'right' ? 'selected' : '' }}>Sağ Taraf</option>
                                        <option value="left" {{ ($template->accessory_position ?? '') === 'left' ? 'selected' : '' }}>Sol Taraf</option>
                                        <option value="center" {{ ($template->accessory_position ?? '') === 'center' ? 'selected' : '' }}>Orta</option>
                                    </select>
                                </div>

                                <div class="space-y-2 pt-1">
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-[10px] text-gray-600 font-medium">
                                                Konum X: <span id="accOffsetX_val" class="font-bold text-amber-700">{{ $template->accessory_offset_x ?? 0 }}</span>
                                            </label>
                                            <button type="button" onclick="document.getElementById('accOffsetX').value=0; document.getElementById('accOffsetX_val').innerText='0'; redrawModel();" class="text-[10px] text-amber-700 hover:text-amber-900 bg-amber-100 hover:bg-amber-200 px-1.5 py-0.5 rounded flex items-center gap-1 transition" title="Konum X Sıfırla">
                                                <i class="fa-solid fa-rotate-left text-[9px]"></i> Sıfırla
                                            </button>
                                        </div>
                                        <input type="range" name="accessory_offset_x" id="accOffsetX" min="-10" max="10" step="0.1" value="{{ $template->accessory_offset_x ?? 0 }}" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('accOffsetX_val').innerText = this.value; redrawModel();">
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-[10px] text-gray-600 font-medium">
                                                Konum Y: <span id="accOffsetY_val" class="font-bold text-amber-700">{{ $template->accessory_offset_y ?? 0 }}</span>
                                            </label>
                                            <button type="button" onclick="document.getElementById('accOffsetY').value=0; document.getElementById('accOffsetY_val').innerText='0'; redrawModel();" class="text-[10px] text-amber-700 hover:text-amber-900 bg-amber-100 hover:bg-amber-200 px-1.5 py-0.5 rounded flex items-center gap-1 transition" title="Konum Y Sıfırla">
                                                <i class="fa-solid fa-rotate-left text-[9px]"></i> Sıfırla
                                            </button>
                                        </div>
                                        <input type="range" name="accessory_offset_y" id="accOffsetY" min="-5" max="10" step="0.1" value="{{ $template->accessory_offset_y ?? 0 }}" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('accOffsetY_val').innerText = this.value; redrawModel();">
                                    </div>

                                    <button type="button" onclick="resetAccessoryOffsets();" class="w-full mt-2 text-[11px] font-bold text-amber-800 bg-amber-100/90 hover:bg-amber-200 py-1.5 px-2 rounded-lg border border-amber-200 transition flex items-center justify-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-arrow-rotate-left"></i> Obje Konumunu Sıfırla (X:0, Y:0)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-6 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-sm shadow transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i> Değişiklikleri Kaydet
                </button>
            </form>
        </div>

        <!-- 3D Viewport Column -->
        <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm relative overflow-hidden flex flex-col min-h-[500px]">
            <div class="absolute top-4 left-4 z-10 bg-white/95 backdrop-blur px-3 py-1.5 rounded-lg border border-gray-100 flex items-center gap-2 text-xs font-bold text-gray-800">
                <span class="w-2.5 h-2.5 rounded-full bg-brand animate-pulse"></span>
                <span>3D Canlı Tasarım Önizleme</span>
            </div>
            <div id="studio3DContainer" class="flex-1 w-full bg-[#f9fafb] cursor-grab active:cursor-grabbing"></div>
        </div>

    </div>
</div>

<!-- 3D OBJECT DESIGNER & UPLOADER MODAL POP-UP -->
<div id="addAccessoryModal" class="fixed inset-0 z-[99999] bg-black/75 backdrop-blur-sm hidden items-center justify-center p-4 overflow-y-auto">
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <i class="fa-solid fa-cubes text-base"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base text-white leading-snug">3D Obje Tasarım & Yükleme Editörü</h3>
                    <p class="text-xs text-gray-400">Ahşap çerçeve içine özel 3D obje tasarlayın veya 3D dosya yükleyin</p>
                </div>
            </div>
            <button type="button" onclick="closeAddAccessoryModal()" class="text-gray-400 hover:text-white text-2xl font-bold w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/10 transition">&times;</button>
        </div>

        <!-- Modal Tabs -->
        <div class="flex border-b border-gray-200 bg-gray-50 px-6 pt-3 gap-2">
            <button type="button" id="tabDesignBtn" onclick="switchAccessoryModalTab('design')" class="px-4 py-2 text-xs font-bold rounded-t-lg border-b-2 border-amber-600 text-amber-700 bg-white shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-palette"></i> 3D Obje Tasarla (Parametrik)
            </button>
            <button type="button" id="tabUploadBtn" onclick="switchAccessoryModalTab('upload')" class="px-4 py-2 text-xs font-bold rounded-t-lg border-b-2 border-transparent text-gray-500 hover:text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up"></i> 3D Dosya Yükle (.GLB / .GLTF)
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto flex-1 space-y-5">
            
            <!-- TAB 1: PARAMETRIC DESIGNER -->
            <div id="modalTabDesign" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Form Controls -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Obje Adı *</label>
                            <input type="text" id="modalAccName" value="Özel Dekoratif Obje" class="w-full text-xs border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Obje Tasarım Şablonu</label>
                            <select id="modalAccPreset" class="w-full text-xs border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none font-medium" onchange="updateModal3DPreview()">
                                <option value="candle_holder" selected>🕯️ Nostaljik Şamdan & Canlı Mum (Işıklı)</option>
                                <option value="street_lamp">💡 Nostaljik Sokak Feneri</option>
                                <option value="wooden_clock">🕰️ Ahşap Masa Saati</option>
                                <option value="flower_vase">🪴 Saksılı Çiçek & Bitki</option>
                                <option value="mini_bookshelf">📚 Minyatür Kitap Dizimi</option>
                                <option value="abstract_sculpture">🗿 Modern Geometrik Heykel</option>
                            </select>
                        </div>

                        <!-- Colors -->
                        <div class="grid grid-cols-3 gap-2 pt-1">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 mb-1">Ana Gövde</label>
                                <input type="color" id="modalAccColor1" value="#c8a257" class="w-full h-8 rounded border border-gray-300 cursor-pointer p-0.5" onchange="updateModal3DPreview()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 mb-1">Detay / Metal</label>
                                <input type="color" id="modalAccColor2" value="#e5c158" class="w-full h-8 rounded border border-gray-300 cursor-pointer p-0.5" onchange="updateModal3DPreview()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 mb-1">Işık / Vurgu</label>
                                <input type="color" id="modalAccColor3" value="#ffaa33" class="w-full h-8 rounded border border-gray-300 cursor-pointer p-0.5" onchange="updateModal3DPreview()">
                            </div>
                        </div>

                        <!-- Scale Slider -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-bold text-gray-700">Obje Boyutu / Yükseklik</label>
                                <span id="modalAccScaleVal" class="text-xs font-bold text-amber-700">1.0x</span>
                            </div>
                            <input type="range" id="modalAccScale" min="0.5" max="1.8" step="0.05" value="1.0" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('modalAccScaleVal').innerText = this.value + 'x'; updateModal3DPreview();">
                        </div>
                    </div>

                    <!-- Mini 3D Preview Canvas -->
                    <div class="bg-gray-900 rounded-xl overflow-hidden border border-gray-800 relative flex flex-col h-64 shadow-inner">
                        <div class="absolute top-2 left-2 z-10 bg-black/60 backdrop-blur px-2.5 py-1 rounded text-[10px] text-amber-400 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Canlı Obje Önizleme
                        </div>
                        <div id="modal3DPreviewContainer" class="w-full h-full cursor-grab active:cursor-grabbing"></div>
                    </div>

                </div>
            </div>

            <!-- TAB 2: FILE UPLOAD -->
            <div id="modalTabUpload" class="hidden space-y-4">
                <div class="border-2 border-dashed border-gray-300 hover:border-amber-600 rounded-xl p-8 text-center bg-gray-50 transition cursor-pointer" onclick="document.getElementById('modalAccFileInput').click()">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3"></i>
                    <h4 class="text-sm font-bold text-gray-800">3D Model Dosyası Yükleyin</h4>
                    <p class="text-xs text-gray-500 mt-1">Desteklenen Formatlar: <strong>.GLB, .GLTF, .JSON</strong> (Max 25MB)</p>
                    <input type="file" id="modalAccFileInput" accept=".glb,.gltf,.json" class="hidden" onchange="handleModalAccFileUpload(event)">
                </div>
                <div id="modalFileUploadStatus" class="hidden p-3 bg-green-50 border border-green-200 rounded-lg text-xs text-green-800 font-medium flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-green-600 text-sm"></i>
                    <span id="modalFileNameText">Dosya yüklendi!</span>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
            <button type="button" onclick="closeAddAccessoryModal()" class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-gray-800 bg-white border border-gray-300 rounded-lg transition">İptal</button>
            <button type="button" onclick="saveModalCustomAccessory()" class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 rounded-lg shadow transition flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Objeyi Kaydet ve Şablona Ekle
            </button>
        </div>

    </div>
</div>

<script>
const customAccessoriesRegistry = {};
let modalUploadedObject = null;

function darkenColor(hex, percent) {
    if (!hex) return '#333333';
    hex = hex.replace('#', '');
    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
    if (hex.length !== 6) return '#333333';
    let num = parseInt(hex, 16);
    let amt = Math.round(2.55 * percent);
    let R = Math.max(0, (num >> 16) - amt);
    let G = Math.max(0, (num >> 8 & 0x00FF) - amt);
    let B = Math.max(0, (num & 0x0000FF) - amt);
    return '#' + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
}

function toggleAccessoryControls() {
    const chk = document.getElementById('hasAccessory');
    const box = document.getElementById('accessoryControls');
    if (box) {
        if (chk && chk.checked) {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }
}

function resetAccessoryOffsets() {
    const xInput = document.getElementById('accOffsetX');
    const yInput = document.getElementById('accOffsetY');
    if (xInput) xInput.value = 0;
    if (yInput) yInput.value = 0;
    const xVal = document.getElementById('accOffsetX_val');
    const yVal = document.getElementById('accOffsetY_val');
    if (xVal) xVal.innerText = '0';
    if (yVal) yVal.innerText = '0';
    redrawModel();
}

function updateWoodColorFromPicker(val) {
    const input = document.getElementById('woodTypeSelect');
    if (input) {
        input.value = val;
        redrawModel();
    }
}

function setWoodPreset(hex, name) {
    const picker = document.getElementById('woodColorPicker');
    const input = document.getElementById('woodTypeSelect');
    if (picker) picker.value = hex;
    if (input) input.value = hex;
    redrawModel();
}

function generateWoodTextures(woodType, rendererInstance) {
    const size = 2048;
    const colorCanvas = document.createElement('canvas'); colorCanvas.width = size; colorCanvas.height = size;
    const colorCtx = colorCanvas.getContext('2d');
    const bumpCanvas = document.createElement('canvas'); bumpCanvas.width = size; bumpCanvas.height = size;
    const bumpCtx = bumpCanvas.getContext('2d');
    const roughCanvas = document.createElement('canvas'); roughCanvas.width = size; roughCanvas.height = size;
    const roughCtx = roughCanvas.getContext('2d');

    let baseColor, lineColor, poreColor;
    if (woodType && (woodType.startsWith('#') || /^[0-9a-fA-F]{6}$/.test(woodType.replace('#','')))) {
        const hex = woodType.startsWith('#') ? woodType : '#' + woodType;
        baseColor = hex;
        lineColor = darkenColor(hex, 32);
        poreColor = darkenColor(hex, 54);
    } else if (woodType === 'Ceviz') {
        baseColor = '#4a3319'; lineColor = '#28170b'; poreColor = '#150b04';
    } else if (woodType === 'Meşe') {
        baseColor = '#a8896c'; lineColor = '#6b4d32'; poreColor = '#45301d';
    } else if (woodType === 'Çam') {
        baseColor = '#e3d3bd'; lineColor = '#b0926d'; poreColor = '#8c6f4b';
    } else if (woodType === 'Kiraz') {
        baseColor = '#8c462b'; lineColor = '#4d1e0d'; poreColor = '#300f04';
    } else {
        baseColor = woodType || '#ead9c3';
        lineColor = darkenColor(baseColor, 32);
        poreColor = darkenColor(baseColor, 54);
    }

    colorCtx.fillStyle = baseColor; colorCtx.fillRect(0, 0, size, size);
    bumpCtx.fillStyle = '#808080'; bumpCtx.fillRect(0, 0, size, size);
    roughCtx.fillStyle = '#656565'; roughCtx.fillRect(0, 0, size, size);

    for (let y = -100; y < size + 100; y += 6) {
        let freq = 0.0025 + (y % 17) * 0.0002;
        let amp = 24 + (y % 19) * 2.2;
        let phase = y * 0.015;
        colorCtx.beginPath(); bumpCtx.beginPath(); roughCtx.beginPath();
        colorCtx.moveTo(0, y); bumpCtx.moveTo(0, y); roughCtx.moveTo(0, y);
        for (let x = 0; x <= size; x += 15) {
            let dy = Math.sin(x * freq + phase) * amp + Math.cos(x * 0.008) * 6;
            colorCtx.lineTo(x, y + dy); bumpCtx.lineTo(x, y + dy); roughCtx.lineTo(x, y + dy);
        }
        const isMajorRing = (y % 24 === 0);
        colorCtx.strokeStyle = isMajorRing ? lineColor : poreColor;
        colorCtx.globalAlpha = isMajorRing ? 0.42 : 0.22;
        colorCtx.lineWidth = isMajorRing ? 4.5 : 2.0;
        colorCtx.stroke();
        bumpCtx.strokeStyle = isMajorRing ? '#050505' : '#303030';
        bumpCtx.globalAlpha = isMajorRing ? 0.65 : 0.35;
        bumpCtx.lineWidth = isMajorRing ? 5.0 : 2.2;
        bumpCtx.stroke();
        roughCtx.strokeStyle = '#e0e0e0';
        roughCtx.globalAlpha = isMajorRing ? 0.35 : 0.15;
        roughCtx.lineWidth = isMajorRing ? 4.0 : 2.0;
        roughCtx.stroke();
    }

    colorCtx.globalAlpha = 0.18; colorCtx.fillStyle = poreColor;
    bumpCtx.globalAlpha = 0.4; bumpCtx.fillStyle = '#0a0a0a';
    for (let i = 0; i < 600; i++) {
        let px = Math.random() * size; let py = Math.random() * size;
        let pw = 3 + Math.random() * 25; let ph = 1.2 + Math.random() * 2.2;
        colorCtx.fillRect(px, py, pw, ph); bumpCtx.fillRect(px, py, pw, ph);
    }

    colorCtx.globalAlpha = 1.0; bumpCtx.globalAlpha = 1.0; roughCtx.globalAlpha = 1.0;
    let knotX = size * 0.42; let knotY = size * 0.48;
    for (let r = 12; r < 80; r += 9) {
        colorCtx.beginPath(); bumpCtx.beginPath();
        colorCtx.ellipse(knotX, knotY, r * 2.8, r * 1.1, Math.PI / 14, 0, Math.PI * 2);
        bumpCtx.ellipse(knotX, knotY, r * 2.8, r * 1.1, Math.PI / 14, 0, Math.PI * 2);
        colorCtx.strokeStyle = lineColor; colorCtx.lineWidth = 2.8; colorCtx.globalAlpha = 0.3; colorCtx.stroke();
        bumpCtx.strokeStyle = '#101010'; bumpCtx.lineWidth = 3.2; bumpCtx.globalAlpha = 0.45; bumpCtx.stroke();
    }

    colorCtx.globalAlpha = 1.0; bumpCtx.globalAlpha = 1.0; roughCtx.globalAlpha = 1.0;
    const colorTex = new THREE.CanvasTexture(colorCanvas);
    const bumpTex = new THREE.CanvasTexture(bumpCanvas);
    const roughTex = new THREE.CanvasTexture(roughCanvas);
    colorTex.wrapS = THREE.RepeatWrapping; colorTex.wrapT = THREE.RepeatWrapping;
    bumpTex.wrapS = THREE.RepeatWrapping; bumpTex.wrapT = THREE.RepeatWrapping;
    roughTex.wrapS = THREE.RepeatWrapping; roughTex.wrapT = THREE.RepeatWrapping;
    if (rendererInstance && rendererInstance.capabilities) {
        const maxAniso = rendererInstance.capabilities.getMaxAnisotropy();
        colorTex.anisotropy = bumpTex.anisotropy = roughTex.anisotropy = maxAniso;
    }
    return { colorMap: colorTex, bumpMap: bumpTex, roughnessMap: roughTex };
}

function createPieceMaterial(woodTextures, bScale, pieceName) {
    return new THREE.MeshStandardMaterial({
        map: woodTextures.colorMap,
        bumpMap: woodTextures.bumpMap,
        bumpScale: bScale,
        roughnessMap: woodTextures.roughnessMap,
        roughness: 0.65,
        metalness: 0.02
    });
}

function createStreetLampGroup(targetHeight, c1, c2, c3) {
    const lampGroup = new THREE.Group();
    const scale = (targetHeight || 14) / 22.0;
    const metalMat = new THREE.MeshStandardMaterial({ color: c1 || 0x332c25, metalness: 0.85, roughness: 0.35 });
    const brassAccentMat = new THREE.MeshStandardMaterial({ color: c2 || 0x8a6e3e, metalness: 0.8, roughness: 0.4 });
    const glassMat = new THREE.MeshStandardMaterial({ color: c3 || 0xffbb44, transparent: true, opacity: 0.75, roughness: 0.15, emissive: c3 || 0xff8800, emissiveIntensity: 0.7 });
    const bulbMat = new THREE.MeshStandardMaterial({ color: 0xffffff, emissive: c3 || 0xffcc44, emissiveIntensity: 2.5, roughness: 0.1 });

    const baseBottom = new THREE.Mesh(new THREE.CylinderGeometry(1.5 * scale, 1.8 * scale, 0.5 * scale, 8), metalMat); baseBottom.position.y = 0.25 * scale; lampGroup.add(baseBottom);
    const baseMid = new THREE.Mesh(new THREE.CylinderGeometry(1.0 * scale, 1.4 * scale, 0.8 * scale, 8), metalMat); baseMid.position.y = 0.9 * scale; lampGroup.add(baseMid);
    const baseRing = new THREE.Mesh(new THREE.TorusGeometry(0.95 * scale, 0.12 * scale, 8, 16), brassAccentMat); baseRing.rotation.x = Math.PI / 2; baseRing.position.y = 1.3 * scale; lampGroup.add(baseRing);
    const stemY = 5.05 * scale;
    const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.35 * scale, 0.60 * scale, 7.5 * scale, 16), metalMat); stem.position.y = stemY; lampGroup.add(stem);
    const stemRing = new THREE.Mesh(new THREE.TorusGeometry(0.48 * scale, 0.1 * scale, 8, 16), brassAccentMat); stemRing.rotation.x = Math.PI / 2; stemRing.position.y = 6.25 * scale; lampGroup.add(stemRing);
    const collarY = 9.25 * scale;
    const collar = new THREE.Mesh(new THREE.CylinderGeometry(0.85 * scale, 0.4 * scale, 0.9 * scale, 6), metalMat); collar.position.y = collarY; lampGroup.add(collar);
    const glassY = 10.9 * scale;
    const glassMesh = new THREE.Mesh(new THREE.CylinderGeometry(1.3 * scale, 0.85 * scale, 2.4 * scale, 6), glassMat); glassMesh.position.y = glassY; lampGroup.add(glassMesh);
    const bulbMesh = new THREE.Mesh(new THREE.SphereGeometry(0.38 * scale, 16, 16), bulbMat); bulbMesh.position.y = glassY; lampGroup.add(bulbMesh);
    const warmLight = new THREE.PointLight(c3 || 0xffaa33, 2.4, 28 * scale, 1.8); warmLight.position.y = glassY; lampGroup.add(warmLight);
    const roofY = 12.6 * scale;
    const roof = new THREE.Mesh(new THREE.CylinderGeometry(0.2 * scale, 1.6 * scale, 1.0 * scale, 6), metalMat); roof.position.y = roofY; lampGroup.add(roof);
    const finialStem = new THREE.Mesh(new THREE.CylinderGeometry(0.12 * scale, 0.25 * scale, 0.6 * scale, 8), brassAccentMat); finialStem.position.y = 13.4 * scale; lampGroup.add(finialStem);
    const finialBall = new THREE.Mesh(new THREE.SphereGeometry(0.28 * scale, 12, 12), brassAccentMat); finialBall.position.y = 13.8 * scale; lampGroup.add(finialBall);
    return lampGroup;
}

function createWoodenClockGroup(targetHeight, c1, c2) {
    const clockGroup = new THREE.Group();
    const scale = (targetHeight || 14) / 22.0;
    const woodMat = new THREE.MeshStandardMaterial({ color: c1 || 0x5a3d28, roughness: 0.5 });
    const brassMat = new THREE.MeshStandardMaterial({ color: c2 || 0xc8a257, roughness: 0.3, metalness: 0.8 });
    const dialMat = new THREE.MeshStandardMaterial({ color: 0xfaf4e8 });
    const bodyMesh = new THREE.Mesh(new THREE.BoxGeometry(4.5 * scale, 7.0 * scale, 2.2 * scale), woodMat);
    bodyMesh.position.y = 3.5 * scale; clockGroup.add(bodyMesh);
    const bezelMesh = new THREE.Mesh(new THREE.TorusGeometry(1.6 * scale, 0.18 * scale, 8, 24), brassMat); bezelMesh.position.set(0, 4.34 * scale, 1.15 * scale); clockGroup.add(bezelMesh);
    const dialMesh = new THREE.Mesh(new THREE.CircleGeometry(1.55 * scale, 24), dialMat); dialMesh.position.set(0, 4.34 * scale, 1.18 * scale); clockGroup.add(dialMesh);
    return clockGroup;
}

function createFlowerVaseGroup(targetHeight, c1, c2, c3) {
    const plantGroup = new THREE.Group();
    const scale = (targetHeight || 14) / 22.0;
    const potMat = new THREE.MeshStandardMaterial({ color: c1 || 0xc46d4e });
    const leafMat = new THREE.MeshStandardMaterial({ color: c2 || 0x2e7d32 });
    const flowerMat = new THREE.MeshStandardMaterial({ color: c3 || 0xe91e63 });
    const potMesh = new THREE.Mesh(new THREE.CylinderGeometry(1.6 * scale, 1.1 * scale, 3.2 * scale, 16), potMat); potMesh.position.y = 1.6 * scale; plantGroup.add(potMesh);
    for (let i = 0; i < 9; i++) {
        const leafMesh = new THREE.Mesh(new THREE.SphereGeometry(0.75 * scale, 8, 8), leafMat);
        leafMesh.position.set(Math.cos(i) * 0.6 * scale, 3.8 * scale, Math.sin(i) * 0.6 * scale);
        leafMesh.scale.set(1.2, 0.4, 0.8); plantGroup.add(leafMesh);
    }
    for (let i = 0; i < 4; i++) {
        const flowerMesh = new THREE.Mesh(new THREE.SphereGeometry(0.45 * scale, 8, 8), flowerMat);
        flowerMesh.position.set(Math.cos(i * 1.5) * 0.5 * scale, 4.6 * scale, Math.sin(i * 1.5) * 0.5 * scale);
        plantGroup.add(flowerMesh);
    }
    return plantGroup;
}

function createMiniBookshelfGroup(targetHeight, c1) {
    const bookGroup = new THREE.Group();
    const scale = (targetHeight || 14) / 22.0;
    const woodMat = new THREE.MeshStandardMaterial({ color: c1 || 0x4a321f });
    const shelfMesh = new THREE.Mesh(new THREE.BoxGeometry(5.5 * scale, 0.4 * scale, 2.2 * scale), woodMat); shelfMesh.position.y = 0.2 * scale; bookGroup.add(shelfMesh);
    return bookGroup;
}

function createCandleHolderGroup(targetHeight, c1, c2, c3) {
    const group = new THREE.Group();
    const scale = (targetHeight || 14) / 22.0;
    const brassMat = new THREE.MeshStandardMaterial({ color: c1 || 0xc8a257, metalness: 0.85, roughness: 0.25 });
    const accentMat = new THREE.MeshStandardMaterial({ color: c2 || 0xe5c158, metalness: 0.9, roughness: 0.2 });
    const candleMat = new THREE.MeshStandardMaterial({ color: 0xfffcf5, roughness: 0.6 });
    const flameMat = new THREE.MeshStandardMaterial({ color: c3 || 0xff9900, emissive: c3 || 0xff8800, emissiveIntensity: 3.0, roughness: 0.1 });

    const base = new THREE.Mesh(new THREE.CylinderGeometry(1.8 * scale, 2.2 * scale, 0.6 * scale, 16), brassMat); base.position.y = 0.3 * scale; group.add(base);
    const baseRing = new THREE.Mesh(new THREE.TorusGeometry(1.9 * scale, 0.15 * scale, 8, 16), accentMat); baseRing.rotation.x = Math.PI/2; baseRing.position.y = 0.6 * scale; group.add(baseRing);
    const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.35 * scale, 0.7 * scale, 6.0 * scale, 16), brassMat); stem.position.y = 3.6 * scale; group.add(stem);
    const cup = new THREE.Mesh(new THREE.CylinderGeometry(1.2 * scale, 0.6 * scale, 1.2 * scale, 16), accentMat); cup.position.y = 7.2 * scale; group.add(cup);
    const candle = new THREE.Mesh(new THREE.CylinderGeometry(0.7 * scale, 0.7 * scale, 4.5 * scale, 16), candleMat); candle.position.y = 9.8 * scale; group.add(candle);
    const wick = new THREE.Mesh(new THREE.CylinderGeometry(0.06 * scale, 0.06 * scale, 0.6 * scale, 8), new THREE.MeshBasicMaterial({ color: 0x111111 })); wick.position.y = 12.35 * scale; group.add(wick);
    const flame = new THREE.Mesh(new THREE.ConeGeometry(0.35 * scale, 1.1 * scale, 12), flameMat); flame.position.y = 13.0 * scale; group.add(flame);
    const flameLight = new THREE.PointLight(c3 || 0xffaa33, 2.5, 25 * scale, 1.5); flameLight.position.y = 13.0 * scale; group.add(flameLight);
    return group;
}

function createAbstractSculptureGroup(targetHeight, c1, c2) {
    const group = new THREE.Group();
    const scale = (targetHeight || 14) / 22.0;
    const baseMat = new THREE.MeshStandardMaterial({ color: c1 || 0x222222, roughness: 0.3 });
    const metalMat = new THREE.MeshStandardMaterial({ color: c2 || 0xd4af37, metalness: 0.9, roughness: 0.15 });

    const pedestal = new THREE.Mesh(new THREE.BoxGeometry(3.5 * scale, 2.5 * scale, 3.5 * scale), baseMat); pedestal.position.y = 1.25 * scale; group.add(pedestal);
    const knot = new THREE.Mesh(new THREE.TorusKnotGeometry(1.8 * scale, 0.45 * scale, 64, 16), metalMat); knot.position.y = 5.2 * scale; group.add(knot);
    return group;
}

function applySinglePieceTimberUVs(geometry, L, T, D) {
    geometry.computeVertexNormals();
    const pos = geometry.attributes.position;
    const norm = geometry.attributes.normal;
    const uv = geometry.attributes.uv;
    if (!pos || !uv) return;

    const depthVal = D || T;

    for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i);
        const y = pos.getY(i);
        const z = pos.getZ(i);

        let nx = norm ? Math.abs(norm.getX(i)) : 0;
        let ny = norm ? Math.abs(norm.getY(i)) : 0;
        let nz = norm ? Math.abs(norm.getZ(i)) : 1;

        let u, v;
        if (nz >= nx && nz >= ny) {
            // Front & Back Faces (XY plane)
            u = (x + L / 2) / L;
            v = (y + T / 2) / T;
        } else if (ny >= nx && ny >= nz) {
            // Top & Bottom Faces (XZ plane)
            u = (x + L / 2) / L;
            v = (z + depthVal / 2) / depthVal;
        } else {
            // Side & Miter Faces (ZY plane)
            u = (z + depthVal / 2) / depthVal;
            v = (y + T / 2) / T;
        }

        u = Math.max(0.001, Math.min(0.999, u));
        v = Math.max(0.001, Math.min(0.999, v));
        uv.setXY(i, u, v);
    }
    uv.needsUpdate = true;
}

function createMiteredFramePiece(L, T, D, miterLeft, miterRight) {
    const shape = new THREE.Shape();
    const halfL = L / 2; const halfT = T / 2;
    shape.moveTo(-halfL, halfT); shape.lineTo(halfL, halfT);
    shape.lineTo(miterRight ? (halfL - T) : halfL, -halfT);
    shape.lineTo(miterLeft ? (-halfL + T) : -halfL, -halfT);
    shape.lineTo(-halfL, halfT);
    const geom = new THREE.ExtrudeGeometry(shape, { depth: D, bevelEnabled: false, curveSegments: 1 });
    geom.computeBoundingBox();
    geom.translate(0, 0, -0.5 * (geom.boundingBox.max.z - geom.boundingBox.min.z));
    applySinglePieceTimberUVs(geom, L, T, D);
    return geom;
}

// --- 3D RENDER ENGINE ---
let scene, camera, renderer, controls;
let currentModelGroup = new THREE.Group();
let outerGroup = null;
let customRotatingFrame = null;
let outerFrameMeshes = [];
let innerFrameMeshes = [];

const container3D = document.getElementById('studio3DContainer');

init3D();
redrawModel();

const inputs = [
    'frameWidth', 'frameHeight', 'frameDepth', 'frameThickness',
    'partTop', 'partBottom', 'partLeft', 'partRight',
    'innerWidth', 'innerHeight', 'innerDepth', 'innerBorder',
    'posX', 'posY', 'bumpScale', 'woodTypeSelect',
    'hasAccessory', 'accessoryType', 'accessoryPos', 'accOffsetX', 'accOffsetY'
];

inputs.forEach(id => {
    const el = document.getElementById(id);
    if(el) {
        el.addEventListener('input', redrawModel);
        el.addEventListener('change', redrawModel);
    }
});

function getViewportSize() {
    if (!container3D) return { w: 600, h: 500 };
    const r = container3D.getBoundingClientRect();
    const w = (r.width && r.width > 100) ? r.width : (container3D.clientWidth || 600);
    const h = (r.height && r.height > 100) ? r.height : (container3D.clientHeight || 500);
    return { w, h };
}

function init3D() {
    scene = new THREE.Scene();
    const { w, h } = getViewportSize();
    camera = new THREE.PerspectiveCamera(45, w / h, 0.1, 1000);
    camera.position.set(0, 0, 50);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    container3D.appendChild(renderer.domElement);

    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;

    scene.add(currentModelGroup);

    const ambient = new THREE.AmbientLight(0xffffff, 0.65);
    const keyLight = new THREE.DirectionalLight(0xffffff, 0.95);
    keyLight.position.set(25, 45, 35);
    keyLight.castShadow = true;

    const fillLight = new THREE.DirectionalLight(0xffeedd, 0.45);
    fillLight.position.set(-30, -20, 20);

    scene.add(ambient, keyLight, fillLight);

    const animate = () => {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    };
    animate();

    window.addEventListener('resize', onWindowResize);
    setTimeout(onWindowResize, 150);
}

function onWindowResize() {
    if (!container3D || !camera || !renderer) return;
    const { w, h } = getViewportSize();
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
}

function buildAccessoryGroup(type, targetH) {
    if (type === 'street_lamp') return createStreetLampGroup(targetH);
    if (type === 'wooden_clock') return createWoodenClockGroup(targetH);
    if (type === 'flower_vase') return createFlowerVaseGroup(targetH);
    if (type === 'mini_bookshelf') return createMiniBookshelfGroup(targetH);
    if (type === 'candle_holder') return createCandleHolderGroup(targetH);
    if (type === 'abstract_sculpture') return createAbstractSculptureGroup(targetH);
    
    if (customAccessoriesRegistry[type]) {
        const item = customAccessoriesRegistry[type];
        const scaledH = targetH * (item.scale || 1.0);
        if (item.isUploadedFile && item.threeObject) {
            return item.threeObject.clone();
        }
        if (item.preset === 'candle_holder') return createCandleHolderGroup(scaledH, item.c1, item.c2, item.c3);
        if (item.preset === 'street_lamp') return createStreetLampGroup(scaledH, item.c1, item.c2, item.c3);
        if (item.preset === 'wooden_clock') return createWoodenClockGroup(scaledH, item.c1, item.c2);
        if (item.preset === 'flower_vase') return createFlowerVaseGroup(scaledH, item.c1, item.c2, item.c3);
        if (item.preset === 'mini_bookshelf') return createMiniBookshelfGroup(scaledH, item.c1);
        if (item.preset === 'abstract_sculpture') return createAbstractSculptureGroup(scaledH, item.c1, item.c2);
    }
    return createStreetLampGroup(targetH);
}

function redrawModel() {
    while(currentModelGroup.children.length > 0) { 
        currentModelGroup.remove(currentModelGroup.children[0]); 
    }
    outerFrameMeshes = [];
    innerFrameMeshes = [];
    outerGroup = new THREE.Group();
    customRotatingFrame = null;

    const width = parseFloat(document.getElementById('frameWidth')?.value) || 22;
    const height = parseFloat(document.getElementById('frameHeight')?.value) || 28;
    const depth = parseFloat(document.getElementById('frameDepth')?.value) || 3.0;
    const thickness = parseFloat(document.getElementById('frameThickness')?.value) || 3.0;

    const innerW = parseFloat(document.getElementById('innerWidth')?.value) || 15;
    const innerH = parseFloat(document.getElementById('innerHeight')?.value) || 21;
    const innerD = parseFloat(document.getElementById('innerDepth')?.value) || 2.6;
    const innerB = parseFloat(document.getElementById('innerBorder')?.value) || 1.4;

    const px = parseFloat(document.getElementById('posX')?.value) || 0;
    const py = parseFloat(document.getElementById('posY')?.value) || 0;

    const woodType = document.getElementById('woodTypeSelect')?.value || 'Ceviz';
    const woodTextures = generateWoodTextures(woodType, renderer);
    const bScale = parseFloat(document.getElementById('bumpScale')?.value) || 0.45;

    const hasTop = document.getElementById('partTop')?.checked ?? true;
    const hasBottom = document.getElementById('partBottom')?.checked ?? true;
    const hasLeft = document.getElementById('partLeft')?.checked ?? true;
    const hasRight = document.getElementById('partRight')?.checked ?? true;

    if(hasTop) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasLeft, hasRight), createPieceMaterial(woodTextures, bScale, 'top'));
        mesh.position.y = height/2 - thickness/2; outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    if(hasBottom) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasRight, hasLeft), createPieceMaterial(woodTextures, bScale, 'bottom'));
        mesh.rotation.z = Math.PI; mesh.position.y = -height/2 + thickness/2; outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    if(hasLeft) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasBottom, hasTop), createPieceMaterial(woodTextures, bScale, 'left'));
        mesh.rotation.z = Math.PI / 2; mesh.position.x = -width/2 + thickness/2; outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    if(hasRight) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasTop, hasBottom), createPieceMaterial(woodTextures, bScale, 'right'));
        mesh.rotation.z = -Math.PI / 2; mesh.position.x = width/2 - thickness/2; outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }

    currentModelGroup.add(outerGroup);

    customRotatingFrame = new THREE.Group();
    customRotatingFrame.position.set(px, py, 0);

    const matInTop = createPieceMaterial(woodTextures, bScale, 'inner_top');
    const topIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), matInTop);
    topIn.position.y = innerH/2 - innerB/2;
    customRotatingFrame.add(topIn); innerFrameMeshes.push(topIn);

    const matInBot = createPieceMaterial(woodTextures, bScale, 'inner_bottom');
    const botIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), matInBot);
    botIn.rotation.z = Math.PI;
    botIn.position.y = -innerH/2 + innerB/2;
    customRotatingFrame.add(botIn); innerFrameMeshes.push(botIn);

    const matInLeft = createPieceMaterial(woodTextures, bScale, 'inner_left');
    const leftIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), matInLeft);
    leftIn.rotation.z = Math.PI / 2;
    leftIn.position.x = -innerW/2 + innerB/2;
    customRotatingFrame.add(leftIn); innerFrameMeshes.push(leftIn);

    const matInRight = createPieceMaterial(woodTextures, bScale, 'inner_right');
    const rightIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), matInRight);
    rightIn.rotation.z = -Math.PI / 2;
    rightIn.position.x = innerW/2 - innerB/2;
    customRotatingFrame.add(rightIn); innerFrameMeshes.push(rightIn);

    const matBacking = createPieceMaterial(woodTextures, bScale, 'backing');
    const backingGeom = new THREE.BoxGeometry(innerW - innerB*1.5, innerH - innerB*1.5, 0.15);
    const backing = new THREE.Mesh(backingGeom, matBacking);
    customRotatingFrame.add(backing); innerFrameMeshes.push(backing);

    const pinMat = new THREE.MeshStandardMaterial({ color: 0xcccccc, metalness: 0.9, roughness: 0.2 });
    
    const innerEdgeTop = py + (innerH / 2);
    const outerTargetTop = (height / 2) - (thickness / 2);
    const lenTop = Math.max(0.15, outerTargetTop - innerEdgeTop);
    const localYTop = (innerH / 2) + (lenTop / 2);

    const pinTopGeo = new THREE.CylinderGeometry(0.18, 0.18, lenTop, 16);
    const pinTop = new THREE.Mesh(pinTopGeo, pinMat);
    pinTop.position.set(0, localYTop, 0);

    const innerEdgeBot = py - (innerH / 2);
    const outerTargetBot = -(height / 2) + (thickness / 2);
    const lenBot = Math.max(0.15, innerEdgeBot - outerTargetBot);
    const localYBot = -(innerH / 2) - (lenBot / 2);

    const pinBotGeo = new THREE.CylinderGeometry(0.18, 0.18, lenBot, 16);
    const pinBottom = new THREE.Mesh(pinBotGeo, pinMat);
    pinBottom.position.set(0, localYBot, 0);

    customRotatingFrame.add(pinTop, pinBottom);
    currentModelGroup.add(customRotatingFrame);

    const hasAccessory = document.getElementById('hasAccessory')?.checked;
    const accessoryType = document.getElementById('accessoryType')?.value || 'street_lamp';
    const accessoryPos = document.getElementById('accessoryPos')?.value || 'right';
    const accOffsetX = parseFloat(document.getElementById('accOffsetX')?.value || 0);
    const accOffsetY = parseFloat(document.getElementById('accOffsetY')?.value || 0);

    if (hasAccessory) {
        const targetH = Math.min(height * 0.65, 18);
        const accGroup = buildAccessoryGroup(accessoryType, targetH);
        if (accGroup) {
            const bottomBoardY = -height/2 + thickness;
            let posX = (accessoryPos === 'right') ? width/2 - thickness * 2.2 : (accessoryPos === 'left' ? -width/2 + thickness * 2.2 : 0);
            accGroup.position.set(posX + accOffsetX, bottomBoardY + accOffsetY, depth * 0.1);
            currentModelGroup.add(accGroup);
        }
    }
}

// --- MODAL CONTROLLER & MINI 3D PREVIEW ENGINE ---
let modalScene, modalCamera, modalRenderer, modalControls;
let modalPreviewGroup = new THREE.Group();

function openAddAccessoryModal() {
    const modal = document.getElementById('addAccessoryModal');
    if (modal) {
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { initModal3DPreview(); updateModal3DPreview(); }, 120);
    }
}

function closeAddAccessoryModal() {
    const modal = document.getElementById('addAccessoryModal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
}

function switchAccessoryModalTab(tab) {
    const dBtn = document.getElementById('tabDesignBtn');
    const uBtn = document.getElementById('tabUploadBtn');
    const dSec = document.getElementById('modalTabDesign');
    const uSec = document.getElementById('modalTabUpload');

    if (tab === 'design') {
        dBtn.className = 'px-4 py-2 text-xs font-bold rounded-t-lg border-b-2 border-amber-600 text-amber-700 bg-white shadow-sm flex items-center gap-2';
        uBtn.className = 'px-4 py-2 text-xs font-bold rounded-t-lg border-b-2 border-transparent text-gray-500 hover:text-gray-800 flex items-center gap-2';
        dSec.classList.remove('hidden'); uSec.classList.add('hidden');
    } else {
        uBtn.className = 'px-4 py-2 text-xs font-bold rounded-t-lg border-b-2 border-amber-600 text-amber-700 bg-white shadow-sm flex items-center gap-2';
        dBtn.className = 'px-4 py-2 text-xs font-bold rounded-t-lg border-b-2 border-transparent text-gray-500 hover:text-gray-800 flex items-center gap-2';
        uSec.classList.remove('hidden'); dSec.classList.add('hidden');
    }
}

function initModal3DPreview() {
    const container = document.getElementById('modal3DPreviewContainer');
    if (!container || modalRenderer) return;
    const w = container.clientWidth || 300; const h = container.clientHeight || 250;
    modalScene = new THREE.Scene();
    modalCamera = new THREE.PerspectiveCamera(45, w / h, 0.1, 1000);
    modalCamera.position.set(0, 8, 22);
    modalRenderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    modalRenderer.setSize(w, h);
    container.appendChild(modalRenderer.domElement);
    modalControls = new THREE.OrbitControls(modalCamera, modalRenderer.domElement);
    modalScene.add(modalPreviewGroup, new THREE.AmbientLight(0xffffff, 0.7));
    const light = new THREE.DirectionalLight(0xffffff, 0.9); light.position.set(15, 25, 20); modalScene.add(light);
    const animate = () => { requestAnimationFrame(animate); modalControls.update(); modalRenderer.render(modalScene, modalCamera); };
    animate();
}

function updateModal3DPreview() {
    if (!modalScene) return;
    while(modalPreviewGroup.children.length > 0) modalPreviewGroup.remove(modalPreviewGroup.children[0]);
    const preset = document.getElementById('modalAccPreset')?.value;
    const c1 = document.getElementById('modalAccColor1')?.value;
    const c2 = document.getElementById('modalAccColor2')?.value;
    const c3 = document.getElementById('modalAccColor3')?.value;
    const scale = parseFloat(document.getElementById('modalAccScale')?.value || 1.0);
    let group = null;
    const targetH = 14 * scale;
    if (preset === 'candle_holder') group = createCandleHolderGroup(targetH, c1, c2, c3);
    else if (preset === 'street_lamp') group = createStreetLampGroup(targetH, c1, c2, c3);
    else if (preset === 'wooden_clock') group = createWoodenClockGroup(targetH, c1, c2);
    else if (preset === 'flower_vase') group = createFlowerVaseGroup(targetH, c1, c2, c3);
    else if (preset === 'mini_bookshelf') group = createMiniBookshelfGroup(targetH, c1);
    else if (preset === 'abstract_sculpture') group = createAbstractSculptureGroup(targetH, c1, c2);
    if (group) { group.position.set(0, -targetH/2, 0); modalPreviewGroup.add(group); }
}

function saveModalCustomAccessory() {
    const name = document.getElementById('modalAccName')?.value || 'Özel 3D Obje';
    const preset = document.getElementById('modalAccPreset')?.value;
    const id = 'custom_' + Date.now();
    customAccessoriesRegistry[id] = {
        name: name,
        preset: preset,
        c1: document.getElementById('modalAccColor1')?.value,
        c2: document.getElementById('modalAccColor2')?.value,
        c3: document.getElementById('modalAccColor3')?.value,
        scale: parseFloat(document.getElementById('modalAccScale')?.value || 1.0),
        threeObject: modalUploadedObject
    };
    const select = document.getElementById('accessoryType');
    const opt = document.createElement('option'); opt.value = id; opt.innerText = '✨ ' + name; opt.selected = true;
    select.appendChild(opt);
    closeAddAccessoryModal(); redrawModel();
}

function handleModalAccFileUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    const statusText = document.getElementById('modalFileNameText');
    document.getElementById('modalFileUploadStatus').classList.remove('hidden');
    statusText.innerText = 'Yükleniyor: ' + file.name;
    const reader = new FileReader();
    reader.onload = function(evt) {
        const loader = new THREE.GLTFLoader();
        loader.parse(evt.target.result, '', (gltf) => { modalUploadedObject = gltf.scene; statusText.innerText = 'Başarılı!'; });
    };
    reader.readAsArrayBuffer(file);
}

function preventSpamSubmit(form) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn && !btn.disabled) {
        btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Kaydediliyor...';
    }
}
</script>
@endsection
