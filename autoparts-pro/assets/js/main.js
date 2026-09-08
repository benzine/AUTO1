/**
 * AutoParts Pro - Main JavaScript
 * Premium Automotive Parts Theme
 * 
 * @version 1.0.0
 * @package AutoParts_Pro
 */

(function($) {
    'use strict';
    
    // Initialize on DOM ready
    $(document).ready(function() {
        AutoPartsPro.init();
    });
    
    // Main theme object
    const AutoPartsPro = {
        
        init: function() {
            this.themeToggle();
            this.customCursor();
            this.vehicleSelector();
            this.wishlistHandler();
            this.chatbotWidget();
            this.backToTop();
            this.megaMenu();
            this.quickView();
            this.countdownTimers();
            this.socialShare();
            this.formSpamProtection();
            this.scrollAnimations();
            this.threeDHero();
        },
        
        // Dark/Light Mode Toggle
        themeToggle: function() {
            const toggle = document.querySelector('.theme-mode-toggle');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            // Load saved preference or use system preference
            const savedMode = localStorage.getItem('autoparts_theme_mode');
            const isDark = savedMode ? savedMode === 'dark' : prefersDark;
            
            if (isDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
            
            if (toggle) {
                toggle.addEventListener('click', function() {
                    const currentTheme = document.documentElement.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('autoparts_theme_mode', newTheme);
                    
                    // Animate toggle
                    this.classList.add('toggling');
                    setTimeout(() => this.classList.remove('toggling'), 300);
                });
            }
        },
        
        // Custom Mechanical Cursor
        customCursor: function() {
            if ('ontouchstart' in window) return; // Disable on touch devices
            
            const cursor = document.createElement('div');
            cursor.className = 'custom-cursor';
            cursor.innerHTML = `
                <div class="cursor-dot"></div>
                <div class="cursor-ring"></div>
                <svg class="cursor-gear" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
            `;
            document.body.appendChild(cursor);
            
            const cursorDot = cursor.querySelector('.cursor-dot');
            const cursorRing = cursor.querySelector('.cursor-ring');
            const cursorGear = cursor.querySelector('.cursor-gear');
            
            let mouseX = 0, mouseY = 0;
            let dotX = 0, dotY = 0;
            let ringX = 0, ringY = 0;
            
            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
            });
            
            const animate = () => {
                dotX += (mouseX - dotX) * 0.5;
                dotY += (mouseY - dotY) * 0.5;
                ringX += (mouseX - ringX) * 0.15;
                ringY += (mouseY - ringY) * 0.15;
                
                cursorDot.style.transform = `translate(${dotX}px, ${dotY}px)`;
                cursorRing.style.transform = `translate(${ringX}px, ${ringY}px)`;
                
                requestAnimationFrame(animate);
            };
            animate();
            
            // Hover effects
            document.querySelectorAll('a, button, input, textarea, .clickable').forEach(el => {
                el.addEventListener('mouseenter', () => {
                    cursor.classList.add('hover');
                    cursorGear.style.opacity = '1';
                });
                el.addEventListener('mouseleave', () => {
                    cursor.classList.remove('hover');
                    cursorGear.style.opacity = '0';
                });
            });
            
            // Click spark effect
            document.addEventListener('click', (e) => {
                const spark = document.createElement('div');
                spark.className = 'cursor-spark';
                spark.style.left = e.clientX + 'px';
                spark.style.top = e.clientY + 'px';
                document.body.appendChild(spark);
                
                setTimeout(() => spark.remove(), 600);
            });
        },
        
        // Vehicle Selector with Cascading Dropdowns
        vehicleSelector: function() {
            const selectors = document.querySelectorAll('.vehicle-selector');
            
            selectors.forEach(selector => {
                const yearSelect = selector.querySelector('.vehicle-year-select');
                const makeSelect = selector.querySelector('.vehicle-make-select');
                const modelSelect = selector.querySelector('.vehicle-model-select');
                const engineSelect = selector.querySelector('.vehicle-engine-select');
                const applyBtn = selector.querySelector('.vehicle-apply-btn');
                
                if (!yearSelect) return;
                
                // Year selection
                yearSelect.addEventListener('change', function() {
                    if (this.value) {
                        makeSelect.disabled = false;
                        loadMakes(this.value);
                    } else {
                        resetSelection(makeSelect, modelSelect, engineSelect);
                    }
                });
                
                // Make selection
                makeSelect.addEventListener('change', function() {
                    if (this.value) {
                        modelSelect.disabled = false;
                        loadModels(yearSelect.value, this.value);
                    } else {
                        resetSelection(modelSelect, engineSelect);
                    }
                });
                
                // Model selection
                modelSelect.addEventListener('change', function() {
                    if (this.value) {
                        engineSelect.disabled = false;
                        loadEngines(yearSelect.value, makeSelect.value, this.value);
                    } else {
                        resetSelection(engineSelect);
                    }
                });
                
                // Engine selection
                engineSelect.addEventListener('change', function() {
                    if (this.value) {
                        applyBtn.disabled = false;
                    } else {
                        applyBtn.disabled = true;
                    }
                });
                
                // Apply button
                applyBtn.addEventListener('click', function() {
                    const vehicleData = {
                        year: yearSelect.value,
                        make: makeSelect.value,
                        model: modelSelect.value,
                        engine: engineSelect.value
                    };
                    
                    // Save to localStorage for guests
                    localStorage.setItem('autoparts_user_vehicle', JSON.stringify(vehicleData));
                    
                    // Trigger event for compatibility check
                    document.dispatchEvent(new CustomEvent('vehicleSelected', { detail: vehicleData }));
                    
                    // Show confirmation
                    showNotification(`Vehicle set: ${vehicleData.year} ${vehicleData.make} ${vehicleData.model}`);
                });
                
                // Load saved vehicle
                const savedVehicle = localStorage.getItem('autoparts_user_vehicle');
                if (savedVehicle) {
                    const vehicle = JSON.parse(savedVehicle);
                    populateVehicleSelector(selector, vehicle);
                }
            });
            
            function loadMakes(year) {
                fetch(`${autopartsData.restUrl}/fitment/makes?year=${year}`, {
                    headers: { 'X-WP-Nonce': autopartsData.nonce }
                })
                .then(res => res.json())
                .then(data => {
                    makeSelect.innerHTML = '<option value="">Make</option>';
                    data.forEach(make => {
                        makeSelect.innerHTML += `<option value="${make.slug}">${make.name}</option>`;
                    });
                });
            }
            
            function loadModels(year, make) {
                fetch(`${autopartsData.restUrl}/fitment/models?year=${year}&make=${make}`, {
                    headers: { 'X-WP-Nonce': autopartsData.nonce }
                })
                .then(res => res.json())
                .then(data => {
                    modelSelect.innerHTML = '<option value="">Model</option>';
                    data.forEach(model => {
                        modelSelect.innerHTML += `<option value="${model.slug}">${model.name}</option>`;
                    });
                });
            }
            
            function loadEngines(year, make, model) {
                fetch(`${autopartsData.restUrl}/fitment/engines?year=${year}&make=${make}&model=${model}`, {
                    headers: { 'X-WP-Nonce': autopartsData.nonce }
                })
                .then(res => res.json())
                .then(data => {
                    engineSelect.innerHTML = '<option value="">Engine</option>';
                    data.forEach(engine => {
                        engineSelect.innerHTML += `<option value="${engine.slug}">${engine.name}</option>`;
                    });
                });
            }
            
            function resetSelection(...selects) {
                selects.forEach(select => {
                    if (select) {
                        select.value = '';
                        select.disabled = true;
                        select.innerHTML = select.querySelector('option').outerHTML;
                    }
                });
                applyBtn.disabled = true;
            }
            
            function populateVehicleSelector(selector, vehicle) {
                const yearSelect = selector.querySelector('.vehicle-year-select');
                const makeSelect = selector.querySelector('.vehicle-make-select');
                const modelSelect = selector.querySelector('.vehicle-model-select');
                const engineSelect = selector.querySelector('.vehicle-engine-select');
                
                if (yearSelect && vehicle.year) {
                    yearSelect.value = vehicle.year;
                    makeSelect.disabled = false;
                    loadMakes(vehicle.year).then(() => {
                        if (vehicle.make && makeSelect) {
                            makeSelect.value = vehicle.make;
                            modelSelect.disabled = false;
                            loadModels(vehicle.year, vehicle.make).then(() => {
                                if (vehicle.model && modelSelect) {
                                    modelSelect.value = vehicle.model;
                                    engineSelect.disabled = false;
                                    loadEngines(vehicle.year, vehicle.make, vehicle.model).then(() => {
                                        if (vehicle.engine && engineSelect) {
                                            engineSelect.value = vehicle.engine;
                                            applyBtn.disabled = false;
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            }
        },
        
        // Wishlist Handler
        wishlistHandler: function() {
            const wishlistBtns = document.querySelectorAll('.wishlist-btn');
            
            wishlistBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const productId = this.dataset.productId;
                    const isInWishlist = this.classList.contains('in-wishlist');
                    
                    if (isInWishlist) {
                        removeFromWishlist(productId, this);
                    } else {
                        addToWishlist(productId, this);
                    }
                });
            });
            
            function addToWishlist(productId, btn) {
                fetch(`${autopartsData.restUrl}/wishlist/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': autopartsData.nonce
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        btn.classList.add('in-wishlist');
                        btn.querySelector('.wishlist-text').textContent = autopartsData.i18n.inWishlist;
                        updateWishlistCount(1);
                        showNotification(autopartsData.i18n.addToWishlist);
                    }
                });
            }
            
            function removeFromWishlist(productId, btn) {
                fetch(`${autopartsData.restUrl}/wishlist/remove`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': autopartsData.nonce
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        btn.classList.remove('in-wishlist');
                        btn.querySelector('.wishlist-text').textContent = autopartsData.i18n.addToWishlist;
                        updateWishlistCount(-1);
                        showNotification('Removed from wishlist');
                    }
                });
            }
            
            function updateWishlistCount(delta) {
                const badge = document.querySelector('.wishlist-count-badge');
                if (badge) {
                    let count = parseInt(badge.textContent) || 0;
                    count += delta;
                    badge.textContent = Math.max(0, count);
                }
            }
        },
        
        // AI Chatbot Widget
        chatbotWidget: function() {
            const chatbot = document.getElementById('autoparts-chatbot');
            if (!chatbot) return;
            
            const toggle = chatbot.querySelector('.chatbot-toggle');
            const close = chatbot.querySelector('.chatbot-close');
            const window = chatbot.querySelector('.chatbot-window');
            const form = chatbot.querySelector('.chatbot-input-form');
            const input = chatbot.querySelector('.chatbot-input');
            const messages = chatbot.querySelector('.chatbot-messages');
            
            const autoOpen = chatbot.dataset.autoOpen === 'true';
            let hasInteracted = false;
            
            // Auto-open cycle
            if (autoOpen && !hasInteracted) {
                setTimeout(() => {
                    openChatbot();
                    setTimeout(() => {
                        if (!hasInteracted) closeChatbot();
                    }, 3000);
                }, 4000);
            }
            
            toggle.addEventListener('click', openChatbot);
            close.addEventListener('click', closeChatbot);
            
            function openChatbot() {
                window.style.display = 'block';
                toggle.style.display = 'none';
                input.focus();
            }
            
            function closeChatbot() {
                window.style.display = 'none';
                toggle.style.display = 'flex';
            }
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const message = input.value.trim();
                if (!message) return;
                
                hasInteracted = true;
                
                // Add user message
                addMessage(message, 'user');
                input.value = '';
                
                // Show typing indicator
                showTypingIndicator();
                
                // Send to API
                fetch(`${autopartsData.restUrl}/chatbot/message`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': autopartsData.nonce
                    },
                    body: JSON.stringify({ message })
                })
                .then(res => res.json())
                .then(data => {
                    hideTypingIndicator();
                    if (data.success) {
                        addMessage(data.response, 'bot');
                    }
                });
            });
            
            function addMessage(text, role) {
                const msgDiv = document.createElement('div');
                msgDiv.className = `chatbot-message ${role}`;
                msgDiv.innerHTML = `<p>${text}</p>`;
                messages.appendChild(msgDiv);
                messages.scrollTop = messages.scrollHeight;
            }
            
            function showTypingIndicator() {
                const typing = document.createElement('div');
                typing.className = 'chatbot-typing';
                typing.innerHTML = '<span></span><span></span><span></span>';
                messages.appendChild(typing);
                messages.scrollTop = messages.scrollHeight;
            }
            
            function hideTypingIndicator() {
                const typing = messages.querySelector('.chatbot-typing');
                if (typing) typing.remove();
            }
        },
        
        // Back to Top Button
        backToTop: function() {
            const btn = document.getElementById('back-to-top');
            if (!btn) return;
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    btn.style.display = 'flex';
                } else {
                    btn.style.display = 'none';
                }
            });
            
            btn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        },
        
        // Mega Menu
        megaMenu: function() {
            const menuItems = document.querySelectorAll('.menu-item-has-children');
            
            menuItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.classList.add('hover');
                });
                
                item.addEventListener('mouseleave', function() {
                    this.classList.remove('hover');
                });
            });
        },
        
        // Quick View Modal
        quickView: function() {
            const triggers = document.querySelectorAll('.quick-view-btn');
            const modal = document.getElementById('quick-view-modal');
            
            triggers.forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    loadQuickView(productId);
                });
            });
            
            function loadQuickView(productId) {
                fetch(`/wp-json/woocommerce/v3/products/${productId}`)
                .then(res => res.json())
                .then(product => {
                    // Populate modal
                    modal.querySelector('.qv-title').textContent = product.name;
                    modal.querySelector('.qv-price').innerHTML = product.price_html;
                    modal.querySelector('.qv-image').src = product.images[0].src;
                    modal.classList.add('active');
                });
            }
            
            modal.querySelector('.qv-close').addEventListener('click', () => {
                modal.classList.remove('active');
            });
        },
        
        // Countdown Timers
        countdownTimers: function() {
            const timers = document.querySelectorAll('.countdown-timer');
            
            timers.forEach(timer => {
                const endDate = new Date(timer.dataset.endDate).getTime();
                
                const interval = setInterval(() => {
                    const now = new Date().getTime();
                    const distance = endDate - now;
                    
                    if (distance < 0) {
                        clearInterval(interval);
                        timer.innerHTML = '<span class="expired">EXPIRED</span>';
                        return;
                    }
                    
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    
                    timer.innerHTML = `
                        <span class="countdown-unit"><span class="value">${days}</span><span class="label">d</span></span>
                        <span class="countdown-separator">:</span>
                        <span class="countdown-unit"><span class="value">${hours}</span><span class="label">h</span></span>
                        <span class="countdown-separator">:</span>
                        <span class="countdown-unit"><span class="value">${minutes}</span><span class="label">m</span></span>
                        <span class="countdown-separator">:</span>
                        <span class="countdown-unit"><span class="value">${seconds}</span><span class="label">s</span></span>
                    `;
                }, 1000);
            });
        },
        
        // Social Share
        socialShare: function() {
            const shareBtns = document.querySelectorAll('.share-btn');
            
            shareBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const platform = this.dataset.platform;
                    const url = encodeURIComponent(window.location.href);
                    const title = encodeURIComponent(document.title);
                    
                    let shareUrl = '';
                    
                    switch(platform) {
                        case 'facebook':
                            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                            break;
                        case 'twitter':
                            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                            break;
                        case 'pinterest':
                            shareUrl = `https://pinterest.com/pin/create/button/?url=${url}&description=${title}`;
                            break;
                        case 'email':
                            shareUrl = `mailto:?subject=${title}&body=${url}`;
                            break;
                    }
                    
                    if (shareUrl) {
                        window.open(shareUrl, '_blank', 'width=600,height=400');
                    }
                });
            });
        },
        
        // Form Spam Protection
        formSpamProtection: function() {
            const forms = document.querySelectorAll('form[data-spam-protection]');
            
            forms.forEach(form => {
                // Honeypot field
                const honeypot = document.createElement('input');
                honeypot.type = 'text';
                honeypot.name = 'website_url';
                honeypot.className = 'hp-field';
                honeypot.style.display = 'none';
                honeypot.tabIndex = -1;
                honeypot.autocomplete = 'off';
                form.appendChild(honeypot);
                
                // Time-based detection
                const submitTime = Date.now();
                
                form.addEventListener('submit', function(e) {
                    const timeElapsed = Date.now() - submitTime;
                    
                    // If submitted too fast (< 2 seconds), likely spam
                    if (timeElapsed < 2000) {
                        e.preventDefault();
                        console.log('Spam detected: submission too fast');
                    }
                    
                    // Check honeypot
                    if (honeypot.value) {
                        e.preventDefault();
                        console.log('Spam detected: honeypot filled');
                    }
                });
            });
        },
        
        // Scroll Animations with GSAP
        scrollAnimations: function() {
            if (typeof gsap === 'undefined') return;
            
            gsap.registerPlugin(ScrollTrigger);
            
            // Animate elements on scroll
            gsap.utils.toArray('.animate-on-scroll').forEach(el => {
                gsap.from(el, {
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    },
                    y: 50,
                    opacity: 0,
                    duration: 0.8,
                    ease: 'power3.out'
                });
            });
        },
        
        // 3D Hero Animation with Three.js
        threeDHero: function() {
            const heroContainer = document.querySelector('.hero-3d-container');
            if (!heroContainer || typeof THREE === 'undefined') return;
            
            // Scene setup
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, heroContainer.clientWidth / heroContainer.clientHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
            
            renderer.setSize(heroContainer.clientWidth, heroContainer.clientHeight);
            renderer.setPixelRatio(window.devicePixelRatio);
            heroContainer.appendChild(renderer.domElement);
            
            // Lighting
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            scene.add(ambientLight);
            
            const pointLight = new THREE.PointLight(0xdc2626, 1);
            pointLight.position.set(5, 5, 5);
            scene.add(pointLight);
            
            // Placeholder geometry (replace with actual 3D model)
            const geometry = new THREE.TorusKnotGeometry(1, 0.3, 100, 16);
            const material = new THREE.MeshStandardMaterial({ 
                color: 0xdc2626,
                metalness: 0.7,
                roughness: 0.2
            });
            const torusKnot = new THREE.Mesh(geometry, material);
            scene.add(torusKnot);
            
            camera.position.z = 5;
            
            // Scroll-triggered explosion animation
            let explosionProgress = 0;
            
            window.addEventListener('scroll', () => {
                const scrollPercent = window.scrollY / (document.body.scrollHeight - window.innerHeight);
                explosionProgress = Math.min(scrollPercent * 2, 1);
            });
            
            // Animation loop
            const animate = () => {
                requestAnimationFrame(animate);
                
                torusKnot.rotation.x += 0.005;
                torusKnot.rotation.y += 0.01;
                
                // Scale based on explosion progress
                const scale = 1 + explosionProgress * 0.5;
                torusKnot.scale.set(scale, scale, scale);
                
                renderer.render(scene, camera);
            };
            
            animate();
            
            // Handle resize
            window.addEventListener('resize', () => {
                camera.aspect = heroContainer.clientWidth / heroContainer.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(heroContainer.clientWidth, heroContainer.clientHeight);
            });
        }
    };
    
    // Utility functions
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification-toast';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 10);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Expose globally
    window.AutoPartsPro = AutoPartsPro;
    
})(jQuery);
