/**
 * JavaScript del panel de administración
 */

(function($) {
    'use strict';
    
    const SliderCards3DAdmin = {
        currentTab: 'images',
        images: [],
        pages: [],
        selectedImages: new Set(),
        selectedPages: new Set(),
        
        init: function() {
            this.bindEvents();
            this.loadTabContent('images');
        },
        
        bindEvents: function() {
            // Cambio de pestañas
            $('.slidercards3d-tab').on('click', (e) => {
                const tab = $(e.currentTarget).data('tab');
                this.switchTab(tab);
            });
            
            // Búsqueda
            $('#image-search').on('input', this.debounce(() => {
                this.filterImages();
            }, 300));
            
            $('#page-search').on('input', this.debounce(() => {
                this.filterPages();
            }, 300));
            
            // Guardar selecciones
            $('#save-images').on('click', () => {
                this.saveSelection('image');
            });
            
            $('#save-pages').on('click', () => {
                this.saveSelection('page');
            });
        },
        
        switchTab: function(tab) {
            this.currentTab = tab;
            
            // Actualizar UI de pestañas
            $('.slidercards3d-tab').removeClass('active');
            $(`.slidercards3d-tab[data-tab="${tab}"]`).addClass('active');
            
            // Actualizar contenido
            $('.slidercards3d-tab-content').removeClass('active');
            $(`#tab-${tab}`).addClass('active');
            
            // Cargar contenido
            this.loadTabContent(tab);
        },
        
        loadTabContent: function(tab) {
            if (tab === 'images') {
                this.loadImages();
            } else if (tab === 'pages') {
                this.loadPages();
            }
        },
        
        loadImages: function() {
            const $grid = $('#images-grid');
            $grid.html('<div class="slidercards3d-loading"><div class="slidercards3d-spinner"></div><p>Cargando imágenes...</p></div>');
            
            $.ajax({
                url: slidercards3dAdmin.apiUrl + 'images',
                method: 'GET',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', slidercards3dAdmin.nonce);
                },
                success: (data) => {
                    this.images = data;
                    this.selectedImages = new Set(
                        data.filter(img => img.selected).map(img => img.id)
                    );
                    this.renderImages();
                },
                error: () => {
                    $grid.html('<div class="slidercards3d-empty"><div class="slidercards3d-empty-icon">📷</div><div class="slidercards3d-empty-title">Error al cargar imágenes</div><div class="slidercards3d-empty-text">Por favor, recarga la página</div></div>');
                }
            });
        },
        
        loadPages: function() {
            const $grid = $('#pages-grid');
            $grid.html('<div class="slidercards3d-loading"><div class="slidercards3d-spinner"></div><p>Cargando páginas...</p></div>');
            
            $.ajax({
                url: slidercards3dAdmin.apiUrl + 'pages',
                method: 'GET',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', slidercards3dAdmin.nonce);
                },
                success: (data) => {
                    this.pages = data;
                    this.selectedPages = new Set(
                        data.filter(page => page.selected).map(page => page.id)
                    );
                    this.renderPages();
                },
                error: () => {
                    $grid.html('<div class="slidercards3d-empty"><div class="slidercards3d-empty-icon">📄</div><div class="slidercards3d-empty-title">Error al cargar páginas</div><div class="slidercards3d-empty-text">Por favor, recarga la página</div></div>');
                }
            });
        },
        
        renderImages: function() {
            const $grid = $('#images-grid');
            
            if (this.images.length === 0) {
                $grid.html('<div class="slidercards3d-empty"><div class="slidercards3d-empty-icon">📷</div><div class="slidercards3d-empty-title">No hay imágenes</div><div class="slidercards3d-empty-text">Sube imágenes a la biblioteca de medios para comenzar</div></div>');
                return;
            }
            
            const html = this.images.map(image => {
                const isSelected = this.selectedImages.has(image.id);
                return `
                    <div class="slidercards3d-image-card ${isSelected ? 'selected' : ''}" data-id="${image.id}">
                        <img src="${image.url}" alt="${this.escapeHtml(image.title)}" loading="lazy">
                        <div class="slidercards3d-image-card-overlay">
                            <p class="slidercards3d-image-card-title">${this.escapeHtml(image.title)}</p>
                        </div>
                        <div class="slidercards3d-image-card-checkbox">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                `;
            }).join('');
            
            $grid.html(html);
            
            // Bind click events
            $('.slidercards3d-image-card').on('click', (e) => {
                const $card = $(e.currentTarget);
                const id = parseInt($card.data('id'));
                this.toggleImageSelection(id);
            });
        },
        
        renderPages: function() {
            const $grid = $('#pages-grid');
            
            if (this.pages.length === 0) {
                $grid.html('<div class="slidercards3d-empty"><div class="slidercards3d-empty-icon">📄</div><div class="slidercards3d-empty-title">No hay páginas</div><div class="slidercards3d-empty-text">Crea páginas en WordPress para comenzar</div></div>');
                return;
            }
            
            const html = this.pages.map(page => {
                const isSelected = this.selectedPages.has(page.id);
                const thumbnail = page.thumbnail || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23f5f5f5" width="400" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3ESin imagen%3C/text%3E%3C/svg%3E';
                
                return `
                    <div class="slidercards3d-page-card ${isSelected ? 'selected' : ''}" data-id="${page.id}">
                        <div class="slidercards3d-page-card-image">
                            <img src="${thumbnail}" alt="${this.escapeHtml(page.title)}" loading="lazy">
                        </div>
                        <div class="slidercards3d-page-card-content">
                            <h3 class="slidercards3d-page-card-title">
                                ${this.escapeHtml(page.title)}
                                <div class="slidercards3d-page-card-checkbox">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                            </h3>
                        </div>
                    </div>
                `;
            }).join('');
            
            $grid.html(html);
            
            // Bind click events
            $('.slidercards3d-page-card').on('click', (e) => {
                const $card = $(e.currentTarget);
                const id = parseInt($card.data('id'));
                this.togglePageSelection(id);
            });
        },
        
        toggleImageSelection: function(id) {
            if (this.selectedImages.has(id)) {
                this.selectedImages.delete(id);
            } else {
                this.selectedImages.add(id);
            }
            
            $(`.slidercards3d-image-card[data-id="${id}"]`).toggleClass('selected');
        },
        
        togglePageSelection: function(id) {
            if (this.selectedPages.has(id)) {
                this.selectedPages.delete(id);
            } else {
                this.selectedPages.add(id);
            }
            
            $(`.slidercards3d-page-card[data-id="${id}"]`).toggleClass('selected');
        },
        
        filterImages: function() {
            const search = $('#image-search').val().toLowerCase();
            
            if (!search) {
                $('.slidercards3d-image-card').show();
                return;
            }
            
            $('.slidercards3d-image-card').each(function() {
                const $card = $(this);
                const title = $card.find('.slidercards3d-image-card-title').text().toLowerCase();
                $card.toggle(title.includes(search));
            });
        },
        
        filterPages: function() {
            const search = $('#page-search').val().toLowerCase();
            
            if (!search) {
                $('.slidercards3d-page-card').show();
                return;
            }
            
            $('.slidercards3d-page-card').each(function() {
                const $card = $(this);
                const title = $card.find('.slidercards3d-page-card-title').text().toLowerCase();
                $card.toggle(title.includes(search));
            });
        },
        
        saveSelection: function(type) {
            const $btn = type === 'image' ? $('#save-images') : $('#save-pages');
            const originalText = $btn.text();
            
            $btn.prop('disabled', true).text(slidercards3dAdmin.strings.saving);
            
            const selected = type === 'image' ? this.selectedImages : this.selectedPages;
            const items = Array.from(selected).map(id => ({
                id: id,
                selected: true
            }));
            
            $.ajax({
                url: slidercards3dAdmin.apiUrl + 'selection',
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', slidercards3dAdmin.nonce);
                },
                data: JSON.stringify({
                    type: type,
                    items: items
                }),
                contentType: 'application/json',
                success: () => {
                    $btn.text(slidercards3dAdmin.strings.saved);
                    setTimeout(() => {
                        $btn.prop('disabled', false).text(originalText);
                    }, 2000);
                },
                error: () => {
                    $btn.text(slidercards3dAdmin.strings.error);
                    setTimeout(() => {
                        $btn.prop('disabled', false).text(originalText);
                    }, 2000);
                }
            });
        },
        
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };
    
    // Inicializar cuando el DOM esté listo
    $(document).ready(() => {
        SliderCards3DAdmin.init();
    });
    
})(jQuery);

