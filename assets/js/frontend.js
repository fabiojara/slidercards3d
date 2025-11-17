/**
 * JavaScript del slider 3D en el frontend
 * Basado en: https://codepen.io/Nidal95/pen/RNNgWNM
 * Soporta múltiples instancias en la misma página
 */

(function() {
    'use strict';

    // Clase para cada instancia del slider
    class SliderCards3DInstance {
        constructor(container) {
            this.container = container;
            this.instanceId = container.dataset.instanceId || 'slidercards3d-1';
            this.items = [];
            this.currentIndex = 0;
            this.isAnimating = false;
            this.type = container.dataset.type || 'all';
            this.settings = {
                separation_desktop: 100,
                separation_tablet: 70,
                separation_mobile: 50,
                autoplay: false,
                autoplay_interval: 3000,
                darkness_intensity: 25
            };
            this.autoplayTimer = null;
            this.isPaused = false;
        }

        init() {
            // Cargar configuración primero
            this.loadSettings().then(() => {
                this.loadItems();
                this.bindEvents();
            });
        }

        loadSettings() {
            return fetch(slidercards3dData.apiUrl + 'settings')
                .then(response => response.json())
                .then(data => {
                    this.settings = {
                        separation_desktop: data.separation_desktop || 100,
                        separation_tablet: data.separation_tablet || 70,
                        separation_mobile: data.separation_mobile || 50,
                        autoplay: data.autoplay || false,
                        autoplay_interval: data.autoplay_interval || 3000,
                        darkness_intensity: data.darkness_intensity || 25
                    };
                })
                .catch(() => {
                    console.warn('No se pudieron cargar los ajustes, usando valores por defecto');
                });
        }

        startAutoplay() {
            if (!this.settings.autoplay || this.items.length <= 1) return;

            this.stopAutoplay();

            this.autoplayTimer = setInterval(() => {
                if (!this.isPaused && !this.isAnimating) {
                    this.next();
                }
            }, this.settings.autoplay_interval);
        }

        stopAutoplay() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        }

        pauseAutoplay() {
            this.isPaused = true;
        }

        resumeAutoplay() {
            this.isPaused = false;
        }

        loadItems() {
            const promises = [];

            if (this.type === 'all' || this.type === 'images') {
                promises.push(this.loadImages());
            }

            if (this.type === 'all' || this.type === 'pages') {
                promises.push(this.loadPages());
            }

            Promise.all(promises).then(() => {
                if (this.items.length > 0) {
                    this.render();
                    if (this.settings.autoplay) {
                        this.startAutoplay();
                    }
                } else {
                    this.showEmpty();
                }
            }).catch(error => {
                console.error('Error al cargar items:', error);
                this.showEmpty();
            });
        }

        loadImages() {
            return fetch(slidercards3dData.apiUrl + 'selection?type=image')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al obtener selección de imágenes');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.ids && data.ids.length > 0) {
                        return Promise.all(
                            data.ids.map(id => this.getImageData(id))
                        );
                    }
                    return [];
                })
                .then(images => {
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
        }

        loadPages() {
            return fetch(slidercards3dData.apiUrl + 'selection?type=page')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al obtener selección de páginas');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.ids && data.ids.length > 0) {
                        return Promise.all(
                            data.ids.map(id => this.getPageData(id))
                        );
                    }
                    return [];
                })
                .then(pages => {
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
        }

        getImageData(id) {
            let baseUrl = slidercards3dData.apiUrl.replace('/slidercards3d/v1/', '');
            if (!baseUrl.endsWith('/')) {
                baseUrl += '/';
            }
            const mediaUrl = baseUrl + `wp/v2/media/${id}`;

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
                    return null;
                });
        }

        getPageData(id) {
            let baseUrl = slidercards3dData.apiUrl.replace('/slidercards3d/v1/', '');
            if (!baseUrl.endsWith('/')) {
                baseUrl += '/';
            }
            const pageUrl = baseUrl + `wp/v2/pages/${id}`;

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
                    return null;
                });
        }

        render() {
            const slider = this.container.querySelector(`#${this.instanceId}-slider`);
            if (!slider) return;

            slider.innerHTML = '';

            // Crear cards
            this.items.forEach((item, index) => {
                const card = this.createCard(item, index);
                slider.appendChild(card);
            });

            // Crear controles si no existen
            const wrapper = this.container.querySelector('.slidercards3d-wrapper');
            if (wrapper && !wrapper.querySelector('.slidercards3d-controls')) {
                this.createControls();
            }

            // Crear indicadores
            this.createIndicators();

            // Posicionar cards
            this.updateCards();
        }

        createCard(item, index) {
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
                const linkIcon = document.createElement('img');
                linkIcon.src = slidercards3dData.pluginUrl + 'assets/icons/arrow-top-right-on-square.svg';
                linkIcon.width = 16;
                linkIcon.height = 16;
                linkIcon.alt = '';
                linkIcon.className = 'slidercards3d-icon-inline';
                linkIcon.onerror = function() {
                    this.src = 'https://api.iconify.design/heroicons-outline/arrow-top-right-on-square.svg?width=16&height=16';
                };
                link.textContent = 'Ver página ';
                link.appendChild(linkIcon);
                overlay.appendChild(link);

                card.appendChild(overlay);
            }

            card.addEventListener('click', () => {
                const isActive = index === this.currentIndex;

                if (item.type === 'page' && item.link) {
                    window.location.href = item.link;
                } else if (isActive) {
                    this.openLightbox(item);
                } else {
                    this.goTo(index);
                }
            });

            return card;
        }

        createControls() {
            const wrapper = this.container.querySelector('.slidercards3d-wrapper');
            if (!wrapper) return;

            let controls = wrapper.querySelector('.slidercards3d-controls');
            if (controls) return; // Ya existen controles

            controls = document.createElement('div');
            controls.className = 'slidercards3d-controls';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'slidercards3d-btn-prev';
            prevBtn.setAttribute('aria-label', 'Anterior');
            prevBtn.dataset.instance = this.instanceId;
            prevBtn.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>';
            prevBtn.addEventListener('click', () => this.prev());

            const nextBtn = document.createElement('button');
            nextBtn.className = 'slidercards3d-btn-next';
            nextBtn.setAttribute('aria-label', 'Siguiente');
            nextBtn.dataset.instance = this.instanceId;
            nextBtn.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>';
            nextBtn.addEventListener('click', () => this.next());

            controls.appendChild(prevBtn);
            controls.appendChild(nextBtn);
            wrapper.appendChild(controls);
        }

        createIndicators() {
            const wrapper = this.container.querySelector('.slidercards3d-wrapper');
            if (!wrapper) return;

            let indicators = wrapper.querySelector('.slidercards3d-indicators');
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

            wrapper.appendChild(indicators);
        }

        updateCards() {
            const cards = this.container.querySelectorAll('.slidercards3d-card');
            const total = cards.length;

            if (total === 0) return;

            cards.forEach((card, index) => {
                let offset = index - this.currentIndex;

                if (offset > total / 2) {
                    offset = offset - total;
                } else if (offset < -total / 2) {
                    offset = offset + total;
                }

                const absOffset = Math.abs(offset);

                let translateZ = -absOffset * 100;
                const isMobile = window.innerWidth <= 480;
                const isTablet = window.innerWidth <= 768 && window.innerWidth > 480;
                const separationX = isMobile ? this.settings.separation_mobile :
                                   (isTablet ? this.settings.separation_tablet :
                                    this.settings.separation_desktop);
                let translateX = offset * separationX;
                let rotateY = offset * 15;
                let opacity = 1;
                let scale = 1;
                let brightness = 1;

                const darknessFactor = this.settings.darkness_intensity / 100;

                if (absOffset > 3) {
                    opacity = 0;
                    scale = 0.8;
                    brightness = 0.2;
                } else if (absOffset > 0) {
                    opacity = 1 - (absOffset * 0.2);
                    scale = 1 - (absOffset * 0.05);
                    const baseDarkness = absOffset * 0.3;
                    const intensityMultiplier = 0.3 + (darknessFactor * 0.7);
                    const adjustedDarkness = baseDarkness * intensityMultiplier;
                    brightness = Math.max(0.2, 1 - adjustedDarkness);
                }

                card.style.transform = `
                    translateX(${translateX}px)
                    translateZ(${translateZ}px)
                    rotateY(${rotateY}deg)
                    scale(${scale})
                `;
                card.style.opacity = opacity;
                card.style.filter = `brightness(${brightness})`;
                card.style.zIndex = total - absOffset;
            });

            // Actualizar indicadores
            const indicatorElements = this.container.querySelectorAll('.slidercards3d-indicator');
            indicatorElements.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === this.currentIndex);
            });

            // Botones siempre habilitados en modo infinito
            const prevBtn = this.container.querySelector('.slidercards3d-btn-prev');
            const nextBtn = this.container.querySelector('.slidercards3d-btn-next');

            if (prevBtn) prevBtn.disabled = false;
            if (nextBtn) nextBtn.disabled = false;
        }

        next() {
            if (this.isAnimating) return;
            this.isAnimating = true;
            this.currentIndex = (this.currentIndex + 1) % this.items.length;
            this.updateCards();
            setTimeout(() => {
                this.isAnimating = false;
            }, 500);
        }

        prev() {
            if (this.isAnimating) return;
            this.isAnimating = true;
            this.currentIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
            this.updateCards();
            setTimeout(() => {
                this.isAnimating = false;
            }, 500);
        }

        goTo(index) {
            if (this.isAnimating) return;
            if (index >= 0 && index < this.items.length && index !== this.currentIndex) {
                this.isAnimating = true;
                this.currentIndex = index;
                this.updateCards();
                setTimeout(() => {
                    this.isAnimating = false;
                }, 500);
            }
        }

        showEmpty() {
            const slider = this.container.querySelector(`#${this.instanceId}-slider`);
            if (slider) {
                slider.innerHTML = `
                    <div class="slidercards3d-empty">
                        <div class="slidercards3d-empty-icon">🎴</div>
                        <div class="slidercards3d-empty-title">No hay contenido</div>
                        <div class="slidercards3d-empty-text">
                            <p>Selecciona imágenes o páginas en el panel de administración.</p>
                        </div>
                    </div>
                `;
            }
        }

        bindEvents() {
            // Navegación con teclado (solo para el slider activo con hover)
            const handleKeydown = (e) => {
                // Solo procesar si el mouse está sobre este contenedor
                if (!this.container.matches(':hover')) return;
                
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
            };
            document.addEventListener('keydown', handleKeydown);

            // Pausar autoplay al interactuar
            this.container.addEventListener('mouseenter', () => {
                this.pauseAutoplay();
            });

            this.container.addEventListener('mouseleave', () => {
                if (this.settings.autoplay) {
                    this.resumeAutoplay();
                }
            });

            // Recalcular posiciones al redimensionar
            let resizeTimeout;
            const handleResize = () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    this.updateCards();
                }, 250);
            };
            window.addEventListener('resize', handleResize);

            // Touch events para móviles
            let touchStartX = 0;
            let touchEndX = 0;

            this.container.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                this.pauseAutoplay();
            });

            this.container.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;

                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        this.next();
                    } else {
                        this.prev();
                    }
                }

                setTimeout(() => {
                    if (this.settings.autoplay) {
                        this.resumeAutoplay();
                    }
                }, this.settings.autoplay_interval);
            });
        }

        openLightbox(item) {
            const overlay = document.createElement('div');
            overlay.className = 'slidercards3d-lightbox';
            overlay.id = `${this.instanceId}-lightbox`;

            const container = document.createElement('div');
            container.className = 'slidercards3d-lightbox-container';

            const closeBtn = document.createElement('button');
            closeBtn.className = 'slidercards3d-lightbox-close';
            closeBtn.setAttribute('aria-label', 'Cerrar');
            closeBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18L18 6M6 6l12 12"/></svg>';
            closeBtn.addEventListener('click', () => this.closeLightbox());

            const img = document.createElement('img');
            img.src = item.fullUrl || item.url;
            img.alt = item.title || 'Imagen ampliada';
            img.className = 'slidercards3d-lightbox-image';

            const imageWrapper = document.createElement('div');
            imageWrapper.className = 'slidercards3d-lightbox-image-wrapper';
            imageWrapper.appendChild(img);
            imageWrapper.appendChild(closeBtn);

            container.appendChild(imageWrapper);
            overlay.appendChild(container);

            document.body.appendChild(overlay);

            setTimeout(() => {
                overlay.classList.add('active');
            }, 10);

            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    this.closeLightbox();
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);

            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    this.closeLightbox();
                }
            });

            container.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        closeLightbox() {
            const lightbox = document.getElementById(`${this.instanceId}-lightbox`);
            if (lightbox) {
                lightbox.classList.remove('active');
                setTimeout(() => {
                    lightbox.remove();
                }, 300);
            }
        }
    }

    // Inicializar todas las instancias cuando el DOM esté listo
    function initAllSliders() {
        const containers = document.querySelectorAll('.slidercards3d-container');
        containers.forEach(container => {
            const instance = new SliderCards3DInstance(container);
            instance.init();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllSliders);
    } else {
        initAllSliders();
    }

})();
