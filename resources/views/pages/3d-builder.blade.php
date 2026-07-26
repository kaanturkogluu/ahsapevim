@extends('layouts.app')

@section('title', '3D Çerçeve Oluşturucu - AhşapEvim')

@section('content')
<!-- Three.js Loaders -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/exporters/GLTFExporter.js"></script>

<div class="bg-gray-50 min-h-[calc(100vh-140px)] flex flex-col">
    <div class="container mx-auto px-4 py-6 flex-grow flex flex-col lg:flex-row gap-6">
        
        <!-- Sidebar Controls -->
        <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-4">
            
            <!-- STEP 1: OUTER FRAME -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
                <div class="absolute -top-3 -left-3 w-8 h-8 bg-brand text-white font-bold rounded-full flex items-center justify-center shadow">1</div>
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 ml-2">Dış Çerçeve</h2>
                
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Genişlik (X)</label>
                            <input type="number" id="frameWidth" value="20" min="5" max="100" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Yükseklik (Y)</label>
                            <input type="number" id="frameHeight" value="25" min="5" max="100" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Derinlik (Z)</label>
                            <input type="number" id="frameDepth" value="3" min="1" max="20" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Et Kalınlığı</label>
                            <input type="number" id="frameThickness" value="3" min="1" max="10" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                        </div>
                    </div>

                    <div class="mt-4 border-t border-gray-100 pt-3">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Çizilecek Parçalar (U veya L şekli için)</label>
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <label class="flex items-center gap-2"><input type="checkbox" id="partTop" checked class="rounded text-brand focus:ring-brand"> Üst</label>
                            <label class="flex items-center gap-2"><input type="checkbox" id="partBottom" checked class="rounded text-brand focus:ring-brand"> Alt</label>
                            <label class="flex items-center gap-2"><input type="checkbox" id="partLeft" checked class="rounded text-brand focus:ring-brand"> Sol</label>
                            <label class="flex items-center gap-2"><input type="checkbox" id="partRight" checked class="rounded text-brand focus:ring-brand"> Sağ</label>
                        </div>
                    </div>

                    <button id="btnBuildOuter" class="w-full mt-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-md text-sm font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-hammer"></i> Dış Çerçeveyi Çiz
                    </button>
                </div>
            </div>

            <!-- STEP 2: INNER FRAME -->
            <div id="step2Panel" class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative opacity-50 pointer-events-none transition duration-300">
                <div class="absolute -top-3 -left-3 w-8 h-8 bg-gray-400 text-white font-bold rounded-full flex items-center justify-center shadow" id="step2Badge">2</div>
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 ml-2">İç Çerçeve (Dönen)</h2>
                
                <div class="space-y-3">
                    <p class="text-[10px] text-gray-500 mb-2">Ölçüler dış çerçevenin boşluğuna göre otomatik hesaplanmıştır.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">İç Genişlik</label>
                            <input type="number" id="innerWidth" value="13" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">İç Yükseklik</label>
                            <input type="number" id="innerHeight" value="18" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">İç Derinlik</label>
                            <input type="number" id="innerDepth" value="2.5" step="0.1" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Dış Duvar Payı (Resim Çerçevesi)</label>
                            <input type="number" id="innerBorder" value="1.0" step="0.1" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                        </div>
                    </div>

                    <div class="mt-2 border-t border-gray-100 pt-2">
                        <label class="block text-xs font-bold text-gray-700 mb-2">İç Çerçeve Yerleşimi (Konum)</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-gray-600 mb-1 flex justify-between">
                                    <span>Sağ / Sol Kaydır</span> <span id="posX_val" class="font-bold text-brand">0</span>
                                </label>
                                <input type="range" id="posX" min="-20" max="20" step="0.5" value="0" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('posX_val').innerText = this.value">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-600 mb-1 flex justify-between">
                                    <span>Yukarı / Aşağı Kaydır</span> <span id="posY_val" class="font-bold text-brand">0</span>
                                </label>
                                <input type="range" id="posY" min="-20" max="20" step="0.5" value="0" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('posY_val').innerText = this.value">
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 border-t border-gray-100 pt-2">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Dönme Ekseni Konumu (Pivot / Menteşe)</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-gray-600 mb-1 flex justify-between">
                                    <span>Menteşe X Ekseni</span> <span id="pivotX_val" class="font-bold text-brand">0</span>
                                </label>
                                <input type="range" id="pivotX" min="-20" max="20" step="0.5" value="0" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('pivotX_val').innerText = this.value">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-600 mb-1 flex justify-between">
                                    <span>Menteşe Y Ekseni</span> <span id="pivotY_val" class="font-bold text-brand">0</span>
                                </label>
                                <input type="range" id="pivotY" min="-20" max="20" step="0.5" value="0" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('pivotY_val').innerText = this.value">
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="block text-[10px] text-gray-600 mb-1 flex justify-between">
                                <span>İleri / Geri (Z - Çarpışmayı Önler)</span> <span id="pivotOffset_val" class="font-bold text-brand">0</span>
                            </label>
                            <input type="range" id="pivotOffset" min="-10" max="10" step="0.5" value="0" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('pivotOffset_val').innerText = this.value">
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1 text-red-600">Dönüş Ekseni (ÖNEMLİ)</label>
                        <select id="rotationAxis" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                            <option value="y">Y Ekseni (Sağdan Sola Döner - Dikey Çerçeve)</option>
                            <option value="x">X Ekseni (Aşağı Yukarı Döner - Yatay Çerçeve)</option>
                        </select>
                    </div>

                    <button id="btnBuildInner" class="w-full mt-4 py-2 bg-brand hover:bg-brand-dark text-white rounded-md text-sm font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus-circle"></i> İç Çerçeveyi Ekle
                    </button>
                </div>
            </div>

            <!-- Custom Photo Upload -->
            <div id="customPhotoControls" class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hidden">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-image text-brand mr-2"></i>Fotoğraflar</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Arka Plan Fotoğrafı (Mekan)</label>
                        <input type="file" id="bgImageInput" class="text-sm w-full mb-1" accept="image/*">
                        <p class="text-[10px] text-gray-500">Kendi duvar/masa fotoğrafınızı yükleyerek çerçeveyi mekanda görebilirsiniz.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Ön Yüz Fotoğrafı</label>
                        <input type="file" id="photoFrontInput" class="text-sm w-full mb-1" accept="image/*">
                        <select id="photoFrontRotation" class="w-full text-xs border-gray-300 rounded-md p-1 border">
                            <option value="0">Yön: Normal</option>
                            <option value="1.5708">90° Sağa Yatır</option>
                            <option value="3.14159">180° Ters</option>
                            <option value="-1.5708">90° Sola Yatır</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Arka Yüz Fotoğrafı</label>
                        <input type="file" id="photoBackInput" class="text-sm w-full mb-1" accept="image/*">
                        <select id="photoBackRotation" class="w-full text-xs border-gray-300 rounded-md p-1 border">
                            <option value="0">Yön: Normal</option>
                            <option value="1.5708">90° Sağa Yatır</option>
                            <option value="3.14159">180° Ters</option>
                            <option value="-1.5708">90° Sola Yatır</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-sliders text-brand mr-2"></i>Ayarlar</h2>
                <div class="space-y-4">
                    <div id="colorControls" class="hidden space-y-3 mb-4 pb-4 border-b border-gray-100">
                        <label class="block text-xs font-bold text-gray-700">Ahşap Dokusu (Renk)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 mb-1">Dış Çerçeve</label>
                                <input type="color" id="outerFrameColor" value="#4a2e1b" class="w-full h-8 rounded cursor-pointer border-0 p-0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 mb-1">İç Çerçeve</label>
                                <input type="color" id="innerFrameColor" value="#5c3a21" class="w-full h-8 rounded cursor-pointer border-0 p-0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Masa/Zemin Göster</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="showTable" class="sr-only peer" checked>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Otomatik Döndür</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="autoRotate" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                        </label>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- 3D Viewport -->
        <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm relative overflow-hidden flex flex-col min-h-[500px]">
            <div class="absolute top-4 right-4 z-10">
                <button id="btnExportGLB" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-bold shadow transition hidden flex items-center gap-2">
                    <i class="fa-solid fa-download"></i> Modeli İndir (.GLB)
                </button>
            </div>
            <div id="studio3DContainer" class="flex-1 w-full bg-[#f9fafb] bg-cover bg-center bg-no-repeat transition-all duration-300">
                <!-- Three.js Canvas goes here -->
            </div>
        </div>

    </div>
