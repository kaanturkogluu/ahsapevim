@extends('layouts.app')

@section('title', '3D Stüdyo - AhşapEvim')

@section('content')
<!-- Three.js Loaders -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/OBJLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/STLLoader.js"></script>

<div class="bg-gray-50 min-h-[calc(100vh-140px)] flex flex-col">
    <div class="container mx-auto px-4 py-6 flex-grow flex flex-col lg:flex-row gap-6">
        
        <!-- Sidebar Controls -->
        <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-4">
            
            <!-- Model Upload -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-upload text-brand mr-2"></i>Model Yükle</h2>
                
                <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 hover:border-brand transition cursor-pointer bg-gray-50">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 font-medium mb-1">Dosyayı Sürükleyin veya Tıklayın</p>
                    <p class="text-[11px] text-gray-500">Desteklenen: .glb, .gltf, .obj, .stl</p>
                    <input type="file" id="fileInput" class="hidden" accept=".glb,.gltf,.obj,.stl">
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Veya Hazır Model Seçin:</label>
                    <select id="presetModel" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-brand focus:ring focus:ring-brand focus:ring-opacity-50 p-2 border">
                        <option value="">-- Kendi Modelinizi Yükleyin --</option>
                        <option value="/cerceve.glb">Özel Dönen Çerçeve (cerceve.glb)</option>
                    </select>
                </div>
            </div>

            <!-- Custom Photo Upload for Specific Meshes -->
            <div id="customPhotoControls" class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hidden">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-image text-brand mr-2"></i>Özel Fotoğraflar</h2>
                <p class="text-xs text-gray-500 mb-4">Bu model özel fotoğraf alanlarını destekliyor.</p>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Ön Yüz Fotoğrafı</label>
                        <input type="file" id="photoFrontInput" class="text-sm w-full" accept="image/*">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Arka Yüz Fotoğrafı</label>
                        <input type="file" id="photoBackInput" class="text-sm w-full" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-sliders text-brand mr-2"></i>Görünüm Ayarları</h2>
                
                <div class="space-y-4">
                    <!-- Background -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Arka Plan</label>
                        <div class="flex gap-2">
                            <button class="bg-btn flex-1 py-1.5 rounded bg-gray-100 hover:bg-gray-200 border border-gray-300 text-xs font-medium" data-bg="#f9fafb">Açık</button>
                            <button class="bg-btn flex-1 py-1.5 rounded bg-gray-800 hover:bg-gray-900 border border-gray-700 text-white text-xs font-medium" data-bg="#1f2937">Koyu</button>
                            <button class="bg-btn flex-1 py-1.5 rounded bg-gradient-to-b from-gray-200 to-gray-400 border border-gray-400 text-xs font-medium" data-bg="gradient">Gradiyent</button>
                        </div>
                    </div>
                    
                    <!-- Lighting -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Aydınlatma</label>
                        <select id="lightPreset" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 border focus:border-brand">
                            <option value="studio">Stüdyo Işığı (Yumuşak)</option>
                            <option value="daylight">Gün Işığı (Sert Gölge)</option>
                            <option value="ambient">Düşük Işık (Gece)</option>
                        </select>
                    </div>
                    
                    <hr class="border-gray-100">
                    
                    <!-- Toggles -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Genel Döndür</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="autoRotate" class="sr-only peer" checked>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Tel Kafes (Wireframe)</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="wireframe" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                        </label>
                    </div>

                    <button id="btnResetCamera" class="w-full mt-2 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-crosshairs"></i> Kamerayı Sıfırla
                    </button>

                </div>
            </div>
            
        </div>
        
        <!-- 3D Viewport -->
        <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm relative overflow-hidden flex flex-col min-h-[400px]">
            <div id="studio3DContainer" class="flex-1 w-full bg-[#f9fafb]">
                <!-- Three.js Canvas goes here -->
            </div>
            
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-50 hidden flex-col items-center justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-brand mb-3"></div>
                <div class="text-gray-700 font-bold" id="loadingText">Model Yükleniyor...</div>
            </div>
        </div>

    </div>
