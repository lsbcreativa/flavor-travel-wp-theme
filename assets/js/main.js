/* =============================================
   AGENCIA DE VIAJES - JAVASCRIPT PRINCIPAL
   ============================================= */

document.addEventListener('DOMContentLoaded', function() {

    // Inicializar Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // =============================================
    // CONTINENT TABS
    // =============================================
    const continentTabs = document.querySelectorAll('.continent-tab');
    const continentPanels = document.querySelectorAll('.continent-panel');

    if (continentTabs.length > 0) {
        continentTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const continent = this.dataset.continent;

                // Remove active from all tabs
                continentTabs.forEach(t => t.classList.remove('continent-tab--active'));
                // Add active to clicked tab
                this.classList.add('continent-tab--active');

                // Hide all panels
                continentPanels.forEach(panel => {
                    panel.classList.remove('continent-panel--active');
                });

                // Show selected panel
                const targetPanel = document.querySelector(`[data-panel="${continent}"]`);
                if (targetPanel) {
                    targetPanel.classList.add('continent-panel--active');

                    // Reinitialize icons in the new panel
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });
        });
    }

    // =============================================
    // ANIMATED COUNTER
    // =============================================
    const counters = document.querySelectorAll('[data-count]');

    const animateCounter = (counter) => {
        const target = parseInt(counter.dataset.count);
        const duration = 2000;
        const start = 0;
        const startTime = performance.now();

        const updateCounter = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function for smooth animation
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.floor(start + (target - start) * easeOutQuart);

            // Format number with K suffix for thousands
            if (target >= 1000) {
                counter.textContent = current >= 1000 ? Math.floor(current / 1000) + 'K+' : current;
            } else {
                counter.textContent = current + '+';
            }

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        };

        requestAnimationFrame(updateCounter);
    };

    // Intersection Observer for counters
    if (counters.length > 0) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => counterObserver.observe(counter));
    }

    // =============================================
    // TESTIMONIALS SLIDER (Improved)
    // =============================================
    const testimonialsTrack = document.querySelector('.testimonials-track');
    const testimonialCards = document.querySelectorAll('.testimonials-track .testimonial-card');
    const prevBtn = document.querySelector('.testimonials-nav__btn--prev');
    const nextBtn = document.querySelector('.testimonials-nav__btn--next');

    if (testimonialsTrack && testimonialCards.length > 0) {
        let currentIndex = 0;
        const totalSlides = testimonialCards.length;

        const updateSlider = () => {
            testimonialsTrack.style.transform = `translateX(-${currentIndex * 100}%)`;
        };

        const nextSlide = () => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlider();
        };

        const prevSlide = () => {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateSlider();
        };

        if (nextBtn) nextBtn.addEventListener('click', nextSlide);
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);

        // Auto-advance testimonials
        let autoSlide = setInterval(nextSlide, 6000);

        // Pause on hover
        testimonialsTrack.addEventListener('mouseenter', () => clearInterval(autoSlide));
        testimonialsTrack.addEventListener('mouseleave', () => {
            autoSlide = setInterval(nextSlide, 6000);
        });

        // Touch support
        let touchStartX = 0;
        let touchEndX = 0;

        testimonialsTrack.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        testimonialsTrack.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                nextSlide();
            } else if (touchEndX - touchStartX > 50) {
                prevSlide();
            }
        });
    }

    // =============================================
    // HEADER SCROLL EFFECT
    // =============================================
    const header = document.getElementById('header');
    
    function handleHeaderScroll() {
        if (window.scrollY > 80) {
            header.classList.add('header--scrolled');
        } else {
            header.classList.remove('header--scrolled');
        }
    }

    window.addEventListener('scroll', handleHeaderScroll);
    handleHeaderScroll(); // Check on load

    // =============================================
    // MOBILE MENU
    // =============================================
    const mobileToggle = document.querySelector('.mobile-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileClose = document.querySelector('.mobile-menu__close');
    const mobileLinks = document.querySelectorAll('.mobile-menu__link');

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', function() {
            mobileMenu.classList.add('mobile-menu--open');
            document.body.style.overflow = 'hidden';
        });

        if (mobileClose) {
            mobileClose.addEventListener('click', function() {
                mobileMenu.classList.remove('mobile-menu--open');
                document.body.style.overflow = '';
            });
        }

        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('mobile-menu--open');
                document.body.style.overflow = '';
            });
        });
    }

    // =============================================
    // HERO SLIDER
    // =============================================
    const heroSlider = document.querySelector('.hero__slider');
    
    if (heroSlider) {
        const slides = heroSlider.querySelectorAll('.hero__slide');
        const dots = document.querySelectorAll('.hero__dot');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('hero__slide--active', i === index);
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('hero__dot--active', i === index);
            });
            currentSlide = index;
        }

        function nextSlide() {
            const next = (currentSlide + 1) % slides.length;
            showSlide(next);
        }

        function startSlider() {
            slideInterval = setInterval(nextSlide, 5000);
        }

        function stopSlider() {
            clearInterval(slideInterval);
        }

        // Click on dots
        dots.forEach((dot, i) => {
            dot.addEventListener('click', function() {
                showSlide(i);
                stopSlider();
                startSlider();
            });
        });

        // Pause on hover
        heroSlider.addEventListener('mouseenter', stopSlider);
        heroSlider.addEventListener('mouseleave', startSlider);

        // Start slider
        if (slides.length > 1) {
            startSlider();
        }
    }

    // =============================================
    // SMOOTH SCROLL
    // =============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href === '#') return;
            
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                const headerHeight = header ? header.offsetHeight : 0;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // =============================================
    // WISHLIST TOGGLE
    // =============================================
    const wishlistButtons = document.querySelectorAll('.tour-card__wishlist');

    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.toggle('tour-card__wishlist--active');
            
            // Aquí puedes agregar lógica para guardar en localStorage o enviar al servidor
            const tourCard = this.closest('.tour-card');
            const tourId = tourCard ? tourCard.dataset.tourId : null;
            
            if (tourId) {
                toggleWishlist(tourId);
            }
        });
    });

    function toggleWishlist(tourId) {
        let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        
        if (wishlist.includes(tourId)) {
            wishlist = wishlist.filter(id => id !== tourId);
        } else {
            wishlist.push(tourId);
        }
        
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
    }

    // Load wishlist state on page load
    function loadWishlistState() {
        const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        
        document.querySelectorAll('.tour-card').forEach(card => {
            const tourId = card.dataset.tourId;
            if (tourId && wishlist.includes(tourId)) {
                const button = card.querySelector('.tour-card__wishlist');
                if (button) {
                    button.classList.add('tour-card__wishlist--active');
                }
            }
        });
    }

    loadWishlistState();

    // =============================================
    // FORM VALIDATION
    // =============================================
    const contactForm = document.getElementById('contact-form');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset errors
            this.querySelectorAll('.form-error').forEach(el => el.remove());
            this.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(el => {
                el.style.borderColor = '';
            });

            let isValid = true;
            const formData = new FormData(this);

            // Validate required fields
            this.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    showError(field, 'Este campo es requerido');
                }
            });

            // Validate email
            const emailField = this.querySelector('[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    isValid = false;
                    showError(emailField, 'Ingresa un email válido');
                }
            }

            // Validate phone
            const phoneField = this.querySelector('[type="tel"]');
            if (phoneField && phoneField.value) {
                const phoneRegex = /^[\d\s\-\+\(\)]{7,}$/;
                if (!phoneRegex.test(phoneField.value)) {
                    isValid = false;
                    showError(phoneField, 'Ingresa un teléfono válido');
                }
            }

            if (isValid) {
                // Submit form (aquí puedes enviar por AJAX)
                console.log('Form submitted:', Object.fromEntries(formData));
                
                // Show success message
                showFormSuccess(this);
            }
        });
    }

    function showError(field, message) {
        field.style.borderColor = 'var(--error)';
        const error = document.createElement('div');
        error.className = 'form-error';
        error.textContent = message;
        field.parentNode.appendChild(error);
    }

    function showFormSuccess(form) {
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i data-lucide="check" style="width: 18px; height: 18px;"></i> Mensaje enviado';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            form.reset();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 3000);
    }

    // =============================================
    // SEARCH BOX
    // =============================================
    const searchForm = document.querySelector('.search-box form');

    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const params = new URLSearchParams();

            for (const [key, value] of formData) {
                if (value) {
                    params.append(key, value);
                }
            }

            // Redirect to tours page with search params
            window.location.href = 'tours.html?' + params.toString();
        });
    }

    // =============================================
    // CONTINENT-DESTINATION FILTER
    // =============================================
    const continenteSelect = document.getElementById('continente-select');
    const destinoSelect = document.getElementById('destino-select');

    if (continenteSelect && destinoSelect) {
        // Store all original options and optgroups
        const allOptgroups = destinoSelect.querySelectorAll('optgroup');
        const defaultOption = destinoSelect.querySelector('option[value=""]');

        // Clone all optgroups for later use
        const optgroupsData = [];
        allOptgroups.forEach(og => {
            optgroupsData.push({
                continent: og.dataset.continent,
                element: og.cloneNode(true)
            });
        });

        function filterDestinations(selectedContinent) {
            // Clear current options (except the default)
            destinoSelect.innerHTML = '';

            // Add back the default option
            if (defaultOption) {
                destinoSelect.appendChild(defaultOption.cloneNode(true));
            }

            if (!selectedContinent) {
                // Show all optgroups
                optgroupsData.forEach(data => {
                    destinoSelect.appendChild(data.element.cloneNode(true));
                });
            } else {
                // Show only matching optgroup's options (without optgroup wrapper for cleaner look)
                const matchingData = optgroupsData.find(data => data.continent === selectedContinent);

                if (matchingData) {
                    // Add options directly without optgroup
                    const options = matchingData.element.querySelectorAll('option');
                    options.forEach(opt => {
                        destinoSelect.appendChild(opt.cloneNode(true));
                    });
                }
            }

            // Reset destination selection
            destinoSelect.value = '';
        }

        // Listen for continent changes
        continenteSelect.addEventListener('change', function() {
            filterDestinations(this.value);
        });

        // Initial state - if continent is pre-selected
        if (continenteSelect.value) {
            filterDestinations(continenteSelect.value);
        }
    }

    // =============================================
    // ANIMATE ON SCROLL (Simple implementation)
    // =============================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.dest-card, .tour-card, .why-item, .testimonial-card').forEach(el => {
        el.style.opacity = '0';
        observer.observe(el);
    });

    // =============================================
    // FILTERS (For tours/destinos pages)
    // =============================================
    const filterButtons = document.querySelectorAll('[data-filter]');
    const filterableItems = document.querySelectorAll('[data-category]');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            filterableItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // =============================================
    // LOAD MORE (For listings)
    // =============================================
    const loadMoreBtn = document.querySelector('.load-more-btn');
    const itemsContainer = document.querySelector('.items-container');
    let currentPage = 1;

    if (loadMoreBtn && itemsContainer) {
        loadMoreBtn.addEventListener('click', async function() {
            currentPage++;
            this.disabled = true;
            this.innerHTML = '<i data-lucide="loader" style="width: 18px; height: 18px;" class="spin"></i> Cargando...';
            
            // Simular carga (reemplazar con llamada AJAX real)
            setTimeout(() => {
                // Aquí cargarías más items del servidor
                this.disabled = false;
                this.innerHTML = 'Cargar más <i data-lucide="arrow-down" style="width: 18px; height: 18px;"></i>';
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 1000);
        });
    }

    // =============================================
    // DATE PICKER (For search)
    // =============================================
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Set min date to today
        const today = new Date().toISOString().split('T')[0];
        input.setAttribute('min', today);
    });

    // =============================================
    // TESTIMONIALS SLIDER (If multiple)
    // =============================================
    const testimonialsSlider = document.querySelector('.testimonials-slider');
    
    if (testimonialsSlider) {
        const testimonials = testimonialsSlider.querySelectorAll('.testimonial-card');
        const prevBtn = testimonialsSlider.querySelector('.slider-prev');
        const nextBtn = testimonialsSlider.querySelector('.slider-next');
        let currentTestimonial = 0;

        function showTestimonial(index) {
            testimonials.forEach((t, i) => {
                t.style.display = i === index ? 'block' : 'none';
            });
        }

        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                currentTestimonial = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
                showTestimonial(currentTestimonial);
            });

            nextBtn.addEventListener('click', () => {
                currentTestimonial = (currentTestimonial + 1) % testimonials.length;
                showTestimonial(currentTestimonial);
            });
        }

        showTestimonial(0);
    }

    // =============================================
    // NEWSLETTER FORM
    // =============================================
    const newsletterForm = document.querySelector('.newsletter__form');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const emailInput = this.querySelector('.newsletter__input');
            const submitBtn = this.querySelector('.newsletter__btn');
            const email = emailInput.value;

            if (email) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i data-lucide="check" style="width: 18px; height: 18px;"></i> Suscrito';
                submitBtn.disabled = true;

                // Simulate API call
                setTimeout(() => {
                    emailInput.value = '';
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 3000);
            }
        });
    }

    // =============================================
    // BACK TO TOP
    // =============================================
    const backToTop = document.querySelector('.back-to-top');
    
    if (backToTop) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // =============================================
    // GALLERY LIGHTBOX (For tour detail)
    // =============================================
    const galleryItems = document.querySelectorAll('[data-lightbox]');
    
    if (galleryItems.length > 0) {
        galleryItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const imgSrc = this.href || this.dataset.src;
                openLightbox(imgSrc);
            });
        });
    }

    function openLightbox(src) {
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox__overlay"></div>
            <div class="lightbox__content">
                <img src="${src}" alt="">
                <button class="lightbox__close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden';
        
        lightbox.querySelector('.lightbox__overlay').addEventListener('click', closeLightbox);
        lightbox.querySelector('.lightbox__close').addEventListener('click', closeLightbox);
        
        function closeLightbox() {
            lightbox.remove();
            document.body.style.overflow = '';
        }
    }

    // =============================================
    // COPY TO CLIPBOARD (For sharing)
    // =============================================
    const copyButtons = document.querySelectorAll('[data-copy]');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const textToCopy = this.dataset.copy || window.location.href;
            
            try {
                await navigator.clipboard.writeText(textToCopy);
                const originalText = this.innerHTML;
                this.innerHTML = '<i data-lucide="check" style="width: 16px; height: 16px;"></i> Copiado';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
            }
        });
    });

    // =============================================
    // PRICE RANGE SLIDER (For filters)
    // =============================================
    const priceRange = document.querySelector('.price-range');
    const priceMin = document.querySelector('.price-min');
    const priceMax = document.querySelector('.price-max');
    
    if (priceRange && priceMin && priceMax) {
        priceRange.addEventListener('input', function() {
            priceMin.textContent = '$' + this.min;
            priceMax.textContent = '$' + this.value;
        });
    }

});