</div>

<script>
// --- 3D LOGIC ---
let scene, camera, renderer, controls;
let currentModelGroup = new THREE.Group();
let outerGroup = null;
let customRotatingFrame = null;
let customPhotoFront = null;
let customPhotoBack = null;
let outerFrameMeshes = [];
let innerFrameMeshes = [];

const container3D = document.getElementById('studio3DContainer');

init3D();

function init3D() {
    scene = new THREE.Scene();
    
    // Instead of a flat background color, we will build a 3D room environment
    // scene.background = new THREE.Color('#f9fafb');

    const rect = container3D.getBoundingClientRect();
    camera = new THREE.PerspectiveCamera(45, rect.width / rect.height, 0.1, 1500);
    camera.position.set(0, 0, 50);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, preserveDrawingBuffer: true });
    renderer.setSize(rect.width, rect.height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    container3D.appendChild(renderer.domElement);

    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.autoRotate = false;

    scene.add(currentModelGroup);

    // Lights
    const ambient = new THREE.AmbientLight(0xffffff, 0.6);
    const key = new THREE.DirectionalLight(0xffffff, 0.8);
    key.position.set(30, 60, 40);
    key.castShadow = true;
    key.shadow.mapSize.width = 2048;
    key.shadow.mapSize.height = 2048;
    key.shadow.camera.near = 10;
    key.shadow.camera.far = 200;
    key.shadow.camera.left = -50;
    key.shadow.camera.right = 50;
    key.shadow.camera.top = 50;
    key.shadow.camera.bottom = -50;
    scene.add(ambient, key);

    // Transparent Shadow Catcher Plane (To cast shadows on the 2D CSS Background)
    const shadowMat = new THREE.ShadowMaterial({ opacity: 0.5 });
    const shadowGeo = new THREE.PlaneGeometry(500, 500);
    const shadowPlane = new THREE.Mesh(shadowGeo, shadowMat);
    shadowPlane.rotation.x = -Math.PI / 2;
    shadowPlane.position.y = -20; // Will be aligned dynamically
    shadowPlane.receiveShadow = true;
    shadowPlane.name = "shadow_plane";
    scene.add(shadowPlane);

    const animate = () => {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    };
    animate();

    window.addEventListener('resize', () => {
        const r = container3D.getBoundingClientRect();
        if(r.width === 0) return;
        camera.aspect = r.width / r.height;
        camera.updateProjectionMatrix();
        renderer.setSize(r.width, r.height);
    });

    // Custom Interaction for Rotating Frame
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    let isDraggingInnerFrame = false;
    let previousMousePos = { x: 0, y: 0 };

    container3D.addEventListener('pointerdown', (event) => {
        if (!customRotatingFrame) return;
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
        
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObject(customRotatingFrame, true);
        if (intersects.length > 0) {
            isDraggingInnerFrame = true;
            controls.enabled = false;
            previousMousePos = { x: event.clientX, y: event.clientY };
        }
    });

    container3D.addEventListener('pointermove', (event) => {
        if (isDraggingInnerFrame && customRotatingFrame) {
            const deltaX = event.clientX - previousMousePos.x;
            const deltaY = event.clientY - previousMousePos.y;
            
            const rotAxis = document.getElementById('rotationAxis').value;
            
            if (rotAxis === 'y') {
                customRotatingFrame.rotation.y += deltaX * 0.01;
            } else {
                customRotatingFrame.rotation.x += deltaY * 0.01;
            }
            
            previousMousePos = { x: event.clientX, y: event.clientY };
        }
    });

    const stopDrag = () => {
        isDraggingInnerFrame = false;
        controls.enabled = true;
    };
    container3D.addEventListener('pointerup', stopDrag);
    container3D.addEventListener('pointerleave', stopDrag);
}

