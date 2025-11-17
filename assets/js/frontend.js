/**
 * JavaScript del slider 3D en el frontend
 * Basado en: https://codepen.io/Nidal95/pen/RNNgWNM
 */

(function() {
    'use strict';

    const SliderCards3D = {
        items: [],
        currentIndex: 0,
        isAnimating: false,
        type: 'all',

        init: function() {
            const container = document.querySelector('.slidercards3d-container');
            if (!container) return;
            
            this.type = container.dataset.type || 'all';
            this.settings = {
                separation_desktop: 100,
                separation_tablet: 70,
                separation_mobile: 50,
                autoplay: false,
                autoplay_interval: 3000
            };
            this.autoplayTimer = null;
            this.isPaused = false;
            
            // Cargar configuración primero
            this.loadSettings().then(() => {
                this.loadItems();
                this.bindEvents();
            });
        },

        loadSettings: function() {
            return fetch(slidercards3dData.apiUrl + 'settings')
                .then(response => response.json())
                .then(data => {
                    this.settings = {
                        separation_desktop: data.separation_desktop || 100,
                        separation_tablet: data.separation_tablet || 70,
                        separation_mobile: data.separation_mobile || 50,
                        autoplay: data.autoplay || false,
                        autoplay_interval: data.autoplay_interval || 3000
                    };
                })
                .catch(() => {
                    // Usar valores por defecto si falla
                    console.warn('No se pudieron cargar los ajustes, usando valores por defecto');
                });
        },
        
        startAutoplay: function() {
            if (!this.settings.autoplay || this.items.length <= 1) return;
            
            this.stopAutoplay();
            
            this.autoplayTimer = setInterval(() => {
                if (!this.isPaused && !this.isAnimating) {
                    if (this.currentIndex < this.items.length - 1) {
                        this.next();
                    } else {
                        // Volver al inicio
                        this.goTo(0);
                    }
                }
            }, this.settings.autoplay_interval);
        },
        
        stopAutoplay: function() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        },
        
        pauseAutoplay: function() {
            this.isPaused = true;
        },
        
        resumeAutoplay: function() {
            this.isPaused = false;
        },

        loadItems: function() {
            console.log('Iniciando carga de items, tipo:', this.type);
            console.log('API URL:', slidercards3dData.apiUrl);

            const promises = [];

            if (this.type === 'all' || this.type === 'images') {
                promises.push(this.loadImages());
            }

            if (this.type === 'all' || this.type === 'pages') {
                promises.push(this.loadPages());
            }

            Promise.all(promises).then(() => {
                console.log('Items finales cargados:', this.items.length, this.items);
                if (this.items.length > 0) {
                    this.render();
                    // Iniciar autoplay si está activado
                    if (this.settings.autoplay) {
                        this.startAutoplay();
                    }
                } else {
                    console.warn('No se encontraron items para mostrar');
                    this.showEmpty();
                }
            }).catch(error => {
                console.error('Error al cargar items:', error);
                this.showEmpty();
            });
        },

        loadImages: function() {
            // Usar 'image' (singular) que es lo que espera la API
            return fetch(slidercards3dData.apiUrl + 'selection?type=image')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al obtener selección de imágenes');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos de selección de imágenes:', data);
                    if (data.ids && data.ids.length > 0) {
                        return Promise.all(
                            data.ids.map(id => this.getImageData(id))
                        );
                    }
                    return [];
                })
                .then(images => {
                    console.log('Imágenes cargadas:', images);
                    const validImages = images.filter(img => img && img.url);
                    this.items = this.items.concat(validImages.map(img => ({
                        type: 'image',
                        id: img.id,
                        url: img.url,
                        fullUrl: img.full_url,
                        title: img.title
                    })));
                })
                .catch(error => {
                    console.error('Error al cargar imágenes:', error);
                    return [];
                });
        },

        loadPages: function() {
            return fetch(slidercards3dData.apiUrl + 'selection?type=page')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al obtener selección de páginas');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos de selección de páginas:', data);
                    if (data.ids && data.ids.length > 0) {
                        return Promise.all(
                            data.ids.map(id => this.getPageData(id))
                        );
                    }
                    return [];
                })
                .then(pages => {
                    console.log('Páginas cargadas:', pages);
                    const validPages = pages.filter(page => page && page !== null);
                    this.items = this.items.concat(validPages.map(page => ({
                        type: 'page',
                        id: page.id,
                        url: page.thumbnail || '',
                        fullUrl: page.thumbnail || '',
                        title: page.title,
                        link: page.url
                    })));
                })
                .catch(error => {
                    console.error('Error al cargar páginas:', error);
                    return [];
                });
        },

        getImageData: function(id) {
            // Construir URL absoluta correctamente
            // La API URL es: http://localhost/variospluginswp/wp-json/slidercards3d/v1/
            // Necesitamos: http://localhost/variospluginswp/wp-json/wp/v2/media/{id}
            let baseUrl = slidercards3dData.apiUrl.replace('/slidercards3d/v1/', '');
            // Asegurar que baseUrl termine con /wp-json/ (con barra final)
            if (!baseUrl.endsWith('/')) {
                baseUrl += '/';
            }
            const mediaUrl = baseUrl + `wp/v2/media/${id}`;

            console.log('Obteniendo imagen:', id, 'URL:', mediaUrl);

            return fetch(mediaUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP ${response.status} al obtener imagen ${id}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const mediumUrl = data.media_details?.sizes?.medium?.source_url;
                    const fullUrl = data.source_url;

                    return {
                        id: data.id,
                        url: mediumUrl || fullUrl,
                        full_url: fullUrl,
                        title: data.title?.rendered || data.title || 'Sin título'
                    };
                })
                .catch(error => {
                    console.error(`Error al obtener imagen ${id}:`, error);
                    return null; // Retornar null para filtrar después
                });
        },

        getPageData: function(id) {
            // Construir URL absoluta correctamente
            let baseUrl = slidercards3dData.apiUrl.replace('/slidercards3d/v1/', '');
            // Asegurar que baseUrl termine con /wp-json/ (con barra final)
            if (!baseUrl.endsWith('/')) {
                baseUrl += '/';
            }
            const pageUrl = baseUrl + `wp/v2/pages/${id}`;

            console.log('Obteniendo página:', id, 'URL:', pageUrl);

            return fetch(pageUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP ${response.status} al obtener página ${id}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const thumbnailId = data.featured_media;
                    let thumbnail = '';

                    if (thumbnailId) {
                        const mediaUrl = baseUrl + `wp/v2/media/${thumbnailId}`;
                        return fetch(mediaUrl)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Error al obtener thumbnail ${thumbnailId}`);
                                }
                                return response.json();
                            })
                            .then(media => ({
                                id: data.id,
                                thumbnail: media.media_details?.sizes?.medium?.source_url || media.source_url,
                                title: data.title?.rendered || data.title || 'Sin título',
                                url: data.link
                            }))
                            .catch(error => {
                                console.error(`Error al obtener thumbnail para página ${id}:`, error);
                                return {
                                    id: data.id,
                                    thumbnail: '',
                                    title: data.title?.rendered || data.title || 'Sin título',
                                    url: data.link
                                };
                            });
                    }

                    return {
                        id: data.id,
                        thumbnail: '',
                        title: data.title?.rendered || data.title || 'Sin título',
                        url: data.link
                    };
                })
                .catch(error => {
                    console.error(`Error al obtener página ${id}:`, error);
                    return null; // Retornar null para filtrar después
                });
        },

        render: function() {
            const slider = document.getElementById('slidercards3d-slider');
            if (!slider) return;

            slider.innerHTML = '';

            // Crear cards
            this.items.forEach((item, index) => {
                const card = this.createCard(item, index);
                slider.appendChild(card);
            });

            // Crear controles si no existen
            if (!document.querySelector('.slidercards3d-controls')) {
                this.createControls();
            }

            // Crear indicadores
            this.createIndicators();

            // Posicionar cards
            this.updateCards();
        },

        createCard: function(item, index) {
            const card = document.createElement('div');
            card.className = 'slidercards3d-card';
            card.dataset.index = index;

            const img = document.createElement('img');
            img.src = item.url;
            img.alt = item.title;
            img.loading = 'lazy';
            card.appendChild(img);

            if (item.type === 'page' && item.link) {
                const overlay = document.createElement('div');
                overlay.className = 'slidercards3d-card-overlay';

                const title = document.createElement('h3');
                title.className = 'slidercards3d-card-title';
                title.textContent = item.title;
                overlay.appendChild(title);

                const link = document.createElement('a');
                link.href = item.link;
                link.className = 'slidercards3d-card-link';
                link.textContent = 'Ver página';
                link.innerHTML = 'Ver página <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M7 7h10v10"></path></svg>';
                overlay.appendChild(link);

                card.appendChild(overlay);
            }

            card.addEventListener('click', () => {
                if (item.type === 'page' && item.link) {
                    window.location.href = item.link;
                }
            });

            return card;
        },

        createControls: function() {
            const container = document.querySelector('.slidercards3d-wrapper');
            if (!container) return;

            let controls = container.querySelector('.slidercards3d-controls');
            if (!controls) {
                controls = document.createElement('div');
                controls.className = 'slidercards3d-controls';

                const prevBtn = document.createElement('button');
                prevBtn.className = 'slidercards3d-btn-prev';
                prevBtn.setAttribute('aria-label', 'Anterior');
                prevBtn.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>';
                prevBtn.addEventListener('click', () => this.prev());

                const nextBtn = document.createElement('button');
                nextBtn.className = 'slidercards3d-btn-next';
                nextBtn.setAttribute('aria-label', 'Siguiente');
                nextBtn.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>';
                nextBtn.addEventListener('click', () => this.next());

                controls.appendChild(prevBtn);
                controls.appendChild(nextBtn);
                container.appendChild(controls);
            }
        },

        createIndicators: function() {
            const container = document.querySelector('.slidercards3d-wrapper');
            if (!container) return;

            let indicators = container.querySelector('.slidercards3d-indicators');
            if (indicators) {
                indicators.remove();
            }

            if (this.items.length <= 1) return;

            indicators = document.createElement('div');
            indicators.className = 'slidercards3d-indicators';

            this.items.forEach((item, index) => {
                const indicator = document.createElement('div');
                indicator.className = 'slidercards3d-indicator';
                if (index === this.currentIndex) {
                    indicator.classList.add('active');
                }
                indicator.addEventListener('click', () => {
                    this.goTo(index);
                });
                indicators.appendChild(indicator);
            });

            container.appendChild(indicators);
        },

        updateCards: function() {
            const cards = document.querySelectorAll('.slidercards3d-card');
            const total = cards.length;

            if (total === 0) return;

            cards.forEach((card, index) => {
                const offset = index - this.currentIndex;
                const absOffset = Math.abs(offset);

                // Calcular transformación 3D
                let translateZ = -absOffset * 100;
                // Usar separación horizontal desde configuración según el tamaño de pantalla
                const isMobile = window.innerWidth <= 480;
                const isTablet = window.innerWidth <= 768 && window.innerWidth > 480;
                const separationX = isMobile ? this.settings.separation_mobile :
                                   (isTablet ? this.settings.separation_tablet :
                                    this.settings.separation_desktop);
                let translateX = offset * separationX;
                let rotateY = offset * 15;
                let opacity = 1;
                let scale = 1;

                if (absOffset > 3) {
                    opacity = 0;
                    scale = 0.8;
                } else if (absOffset > 0) {
                    opacity = 1 - (absOffset * 0.2);
                    scale = 1 - (absOffset * 0.05);
                }

                // Aplicar transformación
                card.style.transform = `
                    translateX(${translateX}px)
                    translateZ(${translateZ}px)
                    rotateY(${rotateY}deg)
                    scale(${scale})
                `;
                card.style.opacity = opacity;
                card.style.zIndex = total - absOffset;
            });

            // Actualizar indicadores
            const indicatorElements = document.querySelectorAll('.slidercards3d-indicator');
            indicatorElements.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === this.currentIndex);
            });

            // Actualizar botones
            const prevBtn = document.querySelector('.slidercards3d-btn-prev');
            const nextBtn = document.querySelector('.slidercards3d-btn-next');

            if (prevBtn) {
                prevBtn.disabled = this.currentIndex === 0;
            }
            if (nextBtn) {
                nextBtn.disabled = this.currentIndex === total - 1;
            }
        },

        next: function() {
            if (this.isAnimating) return;
            if (this.currentIndex < this.items.length - 1) {
                this.isAnimating = true;
                this.currentIndex++;
                this.updateCards();
                setTimeout(() => {
                    this.isAnimating = false;
                }, 500);
            }
        },

        prev: function() {
            if (this.isAnimating) return;
            if (this.currentIndex > 0) {
                this.isAnimating = true;
                this.currentIndex--;
                this.updateCards();
                setTimeout(() => {
                    this.isAnimating = false;
                }, 500);
            }
        },

        goTo: function(index) {
            if (this.isAnimating) return;
            if (index >= 0 && index < this.items.length && index !== this.currentIndex) {
                this.isAnimating = true;
                this.currentIndex = index;
                this.updateCards();
                setTimeout(() => {
                    this.isAnimating = false;
                }, 500);
            }
        },

        showEmpty: function() {
            const slider = document.getElementById('slidercards3d-slider');
            if (slider) {
                slider.innerHTML = `
                    <div class="slidercards3d-empty">
                        <div class="slidercards3d-empty-icon">🎴</div>
                        <div class="slidercards3d-empty-title">No hay contenido</div>
                        <div class="slidercards3d-empty-text">
                            <p>Selecciona imágenes o páginas en el panel de administración.</p>
                            <p style="margin-top: 1rem; font-size: 0.875rem; opacity: 0.7;">
                                Ve a <strong>Slider 3D</strong> en el menú de WordPress, selecciona contenido y haz clic en "Guardar selección".
                            </p>
                            <p style="margin-top: 0.5rem; font-size: 0.75rem; opacity: 0.6;">
                                Si ya seleccionaste contenido, verifica la consola del navegador (F12) para ver errores.
                            </p>
                        </div>
                    </div>
                `;
            }
        },

        bindEvents: function() {
            const container = document.querySelector('.slidercards3d-container');
            if (!container) return;
            
            // Navegación con teclado
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.pauseAutoplay();
                    this.prev();
                    setTimeout(() => this.resumeAutoplay(), this.settings.autoplay_interval);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.pauseAutoplay();
                    this.next();
                    setTimeout(() => this.resumeAutoplay(), this.settings.autoplay_interval);
                }
            });
            
            // Pausar autoplay al interactuar con el slider
            container.addEventListener('mouseenter', () => {
                this.pauseAutoplay();
            });
            
            container.addEventListener('mouseleave', () => {
                if (this.settings.autoplay) {
                    this.resumeAutoplay();
                }
            });
            
            // Pausar autoplay al hacer clic en los botones
            const prevBtn = container.querySelector('.slidercards3d-btn-prev');
            const nextBtn = container.querySelector('.slidercards3d-btn-next');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    this.pauseAutoplay();
                    setTimeout(() => this.resumeAutoplay(), this.settings.autoplay_interval);
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    this.pauseAutoplay();
                    setTimeout(() => this.resumeAutoplay(), this.settings.autoplay_interval);
                });
            }
            
            // Recalcular posiciones al redimensionar la ventana
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    this.updateCards();
                }, 250);
            });

            // Touch events para móviles
            let touchStartX = 0;
            let touchEndX = 0;
            
            if (container) {
                container.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                    this.pauseAutoplay();
                });
                
                container.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    this.handleSwipe();
                    setTimeout(() => {
                        if (this.settings.autoplay) {
                            this.resumeAutoplay();
                        }
                    }, this.settings.autoplay_interval);
                });
            }
            
            this.handleSwipe = () => {
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        this.next();
                    } else {
                        this.prev();
                    }
                }
            };
        }
    };

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            SliderCards3D.init();
        });
    } else {
        SliderCards3D.init();
    }

})();

