/**
 * 3-Rail Page Builder JavaScript
 */
(function($) {
    'use strict';
    
    const AppBuilder = {
        currentPageId: 0,
        currentSection: null,
        history: [],
        historyIndex: -1,
        maxHistory: 50,
        
        init: function() {
            this.currentPageId = $('.app-builder-wrap').data('page-id');
            this.bindEvents();
            this.initSortable();
            this.loadSections();
        },
        
        bindEvents: function() {
            // Tab switching
            $('.tab-btn').on('click', (e) => {
                const tab = $(e.target).data('tab');
                $('.tab-btn').removeClass('active');
                $(e.target).addClass('active');
                $('.panel').removeClass('active');
                $('#' + tab + '-panel').addClass('active');
            });
            
            // Device preview
            $('.device-btn').on('click', (e) => {
                const device = $(e.target).closest('.device-btn').data('device');
                $('.device-btn').removeClass('active');
                $(e.target).closest('.device-btn').addClass('active');
                this.setDevicePreview(device);
            });
            
            // Section selection
            $('.section-item').on('click', (e) => {
                if (!$(e.target).closest('.section-actions').length) {
                    this.selectSection($(e.target).closest('.section-item'));
                }
            });
            
            // Module drag and drop
            $('.module-item').on('dragstart', (e) => {
                e.originalEvent.dataTransfer.setData('module', $(e.target).closest('.module-item').data('module'));
            });
            
            // Save section
            $('.save-section').on('click', () => this.saveCurrentSection());
            
            // Delete section
            $('.delete-section').on('click', () => this.deleteCurrentSection());
            
            // Duplicate section
            $('.duplicate-section').on('click', () => this.duplicateCurrentSection());
            
            // Undo/Redo
            $('.undo-btn').on('click', () => this.undo());
            $('.redo-btn').on('click', () => this.redo());
            
            // Property changes
            $('.property-control').on('change input', (e) => {
                this.applyPropertyChange($(e.target));
            });
            
            // Image upload
            $('.upload-image-btn').on('click', (e) => {
                this.openMediaUploader($(e.target));
            });
            
            // Category filter
            $('.category-btn').on('click', (e) => {
                const category = $(e.target).data('category');
                $('.category-btn').removeClass('active');
                $(e.target).addClass('active');
                this.filterModules(category);
            });
            
            // Module search
            $('.module-search').on('input', (e) => {
                this.searchModules($(e.target).val());
            });
            
            // Dark mode toggle
            $('.toggle-dark-mode').on('click', () => {
                $('.app-builder-wrap').toggleClass('light-mode');
            });
            
            // Accessibility buttons
            $('.a11y-btn').on('click', (e) => {
                const action = $(e.target).data('action');
                this.applyAccessibility(action);
            });
        },
        
        initSortable: function() {
            $('.sections-list').sortable({
                handle: '.move-section',
                placeholder: 'section-placeholder',
                update: () => this.saveSectionOrder()
            });
        },
        
        loadSections: function() {
            // Load sections for current page
            $.post(appBuilderData.ajaxUrl, {
                action: 'app_get_sections',
                page_id: this.currentPageId,
                nonce: appBuilderData.nonce
            }, (response) => {
                if (response.success) {
                    this.renderSectionsList(response.data);
                }
            });
        },
        
        selectSection: function($section) {
            $('.section-item').removeClass('active');
            $section.addClass('active');
            this.currentSection = $section.data('section-id');
            this.loadSectionProperties(this.currentSection);
        },
        
        loadSectionProperties: function(sectionId) {
            $.post(appBuilderData.ajaxUrl, {
                action: 'app_get_section',
                section_id: sectionId,
                page_id: this.currentPageId,
                nonce: appBuilderData.nonce
            }, (response) => {
                if (response.success) {
                    this.populateProperties(response.data);
                }
            });
        },
        
        populateProperties: function(data) {
            $('.section-name-input').val(data.name || '');
            
            // Populate layout properties
            $('[data-property="display"]').val(data.styles?.display || 'block');
            $('[data-property="width"]').val(data.styles?.width || '100%');
            $('[data-property="height"]').val(data.styles?.height || 'auto');
            
            // Populate style properties
            if (data.styles?.backgroundColor) {
                $('[data-property="backgroundColor"]').val(data.styles.backgroundColor);
            }
            
            // Populate advanced properties
            $('[data-property="animation"]').val(data.styles?.animation || 'none');
            $('[data-property="customClass"]').val(data.customClass || '');
            $('[data-property="customCSS"]').val(data.customCSS || '');
        },
        
        applyPropertyChange: function($control) {
            const property = $control.data('property');
            const value = $control.val();
            
            // Update iframe preview
            const iframe = document.getElementById('builderPreview');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            if (this.currentSection) {
                const element = iframeDoc.querySelector('[data-section="' + this.currentSection + '"]');
                if (element) {
                    if (property === 'backgroundColor' || property === 'backgroundImage') {
                        element.style.background = property === 'backgroundImage' ? `url(${value})` : value;
                    } else if (['padding', 'margin'].includes(property)) {
                        // Handle spacing controls
                    } else {
                        element.style[property] = value;
                    }
                }
            }
            
            // Auto-save after delay
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => this.saveCurrentSection(), 1000);
        },
        
        saveCurrentSection: function() {
            const sectionData = {
                id: this.currentSection,
                name: $('.section-name-input').val(),
                styles: this.collectStyles(),
                customClass: $('[data-property="customClass"]').val(),
                customCSS: $('[data-property="customCSS"]').val()
            };
            
            $.post(appBuilderData.ajaxUrl, {
                action: 'app_save_section',
                section_data: JSON.stringify(sectionData),
                page_id: this.currentPageId,
                nonce: appBuilderData.nonce
            }, (response) => {
                if (response.success) {
                    this.pushToHistory(sectionData);
                    this.showNotification(appBuilderData.strings.sectionSaved, 'success');
                }
            });
        },
        
        collectStyles: function() {
            const styles = {};
            $('.property-control').each(function() {
                const prop = $(this).data('property');
                const val = $(this).val();
                if (prop && val) {
                    styles[prop] = val;
                }
            });
            return styles;
        },
        
        deleteCurrentSection: function() {
            if (!confirm(appBuilderData.strings.confirmDelete)) return;
            
            $.post(appBuilderData.ajaxUrl, {
                action: 'app_delete_section',
                section_id: this.currentSection,
                page_id: this.currentPageId,
                nonce: appBuilderData.nonce
            }, (response) => {
                if (response.success) {
                    $(`.section-item[data-section-id="${this.currentSection}"]`).remove();
                    this.currentSection = null;
                }
            });
        },
        
        duplicateCurrentSection: function() {
            $.post(appBuilderData.ajaxUrl, {
                action: 'app_duplicate_section',
                section_id: this.currentSection,
                page_id: this.currentPageId,
                nonce: appBuilderData.nonce
            }, (response) => {
                if (response.success) {
                    this.addSectionToList(response.data);
                }
            });
        },
        
        pushToHistory: function(state) {
            // Remove any future states
            while (this.history.length > this.historyIndex + 1) {
                this.history.pop();
            }
            
            this.history.push(JSON.parse(JSON.stringify(state)));
            if (this.history.length > this.maxHistory) {
                this.history.shift();
            } else {
                this.historyIndex++;
            }
            
            this.updateUndoRedoButtons();
        },
        
        undo: function() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                const state = this.history[this.historyIndex];
                this.restoreState(state);
            }
        },
        
        redo: function() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                const state = this.history[this.historyIndex];
                this.restoreState(state);
            }
        },
        
        restoreState: function(state) {
            this.populateProperties(state);
            this.saveCurrentSection();
            this.updateUndoRedoButtons();
        },
        
        updateUndoRedoButtons: function() {
            $('.undo-btn').prop('disabled', this.historyIndex <= 0);
            $('.redo-btn').prop('disabled', this.historyIndex >= this.history.length - 1);
            $('.history-count').text(`${this.historyIndex + 1}/${this.history.length}`);
        },
        
        setDevicePreview: function(device) {
            const widths = { desktop: '100%', tablet: '768px', mobile: '375px' };
            $('#builderPreview').css('width', widths[device]);
        },
        
        filterModules: function(category) {
            $('.module-item').each(function() {
                const itemCategory = $(this).data('category');
                $(this).toggle(category === 'all' || itemCategory === category);
            });
        },
        
        searchModules: function(query) {
            query = query.toLowerCase();
            $('.module-item').each(function() {
                const name = $(this).find('.module-name').text().toLowerCase();
                $(this).toggle(name.includes(query));
            });
        },
        
        openMediaUploader: function($btn) {
            const media = wp.media({
                title: 'Select Image',
                button: { text: 'Use Image' },
                multiple: false
            });
            
            media.on('select', () => {
                const attachment = media.state().get('selection').first().toJSON();
                $btn.siblings('input[type="hidden"]').val(attachment.url);
                $btn.siblings('.image-preview').html(`<img src="${attachment.url}" style="max-height:100%;max-width:100%;" />`);
                this.applyPropertyChange($btn.siblings('input[type="hidden"]'));
            });
            
            media.open();
        },
        
        saveSectionOrder: function() {
            const order = [];
            $('.section-item').each(function() {
                order.push($(this).data('section-id'));
            });
            
            $.post(appBuilderData.ajaxUrl, {
                action: 'app_reorder_sections',
                order: order,
                page_id: this.currentPageId,
                nonce: appBuilderData.nonce
            });
        },
        
        applyAccessibility: function(action) {
            const iframe = document.getElementById('builderPreview');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const body = iframeDoc.body;
            
            switch(action) {
                case 'font-increase':
                    body.style.fontSize = '120%';
                    break;
                case 'font-decrease':
                    body.style.fontSize = '90%';
                    break;
                case 'font-reset':
                    body.style.fontSize = '';
                    break;
                case 'high-contrast':
                    body.classList.toggle('high-contrast');
                    break;
                case 'reduced-motion':
                    body.classList.toggle('reduced-motion');
                    break;
            }
        },
        
        showNotification: function(message, type) {
            const $notif = $('<div class="builder-notification">' + message + '</div>')
                .addClass(type)
                .appendTo('body')
                .fadeIn(300);
            
            setTimeout(() => $notif.fadeOut(300, () => $notif.remove()), 3000);
        },
        
        addSectionToList: function(sectionData) {
            const $item = $(`
                <div class="section-item" data-section-id="${sectionData.id}">
                    <span class="section-thumbnail"></span>
                    <span class="section-name">${sectionData.name}</span>
                    <span class="section-actions">
                        <button class="edit-section"><span class="dashicons dashicons-edit"></span></button>
                        <button class="move-section"><span class="dashicons dashicons-move"></span></button>
                    </span>
                </div>
            `);
            $('.sections-list').append($item);
        },
        
        renderSectionsList: function(sections) {
            $('.sections-list').empty();
            sections.forEach(section => this.addSectionToList(section));
        }
    };
    
    $(document).ready(() => AppBuilder.init());
})(jQuery);