// STEP 1: Build Outer Frame
document.getElementById('btnBuildOuter').addEventListener('click', () => {
    // Clear old model
    while(currentModelGroup.children.length > 0) { 
        currentModelGroup.remove(currentModelGroup.children[0]); 
    }
    outerFrameMeshes = [];
    innerFrameMeshes = [];
    outerGroup = new THREE.Group();
    outerGroup.name = "outer_frame";
    customRotatingFrame = null;
    
    document.getElementById('customPhotoControls').classList.add('hidden');
    document.getElementById('colorControls').classList.add('hidden');
    document.getElementById('btnExportGLB').classList.add('hidden');

    const width = parseFloat(document.getElementById('frameWidth').value) || 20;
    const height = parseFloat(document.getElementById('frameHeight').value) || 25;
    const depth = parseFloat(document.getElementById('frameDepth').value) || 3;
    const thickness = parseFloat(document.getElementById('frameThickness').value) || 3;

    if(thickness * 2 >= width || thickness * 2 >= height) {
        alert("Kenar kalınlığı çerçevenin kendisinden büyük olamaz!");
        return;
    }

    const materialObj = new THREE.MeshStandardMaterial({ color: 0x8b5a2b, roughness: 0.8 });

    // Üst
    if(document.getElementById('partTop').checked) {
        const mesh = new THREE.Mesh(new THREE.BoxGeometry(width, thickness, depth), materialObj);
        mesh.position.y = height/2 - thickness/2;
        outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    // Alt
    if(document.getElementById('partBottom').checked) {
        const mesh = new THREE.Mesh(new THREE.BoxGeometry(width, thickness, depth), materialObj);
        mesh.position.y = -height/2 + thickness/2;
        outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    // Sol
    const sideH = height - (thickness * 2);
    if(document.getElementById('partLeft').checked) {
        const mesh = new THREE.Mesh(new THREE.BoxGeometry(thickness, sideH, depth), materialObj);
        mesh.position.x = -width/2 + thickness/2;
        outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }
    // Sağ
    if(document.getElementById('partRight').checked) {
        const mesh = new THREE.Mesh(new THREE.BoxGeometry(thickness, sideH, depth), materialObj);
        mesh.position.x = width/2 - thickness/2;
        outerGroup.add(mesh); outerFrameMeshes.push(mesh);
    }

    currentModelGroup.add(outerGroup);
    applyInitialWoodTexture(outerFrameMeshes, document.getElementById('outerFrameColor').value);

    // Align shadow plane to the bottom of the frame
    const shadowPlane = scene.getObjectByName("shadow_plane");
    if(shadowPlane) {
        shadowPlane.position.y = -height/2; 
    }

    // Calculate Inner Defaults
    const innerGap = 0.5;
    document.getElementById('innerWidth').value = width - (thickness * 2) - (innerGap * 2);
    document.getElementById('innerHeight').value = height - (thickness * 2) - (innerGap * 2);

    // Activate Step 2
    const panel2 = document.getElementById('step2Panel');
    panel2.classList.remove('opacity-50', 'pointer-events-none');
    document.getElementById('step2Badge').className = "absolute -top-3 -left-3 w-8 h-8 bg-brand text-white font-bold rounded-full flex items-center justify-center shadow";

    const maxDim = Math.max(width, height);
    camera.position.set(0, 0, maxDim * 1.5);
    controls.target.set(0, 0, 0);
    controls.update();
});

// STEP 2: Build Inner Frame
document.getElementById('btnBuildInner').addEventListener('click', () => {
    if(!outerGroup) {
        alert("Önce dış çerçeveyi oluşturmalısınız!");
        return;
    }
    
    // Remove old inner frame if it exists
    if(customRotatingFrame) {
        currentModelGroup.remove(customRotatingFrame);
        innerFrameMeshes = [];
    }

    const inWidth = parseFloat(document.getElementById('innerWidth').value) || 13;
    const inHeight = parseFloat(document.getElementById('innerHeight').value) || 18;
    const inDepth = parseFloat(document.getElementById('innerDepth').value) || 2.5;
    const innerBorder = parseFloat(document.getElementById('innerBorder').value) || 1.0;
    
    // Position 
    const posX = parseFloat(document.getElementById('posX').value) || 0;
    const posY = parseFloat(document.getElementById('posY').value) || 0;

    // Pivot Offset
    const pivotX = parseFloat(document.getElementById('pivotX').value) || 0;
    const pivotY = parseFloat(document.getElementById('pivotY').value) || 0;
    const pivotOffset = parseFloat(document.getElementById('pivotOffset').value) || 0;

    customRotatingFrame = new THREE.Group();
    customRotatingFrame.name = "rotating_frame";
    const materialObj = new THREE.MeshStandardMaterial({ color: 0x5c3a21, roughness: 0.8 });

    // Create a 2D Shape with a hole for the inner frame to create a visible photo recess
    const shape = new THREE.Shape();
    const hw = inWidth / 2;
    const hh = inHeight / 2;
    shape.moveTo(-hw, -hh);
    shape.lineTo(hw, -hh);
    shape.lineTo(hw, hh);
    shape.lineTo(-hw, hh);
    shape.lineTo(-hw, -hh);

    // Hole
    if (innerBorder * 2 < inWidth && innerBorder * 2 < inHeight) {
        const holeW = hw - innerBorder;
        const holeH = hh - innerBorder;
        const hole = new THREE.Path();
        hole.moveTo(-holeW, -holeH);
        hole.lineTo(holeW, -holeH);
        hole.lineTo(holeW, holeH);
        hole.lineTo(-holeW, holeH);
        hole.lineTo(-holeW, -holeH);
        shape.holes.push(hole);
    }

    const extrudeSettings = { depth: inDepth, bevelEnabled: false, curveSegments: 4 };
    const innerGeom = new THREE.ExtrudeGeometry(shape, extrudeSettings);
    innerGeom.computeBoundingBox();
    const izOffset = -0.5 * (innerGeom.boundingBox.max.z - innerGeom.boundingBox.min.z);
    innerGeom.translate(0, 0, izOffset);

    const innerMesh = new THREE.Mesh(innerGeom, materialObj);
    customRotatingFrame.add(innerMesh);
    innerFrameMeshes.push(innerMesh);

    // Center divider so we can't see through the hole, this acts as the photo backing
    const divGeom = new THREE.BoxGeometry(inWidth - innerBorder * 1.5, inHeight - innerBorder * 1.5, 0.1);
    const divMesh = new THREE.Mesh(divGeom, materialObj);
    customRotatingFrame.add(divMesh);
    innerFrameMeshes.push(divMesh);

    // Photos fit inside the recess
    const photoW = inWidth - innerBorder * 2 - 0.1; 
    const photoH = inHeight - innerBorder * 2 - 0.1;
    const photoMat = new THREE.MeshStandardMaterial({ color: 0xeeeeee });

    const frontZ = 0.06; // placed just slightly in front of the center divider
    const backZ = -0.06;

    customPhotoFront = new THREE.Mesh(new THREE.PlaneGeometry(photoW, photoH), photoMat);
    customPhotoFront.name = "photo_front";
    customPhotoFront.position.z = frontZ;
    customRotatingFrame.add(customPhotoFront);

    customPhotoBack = new THREE.Mesh(new THREE.PlaneGeometry(photoW, photoH), photoMat);
    customPhotoBack.name = "photo_back";
    customPhotoBack.rotation.y = Math.PI;
    customPhotoBack.position.z = backZ;
    customRotatingFrame.add(customPhotoBack);

    // Rotation Pins (Menteşe/Pim)
    const pinMat = new THREE.MeshStandardMaterial({ color: 0x888888, metalness: 0.9, roughness: 0.2 });
    const rotAxis = document.getElementById('rotationAxis').value;
    
    const outWidth = parseFloat(document.getElementById('frameWidth').value) || 20;
    const outHeight = parseFloat(document.getElementById('frameHeight').value) || 25;
    const outThickness = parseFloat(document.getElementById('frameThickness').value) || 3;

    if (rotAxis === 'y') {
        // Top Pin
        const innerEdgeTop = posY + (inHeight / 2);
        const outerTargetTop = (outHeight / 2) - (outThickness / 2);
        const lenTop = Math.max(0.1, outerTargetTop - innerEdgeTop);
        const centerTop = innerEdgeTop + (lenTop / 2);
        const localYTop = centerTop - (posY + pivotY);
        
        const pinTopGeo = new THREE.CylinderGeometry(0.15, 0.15, lenTop, 16);
        const pinTop = new THREE.Mesh(pinTopGeo, pinMat);
        pinTop.position.set(0, localYTop, 0);
        
        // Bottom Pin
        const innerEdgeBot = posY - (inHeight / 2);
        const outerTargetBot = -(outHeight / 2) + (outThickness / 2);
        const lenBot = Math.max(0.1, innerEdgeBot - outerTargetBot);
        const centerBot = innerEdgeBot - (lenBot / 2);
        const localYBot = centerBot - (posY + pivotY);

        const pinBotGeo = new THREE.CylinderGeometry(0.15, 0.15, lenBot, 16);
        const pinBottom = new THREE.Mesh(pinBotGeo, pinMat);
        pinBottom.position.set(0, localYBot, 0);
        
        customRotatingFrame.add(pinTop, pinBottom);
    } else {
        // Left Pin
        const innerEdgeLeft = posX - (inWidth / 2);
        const outerTargetLeft = -(outWidth / 2) + (outThickness / 2);
        const lenLeft = Math.max(0.1, innerEdgeLeft - outerTargetLeft);
        const centerLeft = innerEdgeLeft - (lenLeft / 2);
        const localXLeft = centerLeft - (posX + pivotX);

        const pinLeftGeo = new THREE.CylinderGeometry(0.15, 0.15, lenLeft, 16);
        const pinLeft = new THREE.Mesh(pinLeftGeo, pinMat);
        pinLeft.rotation.z = Math.PI / 2;
        pinLeft.position.set(localXLeft, 0, 0);
        
        // Right Pin
        const innerEdgeRight = posX + (inWidth / 2);
        const outerTargetRight = (outWidth / 2) - (outThickness / 2);
        const lenRight = Math.max(0.1, outerTargetRight - innerEdgeRight);
        const centerRight = innerEdgeRight + (lenRight / 2);
        const localXRight = centerRight - (posX + pivotX);

        const pinRightGeo = new THREE.CylinderGeometry(0.15, 0.15, lenRight, 16);
        const pinRight = new THREE.Mesh(pinRightGeo, pinMat);
        pinRight.rotation.z = Math.PI / 2;
        pinRight.position.set(localXRight, 0, 0);
        
        customRotatingFrame.add(pinLeft, pinRight);
    }

    // Pivot and Position Offset Logic
    // We move the entire group by (pivotX + posX, pivotY + posY, pivotOffset), 
    // and move all its children backwards by (-pivotX, -pivotY, -pivotOffset) to maintain visual origin relative to group center
    customRotatingFrame.position.set(pivotX + posX, pivotY + posY, pivotOffset);
    innerMesh.position.set(-pivotX, -pivotY, -pivotOffset);
    divMesh.position.set(-pivotX, -pivotY, -pivotOffset);
    customPhotoFront.position.set(-pivotX, -pivotY, frontZ - pivotOffset);
    customPhotoBack.position.set(-pivotX, -pivotY, backZ - pivotOffset);

    currentModelGroup.add(customRotatingFrame);
    applyInitialWoodTexture(innerFrameMeshes, document.getElementById('innerFrameColor').value);

    // Show export and photo controls
    document.getElementById('customPhotoControls').classList.remove('hidden');
    document.getElementById('colorControls').classList.remove('hidden');
    document.getElementById('btnExportGLB').classList.remove('hidden');
});

// UI Listeners
document.getElementById('autoRotate').addEventListener('change', e => controls.autoRotate = e.target.checked);
document.getElementById('showTable').addEventListener('change', e => {
    const shadowPlane = scene.getObjectByName("shadow_plane");
    if(shadowPlane) shadowPlane.visible = e.target.checked;
    
    const container = document.getElementById('studio3DContainer');
    if(!e.target.checked) {
        container.style.backgroundImage = 'none';
        container.classList.add('bg-[#f9fafb]');
    } else {
        container.classList.remove('bg-[#f9fafb]');
        // If there's an uploaded image, it stays because of inline style, otherwise it's transparent
    }
});

// Background Image Upload Logic
document.getElementById('bgImageInput').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        const container = document.getElementById('studio3DContainer');
        container.style.backgroundImage = `url(${event.target.result})`;
        container.classList.remove('bg-[#f9fafb]');
        document.getElementById('showTable').checked = true;
        const shadowPlane = scene.getObjectByName("shadow_plane");
        if(shadowPlane) shadowPlane.visible = true;
    };
    reader.readAsDataURL(file);
});
document.getElementById('outerFrameColor').addEventListener('input', e => applyInitialWoodTexture(outerFrameMeshes, e.target.value));
document.getElementById('innerFrameColor').addEventListener('input', e => applyInitialWoodTexture(innerFrameMeshes, e.target.value));

