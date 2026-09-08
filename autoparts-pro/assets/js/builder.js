/**
 * AutoParts Pro - 3-Rail Builder JavaScript
 * @package AutoParts_Pro
 * @since 1.0.0
 */
(function($) {
    'use strict';
    const AppBuilder = {
        history: [],
        historyIndex: -1,
        maxHistory: 50,
        currentSection: null,
        unsavedChanges: false,
        init() {
            this.cacheElements();
            this.bindEvents();
            this.initSortable();
            this.loadCurrentState();
        },
        cacheElements() {
            this.$wrapper = $('.app-builder-wrapper');
            this.$sectionsList = $('#app-sections-list');
            this.$modulesGrid = $('#app-modules-grid');
            this.$canvas = $('#app-builder-canvas');
            this.$propertiesContent = $('#app-properties-content');
            this.$modal = $('#app-add-section-modal');
            this.$pageId = this.$wrapper.data('page-id');
        },
        bindEvents() {
            $('#app-add-section').on('click', () => this.openModal());
            this.$modal.find('.modal-close, .modal-cancel').on('click', () => this.closeModal());
            this.$modal.find('.modal-confirm').on('click', () => this.addSection());
            this.$modal.find('.template-card').on('click', function() { $(this).toggleClass('selected'); });
            this.$sectionsList.on('click', '.edit-section', (e) => this.editSection(e));
            this.$sectionsList.on('click', '.delete-section', (e) => this.deleteSection(e));
            this.$sectionsList.on('click', '.duplicate-section', (e) => this.duplicateSection(e));
            this.$modulesGrid.on('mousedown', '.module-item', (e) => this.handleModuleDrag(e));
            $('.tab-btn').on('click', (e) => this.switchPropertyTab(e));
            $('.device-btn').on('click', (e) => this.switchDevice(e));
            $('#app-save-builder').on('click', () => this.saveAll());
            $('#app-undo').on('click', () => this.undo());
            $('#app-redo').on('click', () => this.redo());
            $('.app-color-picker').wpColorPicker();
            $('.upload-btn').on('click', (e) => this.handleImageUpload(e));
            $('.remove-btn').on('click', (e) => this.handleImageRemove(e));
            $('#app-module-search').on('input', (e) => this.filterModules(e));
            $('[data-action]').on('click', (e) => this.handleAccessibility(e));
            $('#app-high-contrast').on('change', (e) => this.toggleHighContrast(e));
            $('#app-reduced-motion').on('change', (e) => this.toggleReducedMotion(e));
            $('.close-rail').on('click', () => $('.app-builder-right-rail').hide());
            $(window).on('beforeunload', () => { if (this.unsavedChanges) return appBuilderData.strings.unsavedChanges; });
        },
        initSortable() {
            this.$sectionsList.sortable({ handle: '.section-handle', placeholder: 'section-placeholder', update: () => { this.unsavedChanges = true; this.pushHistory(); }});
        },
        loadCurrentState() { this.pushHistory(); },
        pushHistory() {
            const state = this.getCurrentState();
            if (this.historyIndex < this.history.length - 1) { this.history = this.history.slice(0, this.historyIndex + 1); }
            this.history.push(state);
            if (this.history.length > this.maxHistory) { this.history.shift(); } else { this.historyIndex++; }
            this.updateUndoRedoButtons();
        },
        getCurrentState() {
            const sections = [];
            this.$sectionsList.find('.section-item').each(function() { sections.push({ id: $(this).data('section-id'), name: $(this).find('.section-name').text(), type: $(this).find('.section-type').text(), index: $(this).index() }); });
            return JSON.stringify(sections);
        },
        undo() { if (this.historyIndex > 0) { this.historyIndex--; this.restoreState(this.history[this.historyIndex]); this.unsavedChanges = true; this.updateUndoRedoButtons(); } },
        redo() { if (this.historyIndex < this.history.length - 1) { this.historyIndex++; this.restoreState(this.history[this.historyIndex]); this.unsavedChanges = true; this.updateUndoRedoButtons(); } },
        restoreState(state) { console.log('Restoring state:', state); },
        updateUndoRedoButtons() { $('#app-undo').prop('disabled', this.historyIndex <= 0); $('#app-redo').prop('disabled', this.historyIndex >= this.history.length - 1); },
        openModal() { this.$modal.fadeIn(200); },
        closeModal() { this.$modal.fadeOut(200); this.$modal.find('.template-card').removeClass('selected'); },
        addSection() {
            const $selected = this.$modal.find('.template-card.selected');
            if ($selected.length === 0) { alert('Please select a template'); return; }
            const template = $selected.data('template');
            const name = $selected.find('.template-name').text();
            $.ajax({ url: appBuilderData.ajaxUrl, type: 'POST', data: { action: 'app_save_section', nonce: appBuilderData.nonce, page_id: this.$pageId, section: { type: template, name: name, modules: [] }}, success: (response) => { if (response.success) { this.addSectionToList(response.data.section); this.unsavedChanges = true; this.pushHistory(); this.closeModal(); this.showNotification(appBuilderData.strings.saveSuccess); }}});
        },
        addSectionToList(section) {
            const sectionHtml = '<div class="section-item" data-section-id="' + section.id + '" data-index="' + this.$sectionsList.children().length + '"><div class="section-handle"><span class="dashicons dashicons-menu"></span></div><div class="section-info"><span class="section-name">' + section.name + '</span><span class="section-type">' + section.type + '</span></div><div class="section-actions"><button type="button" class="button-icon edit-section" title="Edit"><span class="dashicons dashicons-edit"></span></button><button type="button" class="button-icon duplicate-section" title="Duplicate"><span class="dashicons dashicons-admin-page"></span></button><button type="button" class="button-icon delete-section" title="Delete"><span class="dashicons dashicons-trash"></span></button></div></div>';
            this.$sectionsList.append(sectionHtml);
            this.$sectionsList.find('.no-sections').remove();
        },
        editSection(e) { e.preventDefault(); const $item = $(e.target).closest('.section-item'); const sectionId = $item.data('section-id'); this.currentSection = sectionId; $.ajax({ url: appBuilderData.ajaxUrl, type: 'POST', data: { action: 'app_get_section', nonce: appBuilderData.nonce, page_id: this.$pageId, section_id: sectionId }, success: (response) => { if (response.success) { this.populateProperties(response.data.section); $('.app-builder-right-rail').show(); }}}); },
        deleteSection(e) { e.preventDefault(); if (!confirm(appBuilderData.strings.confirmDelete)) return; const $item = $(e.target).closest('.section-item'); const sectionId = $item.data('section-id'); $.ajax({ url: appBuilderData.ajaxUrl, type: 'POST', data: { action: 'app_delete_section', nonce: appBuilderData.nonce, page_id: this.$pageId, section_id: sectionId }, success: (response) => { if (response.success) { $item.fadeOut(200, () => { $item.remove(); if (this.$sectionsList.children().length === 0) { this.$sectionsList.html('<div class="no-sections"><p>No sections yet.</p></div>'); }}); this.unsavedChanges = true; this.pushHistory(); this.showNotification(appBuilderData.strings.sectionDeleted); }}}); },
        duplicateSection(e) { e.preventDefault(); const $item = $(e.target).closest('.section-item'); const sectionId = $item.data('section-id'); $.ajax({ url: appBuilderData.ajaxUrl, type: 'POST', data: { action: 'app_duplicate_section', nonce: appBuilderData.nonce, page_id: this.$pageId, section_id: sectionId }, success: (response) => { if (response.success) { location.reload(); this.unsavedChanges = true; this.pushHistory(); this.showNotification(appBuilderData.strings.sectionDuplicated); }}}); },
        reloadSections() { location.reload(); },
        populateProperties(section) { console.log('Populating properties for:', section); },
        switchPropertyTab(e) { const tab = $(e.target).data('tab'); $('.tab-btn').removeClass('active'); $(e.target).addClass('active'); $('.property-panel').hide(); $('#panel-' + tab).show(); },
        switchDevice(e) { const device = $(e.target).data('device') || $(e.target).closest('button').data('device'); $('.device-btn').removeClass('active'); $(e.target).closest('button').addClass('active'); const widths = { desktop: '100%', tablet: '768px', mobile: '375px' }; this.$canvas.css('width', widths[device]); },
        saveAll() { const order = []; this.$sectionsList.find('.section-item').each(function() { order.push($(this).data('section-id')); }); $.ajax({ url: appBuilderData.ajaxUrl, type: 'POST', data: { action: 'app_reorder_sections', nonce: appBuilderData.nonce, page_id: this.$pageId, order: order }, success: (response) => { if (response.success) { this.unsavedChanges = false; this.showNotification(appBuilderData.strings.saveSuccess); } else { alert(appBuilderData.strings.saveError); } }, error: () => { alert(appBuilderData.strings.saveError); }}); },
        handleModuleDrag(e) { const module = $(e.target).closest('.module-item').data('module'); console.log('Dragging module:', module); },
        handleImageUpload(e) { e.preventDefault(); const mediaUploader = wp.media({ title: 'Choose Image', button: { text: 'Use this image' }, multiple: false }); mediaUploader.on('select', () => { const attachment = mediaUploader.state().get('selection').first().toJSON(); const $container = $(e.target).closest('.image-upload'); $container.find('.preview-image').attr('src', attachment.url); $container.find('input[type="hidden"]').val(attachment.id); }); mediaUploader.open(); },
        handleImageRemove(e) { e.preventDefault(); const $container = $(e.target).closest('.image-upload'); $container.find('.preview-image').attr('src', ''); $container.find('input[type="hidden"]').val(''); },
        filterModules(e) { const term = $(e.target).val().toLowerCase(); this.$modulesGrid.find('.module-item').each(function() { const name = $(this).find('.module-name').text().toLowerCase(); $(this).toggle(name.indexOf(term) > -1); }); },
        handleAccessibility(e) { const action = $(e.target).data('action'); const body = $('body'); switch(action) { case 'font-increase': body.css('font-size', '+=1px'); break; case 'font-decrease': body.css('font-size', '-=1px'); break; case 'font-reset': body.css('font-size', ''); break; } },
        toggleHighContrast(e) { $('body').toggleClass('high-contrast', $(e.target).is(':checked')); },
        toggleReducedMotion(e) { $('body').toggleClass('reduced-motion', $(e.target).is(':checked')); },
        showNotification(message) { const $notification = $('<div class="app-notification">' + message + '</div>'); $notification.css({ position: 'fixed', bottom: '20px', right: '20px', background: '#4CAF50', color: '#fff', padding: '12px 24px', borderRadius: '4px', zIndex: 100001 }); $('body').append($notification); setTimeout(() => { $notification.fadeOut(200, () => $notification.remove()); }, 3000); }
    };
    $(document).ready(() => { AppBuilder.init(); });
})(jQuery);