</div>

<script>
let scene, camera, renderer, controls;
let currentModelGroup = new THREE.Group();
let currentBgColor = '#f9fafb';

// Specific meshes for custom integration
let customRotatingFrame = null;
let customPhotoFront = null;
let customPhotoBack = null;

const container = document.getElementById('studio3DContainer');
const loadingOverlay = document.getElementById('loadingOverlay');
const loadingText = document.getElementById('loadingText');

init3D();
setupUI();

// Load default model 'cerceve.glb' if it exists
fetch('/cerceve.glb')
    .then(res => {
        if (!res.ok) throw new Error('Default model not found');
        return res.blob();
    })
    .then(blob => {
        const file = new File([blob], 'cerceve.glb', { type: '' });
        handleFile(file);
    })
    .catch(err => {
        console.log('Varsayılan model (cerceve.glb) yüklenemedi veya bulunamadı, bekleniyor.', err);
    });

function init3D() {
    scene = new THREE.Scene();
    scene.background = new THREE.Color(currentBgColor);

    const rect = container.getBoundingClientRect();
    const width = rect.width || 800;
    const height = rect.height || 600;

    camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
    camera.position.set(0, 0, 10);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, preserveDrawingBuffer: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    container.appendChild(renderer.domElement);

    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.autoRotate = true;

    scene.add(currentModelGroup);

    setupLighting('studio');

    const animate = () => {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    };
    animate();

    window.addEventListener('resize', () => {
        const r = container.getBoundingClientRect();
        if(r.width === 0) return;
        camera.aspect = r.width / r.height;
        camera.updateProjectionMatrix();
        renderer.setSize(r.width, r.height);
    });

    // Custom Interaction for Rotating Frame
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    let isDraggingInnerFrame = false;
    let previousMouseX = 0;

    container.addEventListener('pointerdown', (event) => {
        if (!customRotatingFrame) return;

        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObject(customRotatingFrame, true);

        if (intersects.length > 0) {
            isDraggingInnerFrame = true;
            controls.enabled = false; // Disable orbit controls
            previousMouseX = event.clientX;
        }
    });

    container.addEventListener('pointermove', (event) => {
        if (isDraggingInnerFrame && customRotatingFrame) {
            const deltaX = event.clientX - previousMouseX;
            customRotatingFrame.rotation.y += deltaX * 0.01;
            previousMouseX = event.clientX;
        }
    });

    const stopDrag = () => {
        isDraggingInnerFrame = false;
        controls.enabled = true; // Re-enable orbit controls
    };

    container.addEventListener('pointerup', stopDrag);
    container.addEventListener('pointerleave', stopDrag);
}

function setupLighting(preset) {
    const lights = scene.children.filter(c => c.isLight);
    lights.forEach(l => scene.remove(l));

    if (preset === 'studio') {
        const ambient = new THREE.AmbientLight(0xffffff, 0.6);
        const key = new THREE.DirectionalLight(0xffffff, 1.0);
        key.position.set(5, 5, 5);
        key.castShadow = true;
        const fill = new THREE.DirectionalLight(0xe0f7fa, 0.4);
        fill.position.set(-5, 0, 5);
        scene.add(ambient, key, fill);
    } else if (preset === 'daylight') {
        const ambient = new THREE.AmbientLight(0xffffff, 0.3);
        const sun = new THREE.DirectionalLight(0xfffaf0, 1.5);
        sun.position.set(10, 15, 10);
        sun.castShadow = true;
        scene.add(ambient, sun);
    } else if (preset === 'ambient') {
        const ambient = new THREE.AmbientLight(0x404040, 0.8);
        const spot = new THREE.SpotLight(0xffaa55, 1.5);
        spot.position.set(0, 5, 5);
        spot.angle = Math.PI/4;
        scene.add(ambient, spot);
    }
}

