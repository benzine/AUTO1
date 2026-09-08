/**
 * AutoParts Pro - Main Frontend JavaScript
 * Premium Automotive WordPress Theme
 * 
 * @package AutoParts_Pro
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // =========================================================================
    // State Management
    // =========================================================================
    
    const AppState = {
        theme: {
            mode: localStorage.getItem('autoparts_color_mode') || 'dark',
            initialized: false
        },
        vehicle: {
            year: null,
            make: null,
            model: null,
            engine: null,
            saved: JSON.parse(localStorage.getItem('autoparts_garage')) || []
        },
        wishlist: {
            items: JSON.parse(localStorage.getItem('autoparts_wishlist')) || [],
            userId: autopartsPro?.userId || null
        },
        chatbot: {
            isOpen: false,
            hasInteracted: false,
            autoOpened: false
        },
        ui: {
            scrollPosition: 0,
            lastScroll: 0,
            headerVisible: true
        }
    };

    // =========================================================================
    // Initialize on DOM Ready
    // =========================================================================
    
    document.addEventListener('DOMContentLoaded', function() {
        initTheme();
        initCustomCursor();
        initHeaderScroll();
        initMobileMenu();
        initVehicleSelector();
        initWishlist();
        initChatbot();
        initBackToTop();
        initMegaMenu();
        initCountdownTimers();
        initQuickView();
        initProductGallery();
        initCompatibilityCheck();
        initSocialShare();
        initFormValidation();
        initSmoothScroll();
        initAnimations();
    });

    // =========================================================================
    // Theme Toggle (Light/Dark Mode)
    // =========================================================================
    
    function initTheme() {
        const toggle = document.querySelector('.theme-toggle');
        const html = document.documentElement;
        
        // Apply saved theme
        applyTheme(AppState.theme.mode);
        AppState.theme.initialized = true;
        
        if (toggle) {
            toggle.addEventListener('click', function() {
                const newMode = AppState.theme.mode === 'dark' ? 'light' : 'dark';
                AppState.theme.mode = newMode;
                localStorage.setItem('autoparts_color_mode', newMode);
                applyTheme(newMode);
                
                // Animate toggle
                this.style.transform = 'rotate(360deg)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 300);
            });
        }
        
        // Listen for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('autoparts_color_mode')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
    
    function applyTheme(mode) {
        document.body.classList.remove('color-mode-dark', 'color-mode-light');
        document.body.classList.add('color-mode-' + mode);
        document.documentElement.setAttribute('data-color-mode', mode);
    }

    // =========================================================================
    // Custom Cursor with Mechanical Effects
    // =========================================================================
    
    function initCustomCursor() {
        if (window.matchMedia('(hover: none) and (pointer: coarse)').matches) {
            return; // Disable on touch devices
        }
        
        if (!autopartsPro?.settings?.customCursorEnabled) {
            return;
        }
        
        const cursor = document.querySelector('.custom-cursor');
        if (!cursor) return;
        
        const dot = cursor.querySelector('.cursor-dot');
        const ring = cursor.querySelector('.cursor-ring');
        
        let mouseX = 0, mouseY = 0;
        let dotX = 0, dotY = 0;
        let ringX = 0, ringY = 0;
        
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });
        
        // Smooth animation loop
        function animate() {
            // Dot follows instantly
            dotX += (mouseX - dotX) * 0.9;
            dotY += (mouseY - dotY) * 0.9;
            
            // Ring follows with delay
            ringX += (mouseX - ringX) * 0.15;
            ringY += (mouseY - ringY) * 0.15;
            
            dot.style.left = dotX + 'px';
            dot.style.top = dotY + 'px';
            ring.style.left = ringX + 'px';
            ring.style.top = ringY + 'px';
            
            requestAnimationFrame(animate);
        }
        animate();
        
        // Hover effects
        const hoverTriggers = document.querySelectorAll('a, button, .product-card, .nav-link, .mega-menu-link');
        hoverTriggers.forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursor.classList.add('hover-trigger');
            });
            el.addEventListener('mouseleave', () => {
                cursor.classList.remove('hover-trigger');
            });
        });
        
        // Click effect
        document.addEventListener('mousedown', () => {
            cursor.classList.add('cursor-clicking');
            setTimeout(() => cursor.classList.remove('cursor-clicking'), 300);
        });
    }

    // =========================================================================
    // Header Scroll Behavior
    // =========================================================================
    
    function initHeaderScroll() {
        const header = document.querySelector('.site-header');
        if (!header) return;
        
        let ticking = false;
        
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
        
        function handleScroll() {
            const scrollY = window.scrollY;
            
            // Add scrolled class
            if (scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            // Hide/show header on scroll
            if (scrollY > AppState.ui.lastScroll && scrollY > 200) {
                header.style.transform = 'translateY(-100%)';
                AppState.ui.headerVisible = false;
            } else {
                header.style.transform = '';
                AppState.ui.headerVisible = true;
            }
            
            AppState.ui.lastScroll = scrollY;
        }
    }

    // =========================================================================
    // Mobile Menu
    // =========================================================================
    
    function initMobileMenu() {
        const toggle = document.querySelector('.menu-toggle');
        const nav = document.querySelector('.mobile-nav');
        const overlay = document.querySelector('.mobile-nav-overlay');
        
        if (!toggle || !nav) return;
        
        toggle.addEventListener('click', function() {
            const isActive = this.classList.contains('active');
            
            this.classList.toggle('active');
            nav.classList.toggle('active');
            
            if (overlay) {
                overlay.classList.toggle('active');
            }
            
            document.body.classList.toggle('no-scroll');
            
            // Accessibility
            this.setAttribute('aria-expanded', !isActive);
        });
        
        // Close on overlay click
        if (overlay) {
            overlay.addEventListener('click', function() {
                toggle.classList.remove('active');
                nav.classList.remove('active');
                overlay.classList.remove('active');
                document.body.classList.remove('no-scroll');
            });
        }
        
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && toggle.classList.contains('active')) {
                toggle.click();
            }
        });
    }

    // =========================================================================
    // Vehicle Selector with Cascading Dropdowns
    // =========================================================================
    
    function initVehicleSelector() {
        if (!autopartsPro?.settings?.vehicleSelectorEnabled) {
            return;
        }
        
        const yearSelect = document.getElementById('vehicle-year');
        const makeSelect = document.getElementById('vehicle-make');
        const modelSelect = document.getElementById('vehicle-model');
        const engineSelect = document.getElementById('vehicle-engine');
        const saveBtn = document.querySelector('.save-vehicle-btn');
        
        if (!yearSelect) return;
        
        // Load saved vehicle
        loadSavedVehicle();
        
        // Year change - load makes
        yearSelect?.addEventListener('change', function() {
            const year = this.value;
            AppState.vehicle.year = year;
            
            if (year) {
                loadMakes(year);
                makeSelect.disabled = false;
            } else {
                resetDropdown(makeSelect);
                resetDropdown(modelSelect);
                resetDropdown(engineSelect);
            }
            updateCompatibilityBadges();
        });
        
        // Make change - load models
        makeSelect?.addEventListener('change', function() {
            const make = this.value;
            AppState.vehicle.make = make;
            
            if (make) {
                loadModels(AppState.vehicle.year, make);
                modelSelect.disabled = false;
            } else {
                resetDropdown(modelSelect);
                resetDropdown(engineSelect);
            }
            updateCompatibilityBadges();
        });
        
        // Model change - load engines
        modelSelect?.addEventListener('change', function() {
            const model = this.value;
            AppState.vehicle.model = model;
            
            if (model) {
                loadEngines(AppState.vehicle.year, AppState.vehicle.make, model);
                engineSelect.disabled = false;
            } else {
                resetDropdown(engineSelect);
            }
            updateCompatibilityBadges();
        });
        
        // Engine change
        engineSelect?.addEventListener('change', function() {
            AppState.vehicle.engine = this.value;
            updateCompatibilityBadges();
        });
        
        // Save to garage
        saveBtn?.addEventListener('click', saveVehicle);
    }
    
    function loadSavedVehicle() {
        const saved = AppState.vehicle.saved[0];
        if (saved) {
            const yearSelect = document.getElementById('vehicle-year');
            const makeSelect = document.getElementById('vehicle-make');
            const modelSelect = document.getElementById('vehicle-model');
            const engineSelect = document.getElementById('vehicle-engine');
            
            if (yearSelect) yearSelect.value = saved.year;
            if (makeSelect) {
                makeSelect.value = saved.make;
                makeSelect.disabled = false;
            }
            if (modelSelect) {
                modelSelect.value = saved.model;
                modelSelect.disabled = false;
            }
            if (engineSelect) {
                engineSelect.value = saved.engine;
                engineSelect.disabled = false;
            }
            
            AppState.vehicle = { ...AppState.vehicle, ...saved };
            updateCompatibilityBadges();
        }
    }
    
    async function loadMakes(year) {
        try {
            const response = await fetch(`${autopartsPro.restUrl}/vehicles/makes?year=${year}`, {
                headers: { 'X-WP-Nonce': autopartsPro.nonce }
            });
            const makes = await response.json();
            
            const makeSelect = document.getElementById('vehicle-make');
            makeSelect.innerHTML = '<option value="">Select Make</option>';
            
            makes.forEach(make => {
                const option = document.createElement('option');
                option.value = make.slug;
                option.textContent = make.name;
                makeSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading makes:', error);
        }
    }
    
    async function loadModels(year, make) {
        try {
            const response = await fetch(`${autopartsPro.restUrl}/vehicles/models?year=${year}&make=${make}`, {
                headers: { 'X-WP-Nonce': autopartsPro.nonce }
            });
            const models = await response.json();
            
            const modelSelect = document.getElementById('vehicle-model');
            modelSelect.innerHTML = '<option value="">Select Model</option>';
            
            models.forEach(model => {
                const option = document.createElement('option');
                option.value = model.slug;
                option.textContent = model.name;
                modelSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading models:', error);
        }
    }
    
    async function loadEngines(year, make, model) {
        try {
            const response = await fetch(`${autopartsPro.restUrl}/vehicles/engines?year=${year}&make=${make}&model=${model}`, {
                headers: { 'X-WP-Nonce': autopartsPro.nonce }
            });
            const engines = await response.json();
            
            const engineSelect = document.getElementById('vehicle-engine');
            engineSelect.innerHTML = '<option value="">Select Engine</option>';
            
            engines.forEach(engine => {
                const option = document.createElement('option');
                option.value = engine.slug;
                option.textContent = engine.name;
                engineSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading engines:', error);
        }
    }
    
    function resetDropdown(select) {
        if (!select) return;
        select.innerHTML = `<option value="">Select ${select.id.replace('vehicle-', '')}</option>`;
        select.disabled = true;
        select.value = '';
    }
    
    function saveVehicle() {
        if (!AppState.vehicle.year || !AppState.vehicle.make) {
            showNotification('Please select at least year and make', 'warning');
            return;
        }
        
        AppState.vehicle.saved.unshift({ ...AppState.vehicle });
        localStorage.setItem('autoparts_garage', JSON.stringify(AppState.vehicle.saved));
        
        showNotification('Vehicle saved to garage!', 'success');
        updateGarageBadge();
    }
    
    function updateCompatibilityBadges() {
        // Send AJAX request to check compatibility for products on page
        if (!AppState.vehicle.year || !AppState.vehicle.make) return;
        
        fetch(`${autopartsPro.restUrl}/products/check-compatibility`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': autopartsPro.nonce
            },
            body: JSON.stringify({
                year: AppState.vehicle.year,
                make: AppState.vehicle.make,
                model: AppState.vehicle.model,
                engine: AppState.vehicle.engine
            })
        })
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.product-card').forEach(card => {
                const productId = card.dataset.productId;
                const badge = card.querySelector('.compatibility-badge');
                
                if (data.compatible?.includes(productId)) {
                    badge.textContent = '✓ Fits Your Vehicle';
                    badge.className = 'compatibility-badge compatible';
                    badge.style.display = 'block';
                } else if (data.incompatible?.includes(productId)) {
                    badge.textContent = '✗ Does Not Fit';
                    badge.className = 'compatibility-badge incompatible';
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            });
        });
    }
    
    function updateGarageBadge() {
        const badge = document.querySelector('.garage-indicator');
        if (badge && AppState.vehicle.saved.length > 0) {
            badge.style.display = 'flex';
        }
    }

    // =========================================================================
    // Wishlist System
    // =========================================================================
    
    function initWishlist() {
        if (!autopartsPro?.settings?.wishlistEnabled) return;
        
        updateWishlistCounter();
        
        // Add to wishlist buttons
        document.querySelectorAll('.add-to-wishlist').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                addToWishlist(productId);
            });
        });
        
        // Remove from wishlist
        document.querySelectorAll('.remove-from-wishlist').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                removeFromWishlist(productId);
            });
        });
        
        // Sync with server if logged in
        if (AppState.wishlist.userId) {
            syncWishlistWithServer();
        }
    }
    
    function addToWishlist(productId) {
        if (AppState.wishlist.items.includes(productId)) {
            showNotification('Already in wishlist!', 'warning');
            return;
        }
        
        AppState.wishlist.items.push(productId);
        localStorage.setItem('autoparts_wishlist', JSON.stringify(AppState.wishlist.items));
        
        updateWishlistCounter();
        showNotification('Added to wishlist!', 'success');
        
        // Animate button
        const btn = document.querySelector(`.add-to-wishlist[data-product-id="${productId}"]`);
        if (btn) {
            btn.classList.add('active');
            btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
        }
        
        // Sync with server if logged in
        if (AppState.wishlist.userId) {
            syncWishlistWithServer();
        }
    }
    
    function removeFromWishlist(productId) {
        AppState.wishlist.items = AppState.wishlist.items.filter(id => id !== productId);
        localStorage.setItem('autoparts_wishlist', JSON.stringify(AppState.wishlist.items));
        
        updateWishlistCounter();
        showNotification('Removed from wishlist', 'info');
        
        // Remove from grid if on wishlist page
        const item = document.querySelector(`.wishlist-item[data-product-id="${productId}"]`);
        if (item) {
            item.style.opacity = '0';
            setTimeout(() => item.remove(), 300);
        }
        
        // Update button
        const btn = document.querySelector(`.add-to-wishlist[data-product-id="${productId}"]`);
        if (btn) {
            btn.classList.remove('active');
            btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>';
        }
        
        if (AppState.wishlist.userId) {
            syncWishlistWithServer();
        }
    }
    
    function updateWishlistCounter() {
        const counters = document.querySelectorAll('.wishlist-counter');
        counters.forEach(counter => {
            counter.textContent = AppState.wishlist.items.length;
            if (AppState.wishlist.items.length > 0) {
                counter.style.display = 'flex';
            } else {
                counter.style.display = 'none';
            }
        });
    }
    
    async function syncWishlistWithServer() {
        try {
            await fetch(`${autopartsPro.restUrl}/wishlist/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': autopartsPro.nonce
                },
                body: JSON.stringify({
                    items: AppState.wishlist.items
                })
            });
        } catch (error) {
            console.error('Error syncing wishlist:', error);
        }
    }

    // =========================================================================
    // AI Chatbot Widget
    // =========================================================================
    
    function initChatbot() {
        if (!autopartsPro?.settings?.chatbotEnabled) return;
        
        const toggle = document.querySelector('.chatbot-toggle');
        const window = document.querySelector('.chatbot-window');
        const close = document.querySelector('.chatbot-close');
        const input = document.querySelector('.chatbot-input input');
        const send = document.querySelector('.chatbot-send');
        const messages = document.querySelector('.chatbot-messages');
        
        if (!toggle) return;
        
        // Toggle chatbot
        toggle.addEventListener('click', () => {
            AppState.chatbot.isOpen = !AppState.chatbot.isOpen;
            AppState.chatbot.hasInteracted = true;
            
            if (AppState.chatbot.isOpen) {
                window?.classList.add('open');
                setTimeout(() => input?.focus(), 300);
            } else {
                window?.classList.remove('open');
            }
        });
        
        // Close button
        close?.addEventListener('click', () => {
            AppState.chatbot.isOpen = false;
            window?.classList.remove('open');
        });
        
        // Send message
        function sendMessage() {
            const text = input?.value.trim();
            if (!text) return;
            
            // Add user message
            addMessage(text, 'user');
            input.value = '';
            
            // Show typing indicator
            showTypingIndicator();
            
            // Send to API
            fetch(`${autopartsPro.restUrl}/chatbot/message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': autopartsPro.nonce
                },
                body: JSON.stringify({
                    message: text,
                    vehicle: AppState.vehicle
                })
            })
            .then(response => response.json())
            .then(data => {
                removeTypingIndicator();
                addMessage(data.response || 'I\'m sorry, I didn\'t understand that.', 'bot');
                
                // If product recommendations, show them
                if (data.products) {
                    showProductRecommendations(data.products);
                }
            })
            .catch(error => {
                removeTypingIndicator();
                addMessage('Sorry, I\'m having trouble connecting right now.', 'bot');
            });
        }
        
        send?.addEventListener('click', sendMessage);
        input?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
        
        // Auto-open after delay
        if (autopartsPro.settings.chatbotAutoOpen && !AppState.chatbot.hasInteracted) {
            setTimeout(() => {
                if (!AppState.chatbot.hasInteracted) {
                    AppState.chatbot.autoOpened = true;
                    window?.classList.add('open');
                    
                    // Add greeting
                    setTimeout(() => {
                        addMessage(autopartsPro.strings.chatbotGreeting, 'bot');
                    }, 500);
                    
                    // Auto-close after 3 seconds if no interaction
                    setTimeout(() => {
                        if (!AppState.chatbot.hasInteracted) {
                            window?.classList.remove('open');
                        }
                    }, 3000);
                }
            }, autopartsPro.settings.chatbotAutoOpenDelay * 1000);
        }
    }
    
    function addMessage(text, type) {
        const messages = document.querySelector('.chatbot-messages');
        if (!messages) return;
        
        const div = document.createElement('div');
        div.className = `message message-${type}`;
        div.textContent = text;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }
    
    function showTypingIndicator() {
        const messages = document.querySelector('.chatbot-messages');
        if (!messages) return;
        
        const div = document.createElement('div');
        div.className = 'typing-indicator';
        div.id = 'chatbot-typing';
        div.innerHTML = '<span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>';
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }
    
    function removeTypingIndicator() {
        const typing = document.getElementById('chatbot-typing');
        if (typing) typing.remove();
    }
    
    function showProductRecommendations(products) {
        // Implementation for showing product cards in chatbot
        console.log('Product recommendations:', products);
    }

    // =========================================================================
    // Back to Top Button
    // =========================================================================
    
    function initBackToTop() {
        if (!autopartsPro?.settings?.backToTopEnabled) return;
        
        const btn = document.querySelector('.back-to-top');
        if (!btn) return;
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        });
        
        btn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // =========================================================================
    // Mega Menu
    // =========================================================================
    
    function initMegaMenu() {
        const menuItems = document.querySelectorAll('.menu-item-has-children');
        
        menuItems.forEach(item => {
            // Handle keyboard navigation
            const link = item.querySelector('a');
            link?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    item.classList.toggle('hover');
                }
            });
            
            // Close on outside click
            document.addEventListener('click', (e) => {
                if (!item.contains(e.target)) {
                    item.classList.remove('hover');
                }
            });
        });
    }

    // =========================================================================
    // Countdown Timers
    // =========================================================================
    
    function initCountdownTimers() {
        document.querySelectorAll('.countdown-timer').forEach(timer => {
            const endDate = new Date(timer.dataset.endDate).getTime();
            
            const updateTimer = () => {
                const now = new Date().getTime();
                const distance = endDate - now;
                
                if (distance < 0) {
                    timer.innerHTML = '<span class="expired">EXPIRED</span>';
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                timer.innerHTML = `
                    <div class="countdown-item">
                        <span class="countdown-value">${String(days).padStart(2, '0')}</span>
                        <span class="countdown-label">Days</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-value">${String(hours).padStart(2, '0')}</span>
                        <span class="countdown-label">Hours</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-value">${String(minutes).padStart(2, '0')}</span>
                        <span class="countdown-label">Minutes</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-value">${String(seconds).padStart(2, '0')}</span>
                        <span class="countdown-label">Seconds</span>
                    </div>
                `;
            };
            
            updateTimer();
            setInterval(updateTimer, 1000);
        });
    }

    // =========================================================================
    // Quick View Modal
    // =========================================================================
    
    function initQuickView() {
        const modal = document.querySelector('.quick-view-modal');
        const close = modal?.querySelector('.quick-view-close');
        
        document.querySelectorAll('.quick-view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                openQuickView(productId);
            });
        });
        
        close?.addEventListener('click', () => {
            modal.classList.remove('open');
            document.body.classList.remove('no-scroll');
        });
        
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('open');
                document.body.classList.remove('no-scroll');
            }
        });
    }
    
    async function openQuickView(productId) {
        const modal = document.querySelector('.quick-view-modal');
        const content = modal?.querySelector('.quick-view-content');
        
        if (!modal || !content) return;
        
        content.innerHTML = '<div class="spinner"></div>';
        modal.classList.add('open');
        document.body.classList.add('no-scroll');
        
        try {
            const response = await fetch(`${autopartsPro.restUrl}/products/${productId}/quick-view`, {
                headers: { 'X-WP-Nonce': autopartsPro.nonce }
            });
            const data = await response.json();
            content.innerHTML = data.html;
            
            // Initialize any JS in the loaded content
            initProductGallery();
        } catch (error) {
            content.innerHTML = '<p>Error loading product details</p>';
        }
    }

    // =========================================================================
    // Product Gallery with 360° View
    // =========================================================================
    
    function initProductGallery() {
        // Initialize Swiper for product gallery
        if (typeof Swiper !== 'undefined') {
            document.querySelectorAll('.product-gallery-swiper').forEach(el => {
                new Swiper(el, {
                    loop: true,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    thumbs: {
                        swiper: {
                            el: el.closest('.product-gallery')?.querySelector('.product-gallery-thumbs'),
                            spaceBetween: 10,
                            slidesPerView: 4,
                            freeMode: true,
                            watchSlidesVisibility: true,
                            watchSlidesProgress: true
                        }
                    }
                });
            });
        }
    }

    // =========================================================================
    // Compatibility Check on Product Pages
    // =========================================================================
    
    function initCompatibilityCheck() {
        const checkBtn = document.querySelector('.check-compatibility-btn');
        const result = document.querySelector('.compatibility-result');
        
        checkBtn?.addEventListener('click', () => {
            if (!AppState.vehicle.year || !AppState.vehicle.make) {
                showNotification('Please select your vehicle first', 'warning');
                return;
            }
            
            const productId = checkBtn.dataset.productId;
            
            fetch(`${autopartsPro.restUrl}/products/${productId}/compatibility`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': autopartsPro.nonce
                },
                body: JSON.stringify({
                    year: AppState.vehicle.year,
                    make: AppState.vehicle.make,
                    model: AppState.vehicle.model,
                    engine: AppState.vehicle.engine
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.fits) {
                    result.innerHTML = '<span class="fits">✓ This part fits your vehicle!</span>';
                    result.className = 'compatibility-result fits';
                } else {
                    result.innerHTML = '<span class="not-fits">✗ This part does not fit your vehicle</span>';
                    result.className = 'compatibility-result not-fits';
                }
            });
        });
    }

    // =========================================================================
    // Social Share
    // =========================================================================
    
    function initSocialShare() {
        document.querySelectorAll('.share-btn').forEach(btn => {
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
                        window.location.href = `mailto:?subject=${title}&body=${url}`;
                        return;
                    case 'copy':
                        navigator.clipboard.writeText(window.location.href).then(() => {
                            showNotification('Link copied to clipboard!', 'success');
                        });
                        return;
                }
                
                if (shareUrl) {
                    window.open(shareUrl, 'share', 'width=600,height=400');
                }
            });
        });
    }

    // =========================================================================
    // Form Validation & Spam Protection
    // =========================================================================
    
    function initFormValidation() {
        document.querySelectorAll('form.validate').forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Check honeypot
                const hpField = form.querySelector('.hp-field');
                if (hpField && hpField.value) {
                    e.preventDefault();
                    return; // Silent fail for bots
                }
                
                // Validate required fields
                form.querySelectorAll('[required]').forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                        
                        const error = field.parentNode.querySelector('.form-error');
                        if (error) {
                            error.textContent = 'This field is required';
                            error.style.display = 'block';
                        }
                    } else {
                        field.classList.remove('error');
                        const error = field.parentNode.querySelector('.form-error');
                        if (error) {
                            error.style.display = 'none';
                        }
                    }
                });
                
                // Validate email
                form.querySelectorAll('input[type="email"]').forEach(field => {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (field.value && !emailRegex.test(field.value)) {
                        isValid = false;
                        field.classList.add('error');
                        
                        const error = field.parentNode.querySelector('.form-error');
                        if (error) {
                            error.textContent = 'Please enter a valid email';
                            error.style.display = 'block';
                        }
                    }
                });
                
                // Time-based spam detection
                const submitTime = new Date().getTime();
                const formLoadTime = parseInt(form.dataset.loadTime) || 0;
                if (submitTime - formLoadTime < 2000) {
                    e.preventDefault();
                    console.log('Spam detected: form submitted too quickly');
                    return;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    showNotification('Please fix the errors above', 'error');
                }
            });
            
            // Set load time for spam detection
            form.dataset.loadTime = new Date().getTime();
            
            // Clear errors on input
            form.querySelectorAll('input, textarea, select').forEach(field => {
                field.addEventListener('input', function() {
                    this.classList.remove('error');
                    const error = this.parentNode.querySelector('.form-error');
                    if (error) error.style.display = 'none';
                });
            });
        });
    }

    // =========================================================================
    // Smooth Scroll for Anchor Links
    // =========================================================================
    
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // =========================================================================
    // GSAP Animations (if available)
    // =========================================================================
    
    function initAnimations() {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
            return;
        }
        
        // Fade in elements on scroll
        gsap.utils.toArray('.fade-in').forEach(el => {
            gsap.from(el, {
                scrollTrigger: {
                    trigger: el,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                opacity: 0,
                y: 30,
                duration: 0.8,
                ease: 'power3.out'
            });
        });
        
        // Slide in elements
        gsap.utils.toArray('.slide-in-left').forEach(el => {
            gsap.from(el, {
                scrollTrigger: {
                    trigger: el,
                    start: 'top 80%'
                },
                opacity: 0,
                x: -50,
                duration: 0.8,
                ease: 'power3.out'
            });
        });
        
        gsap.utils.toArray('.slide-in-right').forEach(el => {
            gsap.from(el, {
                scrollTrigger: {
                    trigger: el,
                    start: 'top 80%'
                },
                opacity: 0,
                x: 50,
                duration: 0.8,
                ease: 'power3.out'
            });
        });
        
        // Scale animations
        gsap.utils.toArray('.scale-in').forEach(el => {
            gsap.from(el, {
                scrollTrigger: {
                    trigger: el,
                    start: 'top 80%'
                },
                opacity: 0,
                scale: 0.8,
                duration: 0.6,
                ease: 'back.out(1.7)'
            });
        });
    }

    // =========================================================================
    // Utility Functions
    // =========================================================================
    
    function showNotification(message, type = 'info') {
        const container = document.querySelector('.notification-container') || createNotificationContainer();
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        `;
        
        container.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }
    
    function createNotificationContainer() {
        const container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
        return container;
    }

})(jQuery);
