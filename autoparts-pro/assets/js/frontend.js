/**
 * AutoParts Pro - Main Frontend JavaScript
 *
 * @package AutoParts_Pro
 */

(function($) {
    'use strict';

    // ===========================
    // STATE MANAGEMENT
    // ===========================
    const AppState = {
        theme: localStorage.getItem('app-theme') || 'dark',
        wishlist: JSON.parse(localStorage.getItem('app-wishlist') || '[]'),
        vehicle: JSON.parse(localStorage.getItem('app-vehicle') || 'null'),
        formSelections: JSON.parse(sessionStorage.getItem('app-form-selections') || '{}'),
        
        setTheme(theme) {
            this.theme = theme;
            localStorage.setItem('app-theme', theme);
            document.documentElement.setAttribute('data-theme', theme);
        },
        
        toggleTheme() {
            this.setTheme(this.theme === 'dark' ? 'light' : 'dark');
        },
        
        addToWishlist(productId) {
            if (!this.wishlist.includes(productId)) {
                this.wishlist.push(productId);
                this.saveWishlist();
                return true;
            }
            return false;
        },
        
        removeFromWishlist(productId) {
            const index = this.wishlist.indexOf(productId);
            if (index > -1) {
                this.wishlist.splice(index, 1);
                this.saveWishlist();
                return true;
            }
            return false;
        },
        
        isInWishlist(productId) {
            return this.wishlist.includes(productId);
        },
        
        saveWishlist() {
            localStorage.setItem('app-wishlist', JSON.stringify(this.wishlist));
            this.updateWishlistCounter();
        },
        
        updateWishlistCounter() {
            const counter = document.querySelector('.wishlist-counter');
            if (counter) {
                counter.textContent = this.wishlist.length;
                counter.style.display = this.wishlist.length > 0 ? 'flex' : 'none';
            }
        },
        
        setVehicle(vehicle) {
            this.vehicle = vehicle;
            localStorage.setItem('app-vehicle', JSON.stringify(vehicle));
        },
        
        clearVehicle() {
            this.vehicle = null;
            localStorage.removeItem('app-vehicle');
        },
        
        setFormSelection(key, value) {
            this.formSelections[key] = value;
            sessionStorage.setItem('app-form-selections', JSON.stringify(this.formSelections));
        },
        
        getFormSelection(key) {
            return this.formSelections[key] || null;
        }
    };

    // ===========================
    // THEME TOGGLE
    // ===========================
    function initThemeToggle() {
        // Apply saved theme on load
        document.documentElement.setAttribute('data-theme', AppState.theme);
        
        const toggle = document.querySelector('[data-theme-toggle]');
        if (toggle) {
            toggle.addEventListener('click', () => {
                AppState.toggleTheme();
                toggle.classList.add('animating');
                setTimeout(() => toggle.classList.remove('animating'), 300);
            });
        }
    }

    // ===========================
    // CUSTOM CURSOR
    // ===========================
    function initCustomCursor() {
        const enabled = document.body.classList.contains('custom-cursor-enabled');
        if (!enabled || 'ontouchstart' in window) return;
        
        const cursor = document.createElement('div');
        cursor.className = 'custom-cursor';
        cursor.innerHTML = `
            <div class="cursor-default">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
            </div>
            <div class="cursor-hover">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M12 2v4m0 12v4M2 12h4m12 0h4"></path>
                </svg>
            </div>
            <div class="cursor-trail"></div>
        `;
        document.body.appendChild(cursor);
        
        const trailPositions = [];
        const maxTrailLength = 20;
        
        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
            
            trailPositions.push({ x: e.clientX, y: e.clientY });
            if (trailPositions.length > maxTrailLength) {
                trailPositions.shift();
            }
            
            updateCursorTrail(cursor);
        });
        
        function updateCursorTrail(cursorEl) {
            const trail = cursorEl.querySelector('.cursor-trail');
            if (trailPositions.length < 2) return;
            
            let pathData = `M ${trailPositions[0].x} ${trailPositions[0].y}`;
            for (let i = 1; i < trailPositions.length; i++) {
                pathData += ` L ${trailPositions[i].x} ${trailPositions[i].y}`;
            }
            
            trail.innerHTML = `<svg style="position:fixed;top:0;left:0;pointer-events:none;z-index:9999;">
                <path d="${pathData}" stroke="var(--app-primary-red)" stroke-width="2" fill="none" opacity="0.5"/>
            </svg>`;
        }
        
        // Hover effects
        const hoverElements = document.querySelectorAll('a, button, .clickable, input, textarea');
        hoverElements.forEach(el => {
            el.addEventListener('mouseenter', () => cursor.classList.add('hovering'));
            el.addEventListener('mouseleave', () => cursor.classList.remove('hovering'));
        });
        
        // Click effect
        document.addEventListener('click', (e) => {
            const spark = document.createElement('div');
            spark.className = 'cursor-spark';
            spark.style.left = e.clientX + 'px';
            spark.style.top = e.clientY + 'px';
            document.body.appendChild(spark);
            
            setTimeout(() => spark.remove(), 600);
        });
    }

    // ===========================
    // WISHLIST FUNCTIONALITY
    // ===========================
    function initWishlist() {
        AppState.updateWishlistCounter();
        
        // Initialize wishlist buttons
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            const productId = btn.dataset.productId;
            if (AppState.isInWishlist(parseInt(productId))) {
                btn.classList.add('active');
                btn.querySelector('.wishlist-text').textContent = autopartsProConfig.i18n.wishlisted || 'Wishlisted';
            }
            
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = parseInt(productId);
                
                if (AppState.isInWishlist(id)) {
                    AppState.removeFromWishlist(id);
                    btn.classList.remove('active');
                    btn.querySelector('.wishlist-text').textContent = autopartsProConfig.i18n.wishlist || 'Wishlist';
                    showNotification(autopartsProConfig.i18n.removedFromWishlist || 'Removed from wishlist');
                } else {
                    AppState.addToWishlist(id);
                    btn.classList.add('active');
                    btn.querySelector('.wishlist-text').textContent = autopartsProConfig.i18n.wishlisted || 'Wishlisted';
                    showNotification(autopartsProConfig.i18n.addedToWishlist || 'Added to wishlist');
                }
            });
        });
    }

    // ===========================
    // VEHICLE SELECTOR
    // ===========================
    function initVehicleSelector() {
        const selector = document.querySelector('.vehicle-selector-form');
        if (!selector) return;
        
        const yearSelect = selector.querySelector('#vehicle-year');
        const makeSelect = selector.querySelector('#vehicle-make');
        const modelSelect = selector.querySelector('#vehicle-model');
        const engineSelect = selector.querySelector('#vehicle-engine');
        
        // Load saved vehicle
        if (AppState.vehicle) {
            yearSelect.value = AppState.vehicle.year;
            triggerChange(yearSelect);
            
            setTimeout(() => {
                makeSelect.value = AppState.vehicle.make;
                triggerChange(makeSelect);
                
                setTimeout(() => {
                    modelSelect.value = AppState.vehicle.model;
                    triggerChange(modelSelect);
                    
                    setTimeout(() => {
                        engineSelect.value = AppState.vehicle.engine;
                    }, 200);
                }, 200);
            }, 200);
        }
        
        // Year change
        yearSelect?.addEventListener('change', function() {
            if (this.value) {
                makeSelect.disabled = false;
                AppState.setFormSelection('year', this.value);
            } else {
                resetSelectorAfter(this);
            }
        });
        
        // Make change
        makeSelect?.addEventListener('change', function() {
            if (this.value) {
                modelSelect.disabled = false;
                AppState.setFormSelection('make', this.value);
                loadModels(this.value, yearSelect.value);
            } else {
                resetSelectorAfter(this);
            }
        });
        
        // Model change
        modelSelect?.addEventListener('change', function() {
            if (this.value) {
                engineSelect.disabled = false;
                AppState.setFormSelection('model', this.value);
                loadEngines(makeSelect.value, this.value, yearSelect.value);
            } else {
                resetSelectorAfter(this);
            }
        });
        
        // Engine change
        engineSelect?.addEventListener('change', function() {
            if (this.value) {
                AppState.setFormSelection('engine', this.value);
            }
        });
        
        // Save vehicle
        selector.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const vehicle = {
                year: yearSelect.value,
                make: makeSelect.value,
                model: modelSelect.value,
                engine: engineSelect.value
            };
            
            AppState.setVehicle(vehicle);
            showNotification(autopartsProConfig.i18n.vehicleSaved || 'Vehicle saved!');
            
            // Update compatibility badges
            updateCompatibilityBadges();
        });
        
        function resetSelectorAfter(select) {
            const nextSelects = select.parentElement.parentElement.querySelectorAll('select');
            nextSelects.forEach(s => {
                if (s !== select) {
                    s.disabled = true;
                    s.value = '';
                }
            });
        }
        
        function triggerChange(el) {
            const event = new Event('change');
            el.dispatchEvent(event);
        }
        
        async function loadModels(make, year) {
            try {
                const response = await fetch(`${autopartsProConfig.restUrl}autoparts/v1/vehicles/models?make=${encodeURIComponent(make)}&year=${year}`);
                const models = await response.json();
                
                modelSelect.innerHTML = '<option value="">' + (autopartsProConfig.i18n.selectModel || 'Select Model') + '</option>';
                models.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model;
                    option.textContent = model;
                    modelSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading models:', error);
            }
        }
        
        async function loadEngines(make, model, year) {
            try {
                const response = await fetch(`${autopartsProConfig.restUrl}autoparts/v1/vehicles/engines?make=${encodeURIComponent(make)}&model=${encodeURIComponent(model)}&year=${year}`);
                const engines = await response.json();
                
                engineSelect.innerHTML = '<option value="">' + (autopartsProConfig.i18n.selectEngine || 'Select Engine') + '</option>';
                engines.forEach(engine => {
                    const option = document.createElement('option');
                    option.value = engine;
                    option.textContent = engine;
                    engineSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading engines:', error);
            }
        }
    }

    // ===========================
    // COMPATIBILITY BADGES
    // ===========================
    function updateCompatibilityBadges() {
        document.querySelectorAll('[data-product-id]').forEach(el => {
            const productId = el.dataset.productId;
            checkProductCompatibility(productId);
        });
    }
    
    async function checkProductCompatibility(productId) {
        if (!AppState.vehicle) return;
        
        try {
            const response = await fetch(`${autopartsProConfig.restUrl}autoparts/v1/fitment/check`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: productId,
                    vehicle: AppState.vehicle
                })
            });
            
            const result = await response.json();
            
            const badgeContainer = document.querySelector(`[data-product-id="${productId}"] .compatibility-badge-container`);
            if (badgeContainer && result.compatible !== undefined) {
                badgeContainer.innerHTML = result.compatible 
                    ? `<span class="compatibility-badge compatible">✓ ${autopartsProConfig.i18n.fitsVehicle || 'Fits Your Vehicle'}</span>`
                    : `<span class="compatibility-badge incompatible">✗ ${autopartsProConfig.i18n.doesNotFit || 'Does Not Fit'}</span>`;
            }
        } catch (error) {
            console.error('Error checking compatibility:', error);
        }
    }

    // ===========================
    // AI CHATBOT
    // ===========================
    function initChatbot() {
        const chatbotToggle = document.querySelector('[data-chatbot-toggle]');
        const chatbotWidget = document.querySelector('[data-chatbot-widget]');
        
        if (!chatbotToggle || !chatbotWidget) return;
        
        let isOpen = false;
        let autoOpenTimer = null;
        let autoCloseTimer = null;
        let hasInteracted = false;
        
        // Auto-open after 3-5 seconds
        autoOpenTimer = setTimeout(() => {
            if (!hasInteracted) {
                openChatbot();
                playClickSound();
                
                autoCloseTimer = setTimeout(() => {
                    if (!hasInteracted && isOpen) {
                        closeChatbot();
                    }
                }, 3000);
            }
        }, 4000);
        
        chatbotToggle.addEventListener('click', () => {
            hasInteracted = true;
            clearTimeout(autoOpenTimer);
            clearTimeout(autoCloseTimer);
            
            if (isOpen) {
                closeChatbot();
            } else {
                openChatbot();
            }
        });
        
        function openChatbot() {
            isOpen = true;
            chatbotWidget.classList.add('open');
            chatbotToggle.setAttribute('aria-expanded', 'true');
        }
        
        function closeChatbot() {
            isOpen = false;
            chatbotWidget.classList.remove('open');
            chatbotToggle.setAttribute('aria-expanded', 'false');
        }
        
        function playClickSound() {
            // Optional: Add subtle mechanical click sound
            // const audio = new Audio(autopartsProConfig.assetsUrl + '/sounds/click.mp3');
            // audio.volume = 0.3;
            // audio.play().catch(() => {});
        }
        
        // Chatbot form submission
        const chatForm = chatbotWidget.querySelector('form');
        if (chatForm) {
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const messageInput = chatForm.querySelector('input[type="text"]');
                const message = messageInput.value.trim();
                
                if (!message) return;
                
                // Add user message
                addChatMessage(message, 'user');
                messageInput.value = '';
                
                // Show typing indicator
                showTypingIndicator();
                
                // Send to API
                try {
                    const response = await fetch(`${autopartsProConfig.restUrl}autoparts/v1/chatbot/message`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            message: message,
                            vehicle: AppState.vehicle
                        })
                    });
                    
                    const data = await response.json();
                    hideTypingIndicator();
                    addChatMessage(data.response || autopartsProConfig.i18n.chatbotError || 'I apologize, I could not process that request.', 'bot');
                } catch (error) {
                    hideTypingIndicator();
                    addChatMessage(autopartsProConfig.i18n.chatbotError || 'Something went wrong. Please try again.', 'bot');
                }
            });
        }
        
        function addChatMessage(text, sender) {
            const messagesContainer = chatbotWidget.querySelector('.chat-messages');
            const messageEl = document.createElement('div');
            messageEl.className = `chat-message ${sender}`;
            messageEl.innerHTML = `<div class="message-content">${text}</div>`;
            messagesContainer.appendChild(messageEl);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function showTypingIndicator() {
            const messagesContainer = chatbotWidget.querySelector('.chat-messages');
            const typingEl = document.createElement('div');
            typingEl.className = 'chat-typing';
            typingEl.innerHTML = '<span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>';
            messagesContainer.appendChild(typingEl);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function hideTypingIndicator() {
            const typingEl = chatbotWidget.querySelector('.chat-typing');
            if (typingEl) typingEl.remove();
        }
    }

    // ===========================
    // BACK TO TOP BUTTON
    // ===========================
    function initBackToTop() {
        const btn = document.querySelector('[data-backtotop]');
        if (!btn) return;
        
        const iconType = btn.dataset.icon || 'arrow';
        updateBackToTopIcon(btn, iconType);
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        });
        
        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        function updateBackToTopIcon(btn, type) {
            const icons = {
                arrow: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"></polyline></svg>',
                gear: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M12 2v4m0 12v4M2 12h4m12 0h4m-9.66-7.66l2.83 2.83m-2.83 8.49l2.83-2.83m5.66-8.49l-2.83 2.83m2.83 8.49l-2.83-2.83"/></svg>',
                rocket: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2c-4 6-6 10-6 14a6 6 0 0 0 12 0c0-4-2-8-6-14z"/><circle cx="12" cy="14" r="2"/></svg>'
            };
            btn.innerHTML = icons[type] || icons.arrow;
        }
    }

    // ===========================
    // NOTIFICATIONS
    // ===========================
    function showNotification(message, type = 'info') {
        const container = document.querySelector('.notifications-container') || createNotificationsContainer();
        
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <span class="notification-message">${message}</span>
            <button class="notification-close" aria-label="Close">&times;</button>
        `;
        
        container.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 10);
        
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        });
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }
        }, 4000);
    }
    
    function createNotificationsContainer() {
        const container = document.createElement('div');
        container.className = 'notifications-container';
        document.body.appendChild(container);
        return container;
    }

    // ===========================
    // MEGA MENU
    // ===========================
    function initMegaMenu() {
        const menuItems = document.querySelectorAll('.mega-menu-item.has-submenu');
        
        menuItems.forEach(item => {
            let hoverTimeout;
            
            item.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
                item.classList.add('active');
            });
            
            item.addEventListener('mouseleave', () => {
                hoverTimeout = setTimeout(() => {
                    item.classList.remove('active');
                }, 200);
            });
        });
    }

    // ===========================
    // QUICK VIEW MODAL
    // ===========================
    function initQuickView() {
        document.querySelectorAll('[data-quickview]').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const productId = btn.dataset.quickview;
                const modal = document.getElementById(`quick-view-${productId}`);
                
                if (modal) {
                    modal.style.display = 'block';
                    document.body.classList.add('modal-open');
                    
                    modal.querySelector('.quick-view-close')?.addEventListener('click', () => {
                        modal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                    });
                    
                    modal.querySelector('.quick-view-overlay')?.addEventListener('click', () => {
                        modal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                    });
                } else {
                    // Load product via AJAX
                    try {
                        const response = await fetch(`${autopartsProConfig.restUrl}autoparts/v1/product/${productId}/quick-view`);
                        const html = await response.text();
                        
                        // Create and show modal
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        document.body.appendChild(tempDiv.firstElementChild);
                        
                        const newModal = document.getElementById(`quick-view-${productId}`);
                        newModal.style.display = 'block';
                        document.body.classList.add('modal-open');
                    } catch (error) {
                        console.error('Error loading quick view:', error);
                    }
                }
            });
        });
    }

    // ===========================
    // FLASH DEAL COUNTDOWN
    // ===========================
    function initFlashDeals() {
        document.querySelectorAll('.flash-deal-countdown').forEach(countdown => {
            const endTime = parseInt(countdown.dataset.endTime);
            
            const timer = setInterval(() => {
                const now = Math.floor(Date.now() / 1000);
                const remaining = endTime - now;
                
                if (remaining <= 0) {
                    clearInterval(timer);
                    countdown.innerHTML = '<span class="deal-ended">' + (autopartsProConfig.i18n.dealEnded || 'Deal Ended') + '</span>';
                    return;
                }
                
                const days = Math.floor(remaining / 86400);
                const hours = Math.floor((remaining % 86400) / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const seconds = remaining % 60;
                
                countdown.querySelector('.days').textContent = String(days).padStart(2, '0');
                countdown.querySelector('.hours').textContent = String(hours).padStart(2, '0');
                countdown.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
                countdown.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
            }, 1000);
        });
    }

    // ===========================
    // SOCIAL SHARE
    // ===========================
    function initSocialShare() {
        document.querySelectorAll('.copy-link').forEach(btn => {
            btn.addEventListener('click', () => {
                const url = btn.dataset.url || window.location.href;
                
                navigator.clipboard.writeText(url).then(() => {
                    showNotification(autopartsProConfig.i18n.linkCopied || 'Link copied to clipboard!', 'success');
                }).catch(() => {
                    showNotification(autopartsProConfig.i18n.copyFailed || 'Failed to copy link', 'error');
                });
            });
        });
    }

    // ===========================
    // FORM SPAM PROTECTION
    // ===========================
    function initFormProtection() {
        document.querySelectorAll('form[data-protect-spam]').forEach(form => {
            const honeypot = document.createElement('input');
            honeypot.type = 'text';
            honeypot.name = 'website_url';
            honeypot.className = 'hp-field';
            honeypot.style.display = 'none';
            honeypot.tabIndex = -1;
            honeypot.autocomplete = 'off';
            form.appendChild(honeypot);
            
            const startTime = Date.now();
            form.dataset.startTime = startTime;
            
            form.addEventListener('submit', (e) => {
                const submitTime = Date.now();
                const timeTaken = submitTime - startTime;
                
                // If form submitted too quickly (< 2 seconds), likely spam
                if (timeTaken < 2000) {
                    e.preventDefault();
                    console.log('Spam detected: Form submitted too quickly');
                    return false;
                }
                
                // If honeypot field has value, it's spam
                if (honeypot.value) {
                    e.preventDefault();
                    console.log('Spam detected: Honeypot field filled');
                    return false;
                }
            });
        });
    }

    // ===========================
    // INITIALIZATION
    // ===========================
    $(document).ready(function() {
        initThemeToggle();
        initCustomCursor();
        initWishlist();
        initVehicleSelector();
        initChatbot();
        initBackToTop();
        initMegaMenu();
        initQuickView();
        initFlashDeals();
        initSocialShare();
        initFormProtection();
        
        // Expose AppState globally for other scripts
        window.AppState = AppState;
    });

})(jQuery);
