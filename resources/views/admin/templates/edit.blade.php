@extends('layouts.admin')

@section('title', '3D Çerçeve Şablonu Düzenle - AhşapEvim')

@section('header', '3D Çerçeve Şablonu Düzenle')

@section('content')
<!-- Three.js Loaders -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

<div class="bg-gray-50 min-h-[calc(100vh-140px)] flex flex-col">
    <div class="container mx-auto px-4 py-6 flex-grow flex flex-col lg:flex-row gap-6">
        
        <!-- Sidebar Controls / Form -->
        <div class="w-full lg:w-96 flex-shrink-0 flex flex-col gap-4">
            
            <form action="{{ route('admin.templates.update', $template->id) }}" method="POST" id="templateForm" class="space-y-4">
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
                            <label class="block text-xs font-bold text-gray-700 mb-1">Damar/Bump Kabartma Pürüzü</label>
                            <div class="flex items-center gap-3">
                                <input type="range" name="bump_scale" id="bumpScale" min="0" max="0.5" step="0.01" value="{{ $template->bump_scale }}" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('bumpVal').innerText = this.value">
                                <span id="bumpVal" class="text-xs font-bold text-gray-600 w-8">{{ $template->bump_scale }}</span>
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

<script>
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

// --- WOOD TEXTURE GENERATOR ---
function generateWoodTexture(woodType, rendererInstance) {
    const canvas = document.createElement('canvas');
    canvas.width = 1024;
    canvas.height = 1024;
    const ctx = canvas.getContext('2d');

    let baseColor, lineColor, poreColor;
    if (woodType && (woodType.startsWith('#') || /^[0-9a-fA-F]{6}$/.test(woodType.replace('#','')))) {
        const hex = woodType.startsWith('#') ? woodType : '#' + woodType;
        baseColor = hex;
        lineColor = darkenColor(hex, 25);
        poreColor = darkenColor(hex, 45);
    } else if (woodType === 'Ceviz') {
        baseColor = '#4a3319';
        lineColor = '#2b1b0e';
        poreColor = '#1d1209';
    } else if (woodType === 'Meşe') {
        baseColor = '#a8896c';
        lineColor = '#72553b';
        poreColor = '#503a27';
    } else if (woodType === 'Çam') {
        baseColor = '#e3d3bd';
        lineColor = '#ba9e7d';
        poreColor = '#a68865';
    } else if (woodType === 'Kiraz') {
        baseColor = '#8c462b';
        lineColor = '#562512';
        poreColor = '#3c180a';
    } else {
        baseColor = woodType || '#ead9c3';
        lineColor = darkenColor(baseColor, 25);
        poreColor = darkenColor(baseColor, 45);
    }

    // Base color
    ctx.fillStyle = baseColor;
    ctx.fillRect(0, 0, 1024, 1024);

    // 1. Dark micro-pores (fibers)
    ctx.fillStyle = poreColor;
    ctx.globalAlpha = 0.25;
    for (let i = 0; i < 60000; i++) {
        let px = Math.random() * 1024;
        let py = Math.random() * 1024;
        let pw = 2 + Math.random() * 4;
        let ph = 1 + Math.random() * 1.2;
        ctx.fillRect(px, py, pw, ph);
    }

    // 2. Light wood fibers for realistic raw roughness
    ctx.fillStyle = '#ffffff';
    ctx.globalAlpha = 0.12;
    for (let i = 0; i < 25000; i++) {
        let px = Math.random() * 1024;
        let py = Math.random() * 1024;
        let pw = 3 + Math.random() * 6;
        let ph = 0.8 + Math.random() * 0.8;
        ctx.fillRect(px, py, pw, ph);
    }
    ctx.globalAlpha = 1.0;

    // Soft grain variation rings (horizontal waves)
    ctx.strokeStyle = poreColor;
    ctx.globalAlpha = 0.12;
    ctx.lineWidth = 16;
    for (let i = -200; i < 1224; i += 45) {
        ctx.beginPath();
        let y = i;
        ctx.moveTo(0, y);
        let freq = 0.003;
        let amp = 35;
        let phase = i * 0.05;
        for (let x = 0; x <= 1024; x += 30) {
            let offset = Math.sin(x * freq + phase) * amp;
            ctx.lineTo(x, y + offset);
        }
        ctx.stroke();
    }
    ctx.globalAlpha = 1.0;

    // Sharp horizontal grain lines
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = 2.4;
    for (let i = -200; i < 1224; i += 12) {
        ctx.beginPath();
        let y = i;
        ctx.moveTo(0, y);
        
        let frequency = 0.004 + Math.random() * 0.003;
        let amplitude = 22 + Math.random() * 22;
        let phase = Math.random() * Math.PI;

        for (let x = 0; x <= 1024; x += 15) {
            let offset = Math.sin(x * frequency + phase) * amplitude;
            offset += (Math.random() - 0.5) * 1.5;
            ctx.lineTo(x, y + offset);
        }
        ctx.stroke();
    }

    // Horizontal knots (budaklar)
    ctx.lineWidth = 1.6;
    for (let k = 0; k < 3; k++) {
        let knotX = Math.random() * 624 + 200;
        let knotY = Math.random() * 624 + 200;
        let knotR = 30 + Math.random() * 40;
        
        for (let r = 8; r < knotR; r += 8) {
            ctx.beginPath();
            ctx.ellipse(knotX, knotY, r * 2.3, r, Math.PI / (6 + Math.random() * 4), 0, Math.PI * 2);
            ctx.stroke();
        }
    }

    const texture = new THREE.CanvasTexture(canvas);
    texture.wrapS = THREE.RepeatWrapping;
    texture.wrapT = THREE.RepeatWrapping;
    if (rendererInstance && rendererInstance.capabilities) {
        texture.anisotropy = rendererInstance.capabilities.getMaxAnisotropy();
    }
    return texture;
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
redrawModel(); // Build first time

// Set up listeners on all inputs to automatically update the 3D scene on change
const inputs = [
    'frameWidth', 'frameHeight', 'frameDepth', 'frameThickness',
    'partTop', 'partBottom', 'partLeft', 'partRight',
    'innerWidth', 'innerHeight', 'innerDepth', 'innerBorder',
    'posX', 'posY', 'bumpScale', 'woodTypeSelect'
];

inputs.forEach(id => {
    const el = document.getElementById(id);
    if(el) {
        el.addEventListener('input', redrawModel);
        el.addEventListener('change', redrawModel);
    }
});

function init3D() {
    scene = new THREE.Scene();
    const rect = container3D.getBoundingClientRect();
    camera = new THREE.PerspectiveCamera(45, rect.width / (rect.height || 500), 0.1, 1000);
    camera.position.set(0, 0, 50);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(rect.width, rect.height || 500);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    container3D.appendChild(renderer.domElement);

    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;

    scene.add(currentModelGroup);

    // Lightings
    const ambient = new THREE.AmbientLight(0xffffff, 0.65);
    const keyLight = new THREE.DirectionalLight(0xffffff, 0.85);
    keyLight.position.set(25, 45, 30);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.width = 1024;
    keyLight.shadow.mapSize.height = 1024;
    scene.add(ambient, keyLight);

    const animate = () => {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    };
    animate();

    window.addEventListener('resize', () => {
        const r = container3D.getBoundingClientRect();
        camera.aspect = r.width / r.height;
        camera.updateProjectionMatrix();
        renderer.setSize(r.width, r.height);
    });
}

function createMiteredFramePiece(L, T, D, miterLeft, miterRight) {
    const shape = new THREE.Shape();
    const halfL = L / 2;
    const halfT = T / 2;
    
    shape.moveTo(-halfL, halfT);
    shape.lineTo(halfL, halfT);
    
    const rightInnerX = miterRight ? (halfL - T) : halfL;
    shape.lineTo(rightInnerX, -halfT);
    
    const leftInnerX = miterLeft ? (-halfL + T) : -halfL;
    shape.lineTo(leftInnerX, -halfT);
    
    shape.lineTo(-halfL, halfT);

    const geom = new THREE.ExtrudeGeometry(shape, { depth: D, bevelEnabled: false, curveSegments: 1 });
    geom.computeBoundingBox();
    const zOffset = -0.5 * (geom.boundingBox.max.z - geom.boundingBox.min.z);
    geom.translate(0, 0, zOffset);
    return geom;
}

function redrawModel() {
    // Clear old elements
    while(currentModelGroup.children.length > 0) { 
        currentModelGroup.remove(currentModelGroup.children[0]); 
    }
    outerFrameMeshes = [];
    innerFrameMeshes = [];
    outerGroup = new THREE.Group();
    customRotatingFrame = null;

    const width = parseFloat(document.getElementById('frameWidth').value) || 22;
    const height = parseFloat(document.getElementById('frameHeight').value) || 28;
    const depth = parseFloat(document.getElementById('frameDepth').value) || 3.0;
    const thickness = parseFloat(document.getElementById('frameThickness').value) || 3.0;

    const innerW = parseFloat(document.getElementById('innerWidth').value) || 15;
    const innerH = parseFloat(document.getElementById('innerHeight').value) || 21;
    const innerD = parseFloat(document.getElementById('innerDepth').value) || 2.6;
    const innerB = parseFloat(document.getElementById('innerBorder').value) || 1.4;

    const px = parseFloat(document.getElementById('posX').value) || 0;
    const py = parseFloat(document.getElementById('posY').value) || 0;

    const woodType = document.getElementById('woodTypeSelect').value;

    // Materials map based on selected wood type
    const woodTexture = generateWoodTexture(woodType, renderer);
    const bScale = parseFloat(document.getElementById('bumpScale').value) || 0.08;
    const materialObj = new THREE.MeshStandardMaterial({ 
        map: woodTexture, 
        bumpMap: woodTexture,
        bumpScale: bScale,
        roughness: 0.88,
        metalness: 0.0
    });

    const hasTop = document.getElementById('partTop').checked;
    const hasBottom = document.getElementById('partBottom').checked;
    const hasLeft = document.getElementById('partLeft').checked;
    const hasRight = document.getElementById('partRight').checked;

    // Render Outer parts
    if(hasTop) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasLeft, hasRight), materialObj);
        mesh.position.y = height/2 - thickness/2;
        outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    if(hasBottom) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasRight, hasLeft), materialObj);
        mesh.rotation.z = Math.PI;
        mesh.position.y = -height/2 + thickness/2;
        outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    if(hasLeft) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasBottom, hasTop), materialObj);
        mesh.rotation.z = Math.PI / 2;
        mesh.position.x = -width/2 + thickness/2;
        outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    if(hasRight) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasTop, hasBottom), materialObj);
        mesh.rotation.z = -Math.PI / 2;
        mesh.position.x = width/2 - thickness/2;
        outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }

    currentModelGroup.add(outerGroup);

    // Inner rotating frame
    customRotatingFrame = new THREE.Group();
    customRotatingFrame.position.set(px, py, 0);

    const topIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), materialObj);
    topIn.position.y = innerH/2 - innerB/2;
    customRotatingFrame.add(topIn); innerFrameMeshes.push(topIn);

    const botIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), materialObj);
    botIn.rotation.z = Math.PI;
    botIn.position.y = -innerH/2 + innerB/2;
    customRotatingFrame.add(botIn); innerFrameMeshes.push(botIn);

    const leftIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), materialObj);
    leftIn.rotation.z = Math.PI / 2;
    leftIn.position.x = -innerW/2 + innerB/2;
    customRotatingFrame.add(leftIn); innerFrameMeshes.push(leftIn);

    const rightIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), materialObj);
    rightIn.rotation.z = -Math.PI / 2;
    rightIn.position.x = innerW/2 - innerB/2;
    customRotatingFrame.add(rightIn); innerFrameMeshes.push(rightIn);

    // Card Backing (Picture mesh backing)
    const backingGeom = new THREE.BoxGeometry(innerW - innerB*1.5, innerH - innerB*1.5, 0.15);
    const backing = new THREE.Mesh(backingGeom, materialObj);
    customRotatingFrame.add(backing); innerFrameMeshes.push(backing);

    // Metallic Pivot Pins (Orta Dönme Pinleri)
    const pinMat = new THREE.MeshStandardMaterial({ color: 0xcccccc, metalness: 0.9, roughness: 0.2 });
    
    // Top Pin
    const innerEdgeTop = py + (innerH / 2);
    const outerTargetTop = (height / 2) - (thickness / 2);
    const lenTop = Math.max(0.15, outerTargetTop - innerEdgeTop);
    const localYTop = (innerH / 2) + (lenTop / 2);

    const pinTopGeo = new THREE.CylinderGeometry(0.18, 0.18, lenTop, 16);
    const pinTop = new THREE.Mesh(pinTopGeo, pinMat);
    pinTop.position.set(0, localYTop, 0);

    // Bottom Pin
    const innerEdgeBot = py - (innerH / 2);
    const outerTargetBot = -(height / 2) + (thickness / 2);
    const lenBot = Math.max(0.15, innerEdgeBot - outerTargetBot);
    const localYBot = -(innerH / 2) - (lenBot / 2);

    const pinBotGeo = new THREE.CylinderGeometry(0.18, 0.18, lenBot, 16);
    const pinBottom = new THREE.Mesh(pinBotGeo, pinMat);
    pinBottom.position.set(0, localYBot, 0);

    customRotatingFrame.add(pinTop, pinBottom);

    currentModelGroup.add(customRotatingFrame);
}
</script>
@endsection