function setupUI() {
    // Dropzone
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    dropZone.addEventListener('click', () => fileInput.click());
    
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-brand', 'bg-orange-50');
    });
    
    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-brand', 'bg-orange-50');
    });
    
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-brand', 'bg-orange-50');
        if (e.dataTransfer.files.length) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFile(e.target.files[0]);
        }
    });

    document.getElementById('presetModel').addEventListener('change', (e) => {
        const url = e.target.value;
        if (url) {
            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error('Model not found');
                    return res.blob();
                })
                .then(blob => {
                    const file = new File([blob], url.split('/').pop(), { type: '' });
                    handleFile(file);
                })
                .catch(err => {
                    console.log('Model yüklenemedi:', err);
                    alert("Model yüklenirken bir hata oluştu veya dosya bulunamadı.");
                });
        }
    });

    // Background
    document.querySelectorAll('.bg-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const bg = e.target.getAttribute('data-bg');
            if (bg === 'gradient') {
                container.className = "flex-1 w-full bg-gradient-to-b from-gray-200 to-gray-500";
                scene.background = null;
            } else {
                container.className = "flex-1 w-full";
                scene.background = new THREE.Color(bg);
            }
        });
    });

    // Light
    document.getElementById('lightPreset').addEventListener('change', (e) => {
        setupLighting(e.target.value);
    });

    // Toggles
    document.getElementById('autoRotate').addEventListener('change', (e) => {
        controls.autoRotate = e.target.checked;
    });

    document.getElementById('wireframe').addEventListener('change', (e) => {
        const isWire = e.target.checked;
        currentModelGroup.traverse((child) => {
            if (child.isMesh && child.material) {
                if(Array.isArray(child.material)) {
                    child.material.forEach(m => m.wireframe = isWire);
                } else {
                    child.material.wireframe = isWire;
                }
            }
        });
    });

    document.getElementById('btnResetCamera').addEventListener('click', () => {
        controls.reset();
        camera.position.set(0, 0, 10);
    });

    // Custom Photo Inputs
    document.getElementById('photoFrontInput').addEventListener('change', (e) => {
        handlePhotoUpload(e, customPhotoFront);
    });
    document.getElementById('photoBackInput').addEventListener('change', (e) => {
        handlePhotoUpload(e, customPhotoBack);
    });
}

function showLoading(text) {
    loadingText.innerText = text;
    loadingOverlay.classList.remove('hidden');
    loadingOverlay.classList.add('flex');
}

function hideLoading() {
    loadingOverlay.classList.add('hidden');
    loadingOverlay.classList.remove('flex');
}

function clearModel() {
    while(currentModelGroup.children.length > 0) { 
        currentModelGroup.remove(currentModelGroup.children[0]); 
    }
    customRotatingFrame = null;
    customPhotoFront = null;
    customPhotoBack = null;
    document.getElementById('customPhotoControls').classList.add('hidden');
}