document.getElementById('photoFrontInput').addEventListener('change', e => handlePhotoUpload(e, customPhotoFront, 'photoFrontRotation'));
document.getElementById('photoBackInput').addEventListener('change', e => handlePhotoUpload(e, customPhotoBack, 'photoBackRotation'));
document.getElementById('photoFrontRotation').addEventListener('change', e => updateRotation(customPhotoFront, e.target.value));
document.getElementById('photoBackRotation').addEventListener('change', e => updateRotation(customPhotoBack, e.target.value));

function updateRotation(mesh, val) {
    if(mesh && mesh.material && mesh.material.map) {
        mesh.material.map.rotation = parseFloat(val);
        mesh.material.needsUpdate = true;
    }
}

function handlePhotoUpload(event, targetMesh, rotId) {
    if (!targetMesh) return;
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const canvasImg = document.createElement('canvas');
            canvasImg.width = img.width; canvasImg.height = img.height;
            const ctxImg = canvasImg.getContext('2d');
            ctxImg.drawImage(img, 0, 0);
            
            const texture = new THREE.CanvasTexture(canvasImg);
            texture.flipY = false;
            texture.center.set(0.5, 0.5);
            texture.rotation = parseFloat(document.getElementById(rotId).value);
            
            targetMesh.material = new THREE.MeshStandardMaterial({ map: texture, roughness: 0.2 });
            targetMesh.material.needsUpdate = true;
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function applyInitialWoodTexture(meshes, hexColor) {
    const canvasTex = document.createElement('canvas');
    canvasTex.width = 512; canvasTex.height = 512;
    const ctxT = canvasTex.getContext('2d');
    ctxT.fillStyle = hexColor; ctxT.fillRect(0, 0, 512, 512);
    ctxT.strokeStyle = 'rgba(0,0,0,0.1)';
    for(let i=0; i<200; i++) {
        const x = Math.random() * 512;
        ctxT.beginPath(); ctxT.lineWidth = Math.random() * 2 + 0.5;
        ctxT.moveTo(x, 0); ctxT.bezierCurveTo(x + (Math.random()*40-20), 170, x + (Math.random()*40-20), 340, x + (Math.random()*20-10), 512);
        ctxT.stroke();
    }
    const texture = new THREE.CanvasTexture(canvasTex);
    texture.wrapS = THREE.RepeatWrapping; texture.wrapT = THREE.RepeatWrapping;
    meshes.forEach(mesh => {
        if (mesh && mesh.material) {
            mesh.material.map = texture;
            mesh.material.color.set('#ffffff');
            mesh.material.needsUpdate = true;
        }
    });
}

// GLTF EXPORT
document.getElementById('btnExportGLB').addEventListener('click', () => {
    if(!currentModelGroup.children.length) return;
    const exporter = new THREE.GLTFExporter();
    exporter.parse(currentModelGroup, function (gltf) {
        const blob = new Blob([gltf], { type: 'application/octet-stream' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.style.display = 'none';
        link.href = url;
        link.download = 'cerceve_model_' + Date.now() + '.glb';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }, { binary: true });
});
</script>
@endsection
