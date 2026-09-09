/**
 * AutoParts Pro - 3D Exploded View Hero Engine
 * Premium Three.js + GSAP ScrollTrigger Implementation
 * 
 * @package AutoParts_Pro
 * @version 2.0.0
 */

(function($) {
    'use strict';

    class ExplodedViewHero {
        constructor(containerSelector, options) {
            this.container = document.querySelector(containerSelector);
            if (!this.container) return;

            this.options = {
                modelUrl: autopartsPro3DHero.modelUrl || '',
                explosionFactor: parseFloat(autopartsPro3DHero.explosionFactor) || 2.5,
                cameraPosition: {
                    x: parseFloat(autopartsPro3DHero.cameraX) || 0,
                    y: parseFloat(autopartsPro3DHero.cameraY) || 3,
                    z: parseFloat(autopartsPro3DHero.cameraZ) || 8
                },
                autoRotate: autopartsPro3DHero.autoRotate === '1',
                rotationSpeed: parseFloat(autopartsPro3DHero.rotationSpeed) || 0.002,
                scrollDistance: parseInt(autopartsPro3DHero.scrollDistance) || 2000,
                labels: autopartsPro3DHero.labels || [],
                ...options
            };

            this.scene = null;
            this.camera = null;
            this.renderer = null;
            this.model = null;
            this.parts = [];
            this.originalPositions = new Map();
            this.explosionVectors = new Map();
            this.clock = new THREE.Clock();
            this.mouse = new THREE.Vector2();
            this.raycaster = new THREE.Raycaster();
            this.hoveredPart = null;
            this.tl = null;
            this.progress = 0;
            this.isLoading = true;
            this.isMobile = window.innerWidth < 768;

            this.init();
        }

        init() {
            this.setupScene();
            this.setupCamera();
            this.setupRenderer();
            this.setupLights();
            this.setupControls();
            this.loadModel();
            this.setupScrollTrigger();
            this.setupInteractions();
            this.animate();
            this.setupResizeHandler();

            // Hide loading state when ready
            setTimeout(() => {
                this.hideLoadingState();
            }, 1500);
        }

        setupScene() {
            this.scene = new THREE.Scene();
            this.scene.background = new THREE.Color(0x0a0a0a);
            this.scene.fog = new THREE.FogExp2(0x0a0a0a, 0.02);
        }

        setupCamera() {
            const aspect = this.container.clientWidth / this.container.clientHeight;
            this.camera = new THREE.PerspectiveCamera(45, aspect, 0.1, 1000);
            this.camera.position.set(
                this.options.cameraPosition.x,
                this.options.cameraPosition.y,
                this.options.cameraPosition.z
            );
            this.camera.lookAt(0, 0, 0);
        }

        setupRenderer() {
            this.renderer = new THREE.WebGLRenderer({
                antialias: true,
                alpha: true,
                powerPreference: 'high-performance'
            });
            this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
            this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            this.renderer.shadowMap.enabled = true;
            this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            this.renderer.outputEncoding = THREE.sRGBEncoding;
            this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
            this.renderer.toneMappingExposure = 1.2;
            this.renderer.physicallyCorrectLights = true;

            this.container.insertBefore(this.renderer.domElement, this.container.firstChild);
        }

        setupLights() {
            // Ambient light for base illumination
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
            this.scene.add(ambientLight);

            // Main directional light (key light)
            const mainLight = new THREE.DirectionalLight(0xffffff, 1.2);
            mainLight.position.set(5, 8, 5);
            mainLight.castShadow = true;
            mainLight.shadow.mapSize.width = 2048;
            mainLight.shadow.mapSize.height = 2048;
            mainLight.shadow.camera.near = 0.5;
            mainLight.shadow.camera.far = 50;
            mainLight.shadow.camera.left = -10;
            mainLight.shadow.camera.right = 10;
            mainLight.shadow.camera.top = 10;
            mainLight.shadow.camera.bottom = -10;
            this.scene.add(mainLight);

            // Rim light for edge definition
            const rimLight = new THREE.DirectionalLight(0xdc2626, 0.8);
            rimLight.position.set(-5, 3, -5);
            this.scene.add(rimLight);

            // Fill light
            const fillLight = new THREE.DirectionalLight(0xffffff, 0.5);
            fillLight.position.set(0, -5, 5);
            this.scene.add(fillLight);

            // Hemisphere light for natural sky/ground lighting
            const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.3);
            hemiLight.position.set(0, 20, 0);
            this.scene.add(hemiLight);

            // Point lights for dramatic highlights
            const pointLight1 = new THREE.PointLight(0xdc2626, 0.5, 20);
            pointLight1.position.set(3, 2, 3);
            this.scene.add(pointLight1);

            const pointLight2 = new THREE.PointLight(0xea580c, 0.3, 15);
            pointLight2.position.set(-3, 4, -2);
            this.scene.add(pointLight2);
        }

        setupControls() {
            // Simple orbit controls for mobile
            if (this.isMobile && this.options.autoRotate) {
                this.autoRotateAngle = 0;
            }
        }

        async loadModel() {
            if (!this.options.modelUrl) {
                this.createFallbackGeometry();
                return;
            }

            const loader = new THREE.GLTFLoader();
            
            try {
                const gltf = await loader.loadAsync(this.options.modelUrl);
                this.model = gltf.scene;
                
                // Process each part of the model
                this.model.traverse((child) => {
                    if (child.isMesh) {
                        child.castShadow = true;
                        child.receiveShadow = true;
                        
                        // Store original position
                        const originalPos = child.position.clone();
                        this.originalPositions.set(child.uuid, originalPos);
                        
                        // Calculate explosion vector (outward from center)
                        const direction = child.position.clone().normalize();
                        const distance = child.position.length() * this.options.explosionFactor;
                        const explodedPos = direction.multiplyScalar(distance);
                        this.explosionVectors.set(child.uuid, explodedPos);
                        
                        // Store part metadata for labels
                        if (child.name) {
                            const labelData = this.options.labels.find(l => l.partName === child.name);
                            if (labelData) {
                                child.userData.label = labelData;
                            }
                        }
                        
                        // Enhanced material properties
                        if (child.material) {
                            child.material.envMapIntensity = 1.5;
                            child.material.needsUpdate = true;
                        }
                        
                        this.parts.push(child);
                    }
                });
                
                this.scene.add(this.model);
                this.centerModel();
                this.createLabels();
                this.isLoading = false;
                
            } catch (error) {
                console.error('Error loading 3D model:', error);
                this.createFallbackGeometry();
            }
        }

        createFallbackGeometry() {
            // Create an impressive fallback engine assembly
            const engineGroup = new THREE.Group();
            
            // Engine block
            const blockGeometry = new THREE.BoxGeometry(2, 1.5, 3, 4, 4, 4);
            const blockMaterial = new THREE.MeshStandardMaterial({
                color: 0x2d2d2d,
                metalness: 0.9,
                roughness: 0.3,
                envMapIntensity: 1.5
            });
            const engineBlock = new THREE.Mesh(blockGeometry, blockMaterial);
            engineBlock.castShadow = true;
            engineBlock.receiveShadow = true;
            engineBlock.name = 'Engine Block';
            engineBlock.userData.label = {
                title: 'Engine Block',
                description: 'Aluminum alloy construction'
            };
            engineGroup.add(engineBlock);
            this.parts.push(engineBlock);
            this.originalPositions.set(engineBlock.uuid, engineBlock.position.clone());
            this.explosionVectors.set(engineBlock.uuid, new THREE.Vector3(0, 2, 0));
            
            // Pistons (4 cylinders)
            const pistonGeometry = new THREE.CylinderGeometry(0.3, 0.3, 0.8, 16);
            const pistonMaterial = new THREE.MeshStandardMaterial({
                color: 0xc0c0c0,
                metalness: 1.0,
                roughness: 0.2
            });
            
            for (let i = 0; i < 4; i++) {
                const piston = new THREE.Mesh(pistonGeometry, pistonMaterial);
                piston.position.set(-1.5 + i * 1, 0.5, 0);
                piston.rotation.x = Math.PI / 2;
                piston.castShadow = true;
                piston.name = `Piston ${i + 1}`;
                piston.userData.label = {
                    title: `Piston ${i + 1}`,
                    description: 'High-performance forged aluminum'
                };
                engineGroup.add(piston);
                this.parts.push(piston);
                this.originalPositions.set(piston.uuid, piston.position.clone());
                this.explosionVectors.set(piston.uuid, new THREE.Vector3(0, i % 2 === 0 ? 2 : -2, 0));
            }
            
            // Crankshaft
            const crankGeometry = new THREE.CylinderGeometry(0.2, 0.2, 3.5, 16);
            const crankMaterial = new THREE.MeshStandardMaterial({
                color: 0x4a4a4a,
                metalness: 0.95,
                roughness: 0.15
            });
            const crankshaft = new THREE.Mesh(crankGeometry, crankMaterial);
            crankshaft.rotation.z = Math.PI / 2;
            crankshaft.position.y = -0.5;
            crankshaft.castShadow = true;
            crankshaft.name = 'Crankshaft';
            crankshaft.userData.label = {
                title: 'Crankshaft',
                description: 'Forged steel, balanced'
            };
            engineGroup.add(crankshaft);
            this.parts.push(crankshaft);
            this.originalPositions.set(crankshaft.uuid, crankshaft.position.clone());
            this.explosionVectors.set(crankshaft.uuid, new THREE.Vector3(0, -2.5, 0));
            
            // Cylinder head
            const headGeometry = new THREE.BoxGeometry(2.2, 0.4, 3.2, 4, 2, 4);
            const headMaterial = new THREE.MeshStandardMaterial({
                color: 0x1a1a1a,
                metalness: 0.8,
                roughness: 0.4
            });
            const cylinderHead = new THREE.Mesh(headGeometry, headMaterial);
            cylinderHead.position.y = 1;
            cylinderHead.castShadow = true;
            cylinderHead.receiveShadow = true;
            cylinderHead.name = 'Cylinder Head';
            cylinderHead.userData.label = {
                title: 'Cylinder Head',
                description: 'DOHC, 4 valves per cylinder'
            };
            engineGroup.add(cylinderHead);
            this.parts.push(cylinderHead);
            this.originalPositions.set(cylinderHead.uuid, cylinderHead.position.clone());
            this.explosionVectors.set(cylinderHead.uuid, new THREE.Vector3(0, 3, 0));
            
            // Camshafts (2)
            const camGeometry = new THREE.CylinderGeometry(0.15, 0.15, 2.5, 16);
            const camMaterial = new THREE.MeshStandardMaterial({
                color: 0x6b7280,
                metalness: 0.9,
                roughness: 0.2
            });
            
            for (let i = 0; i < 2; i++) {
                const camshaft = new THREE.Mesh(camGeometry, camMaterial);
                camshaft.rotation.z = Math.PI / 2;
                camshaft.position.set(0, 1.3, -0.8 + i * 1.6);
                camshaft.castShadow = true;
                camshaft.name = `Camshaft ${i + 1}`;
                camshaft.userData.label = {
                    title: `Camshaft ${i + 1}`,
                    description: 'Performance profile'
                };
                engineGroup.add(camshaft);
                this.parts.push(camshaft);
                this.originalPositions.set(camshaft.uuid, camshaft.position.clone());
                this.explosionVectors.set(camshaft.uuid, new THREE.Vector3(0, 3.5, i === 0 ? -2 : 2));
            }
            
            // Valves (8)
            const valveGeometry = new THREE.CylinderGeometry(0.08, 0.08, 0.6, 8);
            const valveMaterial = new THREE.MeshStandardMaterial({
                color: 0xdc2626,
                metalness: 1.0,
                roughness: 0.1,
                emissive: 0xdc2626,
                emissiveIntensity: 0.3
            });
            
            for (let i = 0; i < 8; i++) {
                const valve = new THREE.Mesh(valveGeometry, valveMaterial);
                const row = Math.floor(i / 4);
                const col = i % 4;
                valve.position.set(-1 + col * 0.67, 1.5, -1.2 + row * 2.4);
                valve.castShadow = true;
                valve.name = `Valve ${i + 1}`;
                valve.userData.label = {
                    title: `Valve ${i + 1}`,
                    description: 'Titanium intake/exhaust'
                };
                engineGroup.add(valve);
                this.parts.push(valve);
                this.originalPositions.set(valve.uuid, valve.position.clone());
                this.explosionVectors.set(valve.uuid, new THREE.Vector3(0, 4, (row - 0.5) * 3));
            }
            
            // Spark plugs (4)
            const plugGeometry = new THREE.CylinderGeometry(0.1, 0.1, 0.5, 8);
            const plugMaterial = new THREE.MeshStandardMaterial({
                color: 0xea580c,
                metalness: 0.7,
                roughness: 0.3,
                emissive: 0xea580c,
                emissiveIntensity: 0.5
            });
            
            for (let i = 0; i < 4; i++) {
                const sparkPlug = new THREE.Mesh(plugGeometry, plugMaterial);
                sparkPlug.position.set(-1.5 + i * 1, 1.7, 0);
                sparkPlug.castShadow = true;
                sparkPlug.name = `Spark Plug ${i + 1}`;
                sparkPlug.userData.label = {
                    title: `Spark Plug ${i + 1}`,
                    description: 'Iridium high-performance'
                };
                engineGroup.add(sparkPlug);
                this.parts.push(sparkPlug);
                this.originalPositions.set(sparkPlug.uuid, sparkPlug.position.clone());
                this.explosionVectors.set(sparkPlug.uuid, new THREE.Vector3(0, 4.5, 0));
            }
            
            // Oil pan
            const panGeometry = new THREE.BoxGeometry(2.1, 0.6, 3.1, 4, 2, 4);
            const panMaterial = new THREE.MeshStandardMaterial({
                color: 0x0a0a0a,
                metalness: 0.6,
                roughness: 0.5
            });
            const oilPan = new THREE.Mesh(panGeometry, panMaterial);
            oilPan.position.y = -1;
            oilPan.castShadow = true;
            oilPan.receiveShadow = true;
            oilPan.name = 'Oil Pan';
            oilPan.userData.label = {
                title: 'Oil Pan',
                description: 'Baffled racing design'
            };
            engineGroup.add(oilPan);
            this.parts.push(oilPan);
            this.originalPositions.set(oilPan.uuid, oilPan.position.clone());
            this.explosionVectors.set(oilPan.uuid, new THREE.Vector3(0, -3, 0));
            
            this.model = engineGroup;
            this.scene.add(this.model);
            this.centerModel();
            this.createLabels();
            this.isLoading = false;
        }

        centerModel() {
            const box = new THREE.Box3().setFromObject(this.model);
            const center = box.getCenter(new THREE.Vector3());
            this.model.position.sub(center);
        }

        createLabels() {
            // Remove existing labels
            const existingLabels = this.container.querySelectorAll('.hero-3d-label');
            existingLabels.forEach(label => label.remove());
            
            this.options.labels.forEach((labelData, index) => {
                const label = document.createElement('div');
                label.className = 'hero-3d-label';
                label.setAttribute('data-part', labelData.partName || index);
                label.innerHTML = `
                    <div class="hero-3d-label-marker"></div>
                    <div class="hero-3d-label-content">
                        <h4 class="hero-3d-label-title">${labelData.title}</h4>
                        <p class="hero-3d-label-description">${labelData.description}</p>
                    </div>
                `;
                this.container.appendChild(label);
            });
        }

        setupScrollTrigger() {
            if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                console.warn('GSAP or ScrollTrigger not loaded');
                return;
            }

            ScrollTrigger.create({
                trigger: this.container,
                start: 'top top',
                end: `+=${this.options.scrollDistance}`,
                scrub: 0.5,
                pin: true,
                onUpdate: (self) => {
                    this.progress = self.progress;
                    this.updateExplosion(this.progress);
                    this.updateLabels(this.progress);
                },
                onEnter: () => {
                    if (this.options.autoRotate && !this.isMobile) {
                        this.autoRotateAngle = 0;
                    }
                },
                onLeave: () => {
                    this.cleanupLabels();
                },
                onLeaveBack: () => {
                    this.cleanupLabels();
                },
                onEnterBack: () => {
                    this.updateLabels(this.progress);
                }
            });
        }

        updateExplosion(progress) {
            if (!this.model) return;
            
            const easeProgress = this.easeInOutCubic(progress);
            
            this.parts.forEach((part) => {
                const originalPos = this.originalPositions.get(part.uuid);
                const explosionVec = this.explosionVectors.get(part.uuid);
                
                if (originalPos && explosionVec) {
                    // Interpolate between original and exploded position
                    part.position.lerpVectors(originalPos, explosionVec, easeProgress);
                    
                    // Add slight rotation during explosion for dynamic effect
                    const rotationAmount = 0.1 * easeProgress;
                    part.rotation.y = originalPos.rotation?.y || 0 + rotationAmount * (part.position.x > 0 ? 1 : -1);
                    
                    // Scale effect for emphasis
                    const scale = 1 + (0.05 * easeProgress);
                    part.scale.setScalar(scale);
                    
                    // Emissive glow on hover or at peak explosion
                    if (part.material) {
                        const baseEmissive = part.userData.label ? 0.2 : 0;
                        const hoverEmissive = this.hoveredPart === part ? 0.8 : 0;
                        part.material.emissiveIntensity = baseEmissive * easeProgress + hoverEmissive;
                    }
                }
            });
            
            // Camera movement for cinematic effect
            const baseZ = this.options.cameraPosition.z;
            const zoomAmount = 2 * easeProgress;
            this.camera.position.z = baseZ - zoomAmount;
            this.camera.position.y = this.options.cameraPosition.y + (1 * easeProgress);
            this.camera.lookAt(0, 0, 0);
        }

        updateLabels(progress) {
            if (progress < 0.3 || progress > 0.9) {
                this.cleanupLabels();
                return;
            }
            
            const labelElements = this.container.querySelectorAll('.hero-3d-label');
            const displayProgress = (progress - 0.3) / 0.6; // Normalize to 0-1 between 0.3 and 0.9
            
            labelElements.forEach((labelEl, index) => {
                const partName = labelEl.getAttribute('data-part');
                const part = this.parts.find(p => p.name === partName || p.userData.label?.partName === partName);
                
                if (!part) return;
                
                // Project 3D position to 2D screen space
                const vector = part.position.clone();
                vector.project(this.camera);
                
                const x = (vector.x * 0.5 + 0.5) * this.container.clientWidth;
                const y = (-vector.y * 0.5 + 0.5) * this.container.clientHeight;
                
                // Stagger label appearance
                const staggerDelay = index * 0.1;
                const labelProgress = Math.max(0, Math.min(1, (displayProgress - staggerDelay) * 2));
                
                labelEl.style.opacity = labelProgress;
                labelEl.style.transform = `translate(${x}px, ${y}px) translate(-50%, -50%) scale(${0.8 + 0.2 * labelProgress})`;
                labelEl.style.pointerEvents = labelProgress > 0.8 ? 'auto' : 'none';
            });
        }

        cleanupLabels() {
            const labelElements = this.container.querySelectorAll('.hero-3d-label');
            labelElements.forEach(label => {
                label.style.opacity = '0';
                label.style.pointerEvents = 'none';
            });
        }

        setupInteractions() {
            // Mouse move for raycasting
            this.container.addEventListener('mousemove', (e) => {
                const rect = this.renderer.domElement.getBoundingClientRect();
                this.mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
                this.mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;
                
                this.checkIntersection();
            });

            // Click interaction
            this.container.addEventListener('click', () => {
                if (this.hoveredPart && this.hoveredPart.userData.label) {
                    this.onPartClick(this.hoveredPart);
                }
            });

            // Touch support for mobile
            this.container.addEventListener('touchstart', (e) => {
                if (e.touches.length === 1) {
                    const touch = e.touches[0];
                    const rect = this.renderer.domElement.getBoundingClientRect();
                    this.mouse.x = ((touch.clientX - rect.left) / rect.width) * 2 - 1;
                    this.mouse.y = -((touch.clientY - rect.top) / rect.height) * 2 + 1;
                    
                    this.checkIntersection();
                    
                    if (this.hoveredPart && this.hoveredPart.userData.label) {
                        this.onPartClick(this.hoveredPart);
                    }
                }
            }, { passive: true });
        }

        checkIntersection() {
            this.raycaster.setFromCamera(this.mouse, this.camera);
            const intersects = this.raycaster.intersectObjects(this.parts);
            
            if (intersects.length > 0) {
                const newHoveredPart = intersects[0].object;
                
                if (this.hoveredPart !== newHoveredPart) {
                    // Reset previous hover
                    if (this.hoveredPart && this.hoveredPart.material) {
                        this.hoveredPart.material.emissiveIntensity = 0.2 * this.progress;
                        this.hoveredPart.scale.lerp(new THREE.Vector3(1, 1, 1), 0.3);
                        document.body.style.cursor = 'default';
                    }
                    
                    // Set new hover
                    this.hoveredPart = newHoveredPart;
                    if (this.hoveredPart.material) {
                        this.hoveredPart.material.emissiveIntensity = 0.8;
                        this.hoveredPart.scale.lerp(new THREE.Vector3(1.1, 1.1, 1.1), 0.3);
                        document.body.style.cursor = 'pointer';
                    }
                    
                    this.onPartHover(this.hoveredPart);
                }
            } else {
                if (this.hoveredPart && this.hoveredPart.material) {
                    this.hoveredPart.material.emissiveIntensity = 0.2 * this.progress;
                    this.hoveredPart.scale.lerp(new THREE.Vector3(1, 1, 1), 0.3);
                }
                this.hoveredPart = null;
                document.body.style.cursor = 'default';
            }
        }

        onPartHover(part) {
            if (!part.userData.label) return;
            
            const labelEl = this.container.querySelector(`.hero-3d-label[data-part="${part.name}"]`);
            if (labelEl) {
                labelEl.classList.add('active');
            }
        }

        onPartClick(part) {
            if (!part.userData.label) return;
            
            // Dispatch custom event for external handling
            const event = new CustomEvent('autopartspro:partSelected', {
                detail: {
                    partName: part.name,
                    label: part.userData.label,
                    position: part.position.clone()
                }
            });
            window.dispatchEvent(event);
            
            // Show quick view or navigate to product
            console.log('Part selected:', part.userData.label);
        }

        hideLoadingState() {
            const loadingEl = this.container.querySelector('.hero-3d-loading');
            if (loadingEl) {
                loadingEl.style.opacity = '0';
                setTimeout(() => loadingEl.remove(), 500);
            }
        }

        easeInOutCubic(x) {
            return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2;
        }

        animate() {
            requestAnimationFrame(() => this.animate());
            
            const delta = this.clock.getDelta();
            
            // Auto-rotation for mobile or when enabled
            if (this.options.autoRotate && this.model && !this.hoveredPart) {
                if (this.isMobile) {
                    this.autoRotateAngle += this.options.rotationSpeed * 2;
                    this.model.rotation.y = this.autoRotateAngle;
                } else if (this.progress < 0.1) {
                    // Only rotate when assembled
                    this.model.rotation.y += this.options.rotationSpeed;
                }
            }
            
            // Subtle floating animation for parts
            if (this.progress > 0.5) {
                const floatSpeed = 0.5;
                const floatAmount = 0.05 * (this.progress - 0.5);
                this.parts.forEach((part, index) => {
                    part.position.y += Math.sin(Date.now() * 0.001 + index) * floatAmount * delta;
                });
            }
            
            this.renderer.render(this.scene, this.camera);
        }

        setupResizeHandler() {
            window.addEventListener('resize', () => {
                const width = this.container.clientWidth;
                const height = this.container.clientHeight;
                
                this.camera.aspect = width / height;
                this.camera.updateProjectionMatrix();
                
                this.renderer.setSize(width, height);
                this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                
                this.isMobile = width < 768;
            });
        }

        destroy() {
            if (this.tl) {
                this.tl.kill();
            }
            
            this.container.removeEventListener('mousemove', this.checkIntersection.bind(this));
            this.container.removeEventListener('click', this.onPartClick.bind(this));
            
            if (this.renderer) {
                this.renderer.dispose();
                this.renderer.forceContextLoss();
            }
            
            this.scene = null;
            this.model = null;
            this.parts = [];
        }
    }

    // Initialize on DOM ready
    $(document).ready(function() {
        if (typeof THREE === 'undefined' || typeof gsap === 'undefined') {
            console.warn('AutoParts Pro 3D Hero: Required libraries not loaded');
            return;
        }
        
        const heroContainer = document.querySelector('.hero-3d-exploded');
        if (heroContainer) {
            window.autopartsPro3DHeroInstance = new ExplodedViewHero('.hero-3d-exploded');
        }
    });

})(jQuery);