// =============================================
// UTILITY FUNCTIONS
// =============================================

// Format price
function formatPrice(price, currency = 'USD') {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: currency
    }).format(price);
}

// Format date
function formatDate(date, locale = 'es-PE') {
    return new Date(date).toLocaleDateString(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Debounce function
function debounce(func, wait) {
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

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// =============================================
// COUNTDOWN TIMER (For offers section)
// =============================================
(function initCountdowns() {
    const countdownElements = document.querySelectorAll('.offer-v2__countdown');

    if (countdownElements.length === 0) return;

    countdownElements.forEach(countdown => {
        const endDateStr = countdown.dataset.endDate;
        if (!endDateStr) return;

        const endDate = new Date(endDateStr + 'T23:59:59').getTime();

        const daysEl = countdown.querySelector('[data-days]');
        const hoursEl = countdown.querySelector('[data-hours]');
        const minsEl = countdown.querySelector('[data-mins]');
        const secsEl = countdown.querySelector('[data-secs]');

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endDate - now;

            if (distance < 0) {
                // Offer expired
                if (daysEl) daysEl.textContent = '00';
                if (hoursEl) hoursEl.textContent = '00';
                if (minsEl) minsEl.textContent = '00';
                if (secsEl) secsEl.textContent = '00';
                countdown.classList.add('expired');
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minsEl) minsEl.textContent = String(minutes).padStart(2, '0');
            if (secsEl) secsEl.textContent = String(seconds).padStart(2, '0');
        }

        // Initial update
        updateCountdown();

        // Update every second
        setInterval(updateCountdown, 1000);
    });
})();

// =============================================
// ANIMATE OFFER CARDS ON SCROLL
// =============================================
(function initOfferAnimations() {
    const offerCards = document.querySelectorAll('.offer-v2');

    if (offerCards.length === 0) return;

    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('animate-in');
                }, index * 100);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    offerCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        observer.observe(card);
    });
})();

// Add animation styles dynamically
(function addAnimationStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .offer-v2.animate-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .offer-v2__countdown.expired {
            opacity: 0.5;
        }

        .offer-v2__countdown.expired::after {
            content: 'Oferta expirada';
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.7);
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: inherit;
        }
    `;
    document.head.appendChild(style);
})();