function handlePhotoUpload(event, targetMesh) {
    if (!targetMesh) return;
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            // Create a canvas to draw the image and add artificial edge shadows
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            
            // Draw original image
            ctx.drawImage(img, 0, 0);
            
            // Calculate shadow size based on image resolution (approx 4%)
            const shadowSize = Math.max(img.width, img.height) * 0.04;
            
            // Draw Top Shadow
            let topGrad = ctx.createLinearGradient(0, 0, 0, shadowSize);
            topGrad.addColorStop(0, 'rgba(0,0,0,0.6)');
            topGrad.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.fillStyle = topGrad;
            ctx.fillRect(0, 0, img.width, shadowSize);

            // Draw Bottom Shadow
            let botGrad = ctx.createLinearGradient(0, img.height, 0, img.height - shadowSize);
            botGrad.addColorStop(0, 'rgba(0,0,0,0.6)');
            botGrad.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.fillStyle = botGrad;
            ctx.fillRect(0, img.height - shadowSize, img.width, shadowSize);

            // Draw Left Shadow
            let leftGrad = ctx.createLinearGradient(0, 0, shadowSize, 0);
            leftGrad.addColorStop(0, 'rgba(0,0,0,0.6)');
            leftGrad.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.fillStyle = leftGrad;
            ctx.fillRect(0, 0, shadowSize, img.height);

            // Draw Right Shadow
            let rightGrad = ctx.createLinearGradient(img.width, 0, img.width - shadowSize, 0);
            rightGrad.addColorStop(0, 'rgba(0,0,0,0.6)');
            rightGrad.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.fillStyle = rightGrad;
            ctx.fillRect(img.width - shadowSize, 0, shadowSize, img.height);
            
            // Create texture from the canvas
            const texture = new THREE.CanvasTexture(canvas);
            texture.flipY = false;
            
            targetMesh.material = new THREE.MeshStandardMaterial({
                map: texture,
                roughness: 0.2,    // Parlak fotoğraf hissi
                metalness: 0.05,
                envMapIntensity: 1.2
            });
            targetMesh.material.needsUpdate = true;
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function scanModelForCustomMeshes(object) {
    let foundAny = false;
    object.traverse((child) => {
        if (child.isMesh || child.isGroup) {
            const name = child.name.toLowerCase();
            if (name.includes('rotatingframe') || name.includes('rotating_frame')) {
                customRotatingFrame = child;
                foundAny = true;
            }
            if (name.includes('photofront') || name.includes('photo_front')) {
                customPhotoFront = child;
                foundAny = true;
            }
            if (name.includes('photoback') || name.includes('photo_back')) {
                customPhotoBack = child;
                foundAny = true;
            }
        }
    });

    if (foundAny) {
        document.getElementById('customPhotoControls').classList.remove('hidden');
        console.log("Custom meshes detected:", {
            RotatingFrame: !!customRotatingFrame,
            PhotoFront: !!customPhotoFront,
            PhotoBack: !!customPhotoBack
        });
    }
}

function handleFile(file) {
    const name = file.name.toLowerCase();
    const url = URL.createObjectURL(file);

    showLoading(`Yükleniyor: ${file.name}`);
    clearModel();

    camera.position.set(0,0,10);
    controls.target.set(0,0,0);

    const onProgress = (xhr) => {
        if (xhr.lengthComputable) {
            const percent = Math.round((xhr.loaded / xhr.total) * 100);
            loadingText.innerText = `%${percent} Yüklendi`;
        }
    };
    
    const onError = (e) => {
        console.error(e);
        alert("Model yüklenirken bir hata oluştu.");
        hideLoading();
    };

    const processLoadedModel = (object) => {
        const box = new THREE.Box3().setFromObject(object);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());
        const maxDim = Math.max(size.x, size.y, size.z);
        const scale = 5 / maxDim;
        object.scale.set(scale, scale, scale);
        
        object.position.x = -center.x * scale;
        object.position.y = -center.y * scale;
        object.position.z = -center.z * scale;

        const isWire = document.getElementById('wireframe').checked;
        object.traverse((child) => {
            if (child.isMesh) {
                child.castShadow = true;
                child.receiveShadow = true;
                if(child.material) {
                    if(Array.isArray(child.material)) {
                        child.material.forEach(m => m.wireframe = isWire);
                    } else {
                        child.material.wireframe = isWire;
                    }
                }
            }
        });

        scanModelForCustomMeshes(object);

        currentModelGroup.add(object);
        hideLoading();
    };

    if (name.endsWith('.gltf') || name.endsWith('.glb')) {
        const loader = new THREE.GLTFLoader();
        loader.load(url, (gltf) => {
            processLoadedModel(gltf.scene);
        }, onProgress, onError);
    } else if (name.endsWith('.obj')) {
        const loader = new THREE.OBJLoader();
        loader.load(url, (obj) => {
            processLoadedModel(obj);
        }, onProgress, onError);
    } else if (name.endsWith('.stl')) {
        const loader = new THREE.STLLoader();
        loader.load(url, (geometry) => {
            const material = new THREE.MeshStandardMaterial({ color: 0xaaaaaa, roughness: 0.4 });
            const mesh = new THREE.Mesh(geometry, material);
            processLoadedModel(mesh);
        }, onProgress, onError);
    } else {
        alert("Desteklenmeyen dosya formatı. Lütfen .glb, .gltf, .obj veya .stl yükleyin.");
        hideLoading();
    }
}
</script>
@endsection
