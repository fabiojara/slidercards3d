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

            // Configuración
            $('#slidercards3d-settings-form').on('submit', (e) => {
                e.preventDefault();
                this.saveSettings();
            });

            $('#reset-settings').on('click', () => {
                this.resetSettings();
            });

            // Toggle autoplay interval visibility
            $('#autoplay').on('change', () => {
                this.toggleAutoplayInterval();
            });

            // Actualizar valor del slider de oscurecimiento
            $('#darkness-intensity').on('input', (e) => {
                $('#darkness-intensity-value').text(e.target.value);
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
            } else if (tab === 'settings') {
                this.loadSettings();
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
                            <img src="${slidercards3dAdmin.pluginUrl}assets/icons/check.png" width="16" height="16" alt="" class="slidercards3d-check-icon" onerror="this.style.display='none'">
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
                                    <img src="${slidercards3dAdmin.pluginUrl}assets/icons/check.png" width="14" height="14" alt="" class="slidercards3d-check-icon" onerror="this.style.display='none'">
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
        },

        loadSettings: function() {
            $.ajax({
                url: slidercards3dAdmin.apiUrl + 'settings',
                method: 'GET',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', slidercards3dAdmin.nonce);
                },
                success: (data) => {
                    $('#separation-desktop').val(data.separation_desktop || 100);
                    $('#separation-tablet').val(data.separation_tablet || 70);
                    $('#separation-mobile').val(data.separation_mobile || 50);
                    $('#autoplay').prop('checked', data.autoplay || false);
                    $('#autoplay-interval').val(data.autoplay_interval || 3000);
                    $('#darkness-intensity').val(data.darkness_intensity || 25);
                    $('#darkness-intensity-value').text(data.darkness_intensity || 25);
                    this.toggleAutoplayInterval();
                },
                error: () => {
                    console.error('Error al cargar configuración');
                }
            });
        },

        toggleAutoplayInterval: function() {
            const isChecked = $('#autoplay').is(':checked');
            $('#autoplay-interval-group').toggle(isChecked);
        },

        saveSettings: function() {
            const $btn = $('#save-settings');
            const originalText = $btn.text();

            $btn.prop('disabled', true).text(slidercards3dAdmin.strings.saving);

            const settings = {
                separation_desktop: parseInt($('#separation-desktop').val()) || 100,
                separation_tablet: parseInt($('#separation-tablet').val()) || 70,
                separation_mobile: parseInt($('#separation-mobile').val()) || 50,
                autoplay: $('#autoplay').is(':checked') ? '1' : '0',
                autoplay_interval: parseInt($('#autoplay-interval').val()) || 3000,
                darkness_intensity: parseInt($('#darkness-intensity').val()) || 25
            };

            $.ajax({
                url: slidercards3dAdmin.apiUrl + 'settings',
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', slidercards3dAdmin.nonce);
                },
                data: JSON.stringify(settings),
                contentType: 'application/json',
                success: (data) => {
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

        resetSettings: function() {
            if (confirm('¿Estás seguro de que quieres restaurar los valores por defecto?')) {
                $('#separation-desktop').val(100);
                $('#separation-tablet').val(70);
                $('#separation-mobile').val(50);
                $('#autoplay').prop('checked', false);
                $('#autoplay-interval').val(3000);
                $('#darkness-intensity').val(25);
                $('#darkness-intensity-value').text(25);
                this.toggleAutoplayInterval();
                this.saveSettings();
            }
        }
    };

    // Inicializar cuando el DOM esté listo
    $(document).ready(() => {
        SliderCards3DAdmin.init();
    });

})(jQuery);

