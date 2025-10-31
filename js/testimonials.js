// ========================================
// PYRAMEDIA - Testimonials Manager
// ========================================

class TestimonialsManager {
    constructor() {
        this.apiUrl = 'php/testimonials-api.php';
        this.currentPage = 1;
        this.currentLang = this.detectLanguage();
        this.rating = 5; // Default rating
        this.init();
    }

    detectLanguage() {
        const path = window.location.pathname;
        return path.includes('-ar.html') || path.includes('/ar/') ? 'ar' : 'en';
    }

    async init() {
        await this.loadTestimonials();
        await this.loadStats();
        this.setupRatingStars();
        this.setupForm();
    }

    /**
     * Load testimonials
     */
    async loadTestimonials(page = 1) {
        try {
            const response = await fetch(`${this.apiUrl}?action=list&lang=${this.currentLang}&page=${page}&per_page=12&status=approved`);
            const result = await response.json();

            if (result.success) {
                this.renderTestimonials(result.data.testimonials);
                this.renderPagination(result.data.pagination);
                this.currentPage = page;
            }
        } catch (error) {
            console.error('Error loading testimonials:', error);
        }
    }

    /**
     * Load stats
     */
    async loadStats() {
        try {
            const response = await fetch(`${this.apiUrl}?action=stats`);
            const result = await response.json();

            if (result.success) {
                const stats = result.data;
                document.getElementById('totalTestimonials').textContent = stats.total_testimonials + '+';
                document.getElementById('avgRating').textContent = stats.average_rating;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    /**
     * Render testimonials
     */
    renderTestimonials(testimonials) {
        const container = document.getElementById('testimonialsContainer');

        if (testimonials.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                    <p class="text-xl text-gray-600">No testimonials yet. Be the first to share your experience!</p>
                </div>
            `;
            return;
        }

        container.innerHTML = testimonials.map(testimonial => `
            <div class="testimonial-card bg-white rounded-2xl shadow-lg p-8 relative" data-aos="fade-up">
                <!-- Quote Icon -->
                <div class="quote-icon">
                    <i class="fas fa-quote-left"></i>
                </div>

                <!-- Rating -->
                <div class="star-rating mb-4 mt-4">
                    ${this.renderStars(testimonial.rating)}
                </div>

                <!-- Testimonial Text -->
                <p class="text-gray-700 mb-6 line-clamp-5 leading-relaxed">
                    "${testimonial.testimonial_text}"
                </p>

                <!-- Client Info -->
                <div class="flex items-center border-t pt-6">
                    <img
                        src="${testimonial.client_avatar}"
                        alt="${testimonial.client_name}"
                        class="w-16 h-16 rounded-full mr-4 object-cover"
                    >
                    <div>
                        <h4 class="font-bold text-gray-800">${testimonial.client_name}</h4>
                        <p class="text-sm text-gray-600">${testimonial.client_position || ''}</p>
                        <p class="text-sm font-semibold text-orange-500">${testimonial.client_company || ''}</p>
                    </div>
                </div>

                <!-- Project Type Badge -->
                ${testimonial.project_type ? `
                    <div class="mt-4">
                        <span class="inline-block px-3 py-1 bg-orange-100 text-orange-600 text-xs font-semibold rounded-full">
                            ${testimonial.project_type}
                        </span>
                    </div>
                ` : ''}

                <!-- Views -->
                ${testimonial.views ? `
                    <div class="mt-4 text-xs text-gray-400">
                        <i class="far fa-eye mr-1"></i>${testimonial.views} views
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    /**
     * Render star rating
     */
    renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="fas fa-star ${i <= rating ? 'text-yellow-400' : 'text-gray-300'}"></i>`;
        }
        return stars;
    }

    /**
     * Render pagination
     */
    renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');

        if (pagination.total_pages <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '<div class="flex justify-center items-center space-x-2">';

        // Previous button
        if (pagination.has_prev) {
            html += `
                <button
                    onclick="testimonialsManager.loadTestimonials(${pagination.current_page - 1})"
                    class="px-4 py-2 border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;
        }

        // Page numbers
        for (let i = 1; i <= pagination.total_pages; i++) {
            const active = i === pagination.current_page;
            html += `
                <button
                    onclick="testimonialsManager.loadTestimonials(${i})"
                    class="px-4 py-2 rounded-lg font-semibold transition-all ${
                        active
                            ? 'gradient-bg text-white'
                            : 'border-2 border-gray-300 hover:border-orange-500 hover:text-orange-500'
                    }">
                    ${i}
                </button>
            `;
        }

        // Next button
        if (pagination.has_next) {
            html += `
                <button
                    onclick="testimonialsManager.loadTestimonials(${pagination.current_page + 1})"
                    class="px-4 py-2 border-2 border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
        }

        html += '</div>';
        container.innerHTML = html;
    }

    /**
     * Setup rating stars
     */
    setupRatingStars() {
        const stars = document.querySelectorAll('#ratingStars i');
        const ratingValue = document.getElementById('ratingValue');
        const ratingText = document.getElementById('ratingText');

        const ratingTexts = {
            1: 'Poor',
            2: 'Fair',
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent'
        };

        // Set default to 5 stars
        this.updateStars(5);
        ratingText.textContent = ratingTexts[5];

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = parseInt(star.dataset.rating);
                this.rating = rating;
                ratingValue.value = rating;
                this.updateStars(rating);
                ratingText.textContent = ratingTexts[rating];
            });

            star.addEventListener('mouseenter', () => {
                const rating = parseInt(star.dataset.rating);
                this.updateStars(rating);
            });
        });

        document.getElementById('ratingStars').addEventListener('mouseleave', () => {
            this.updateStars(this.rating);
        });
    }

    updateStars(rating) {
        const stars = document.querySelectorAll('#ratingStars i');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    /**
     * Setup form submission
     */
    setupForm() {
        const form = document.getElementById('testimonialForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Get form data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Validate testimonial length
            if (data.testimonial_text.length < 50) {
                alert('Please write at least 50 characters in your testimonial.');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(`${this.apiUrl}?action=submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    document.getElementById('successMessage').classList.remove('hidden');
                    form.classList.add('hidden');

                    // Scroll to success message
                    document.getElementById('successMessage').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // Reset form
                    form.reset();
                    this.rating = 5;
                    this.updateStars(5);
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                console.error('Error submitting testimonial:', error);
                alert('An error occurred. Please try again.');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
}

// ==========================================
// TESTIMONIALS CAROUSEL (For Homepage)
// ==========================================

class TestimonialsCarousel {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;

        this.apiUrl = 'php/testimonials-api.php';
        this.currentLang = window.location.pathname.includes('-ar') ? 'ar' : 'en';
        this.currentIndex = 0;
        this.testimonials = [];
        this.autoplayInterval = null;

        this.init();
    }

    async init() {
        await this.loadFeaturedTestimonials();
        this.setupControls();
        this.startAutoplay();
    }

    async loadFeaturedTestimonials() {
        try {
            const response = await fetch(`${this.apiUrl}?action=featured&lang=${this.currentLang}&limit=5`);
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                this.testimonials = result.data;
                this.render();
            }
        } catch (error) {
            console.error('Error loading featured testimonials:', error);
        }
    }

    render() {
        if (this.testimonials.length === 0) return;

        this.container.innerHTML = `
            <div class="relative">
                <!-- Testimonial -->
                <div id="carouselContent" class="text-center px-8">
                    ${this.renderTestimonial(this.testimonials[0])}
                </div>

                <!-- Navigation -->
                <button id="prevBtn" class="absolute left-0 top-1/2 -translate-y-1/2 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="nextBtn" class="absolute right-0 top-1/2 -translate-y-1/2 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <!-- Dots -->
                <div id="carouselDots" class="flex justify-center space-x-2 mt-8">
                    ${this.testimonials.map((_, i) => `
                        <button class="w-3 h-3 rounded-full transition-all ${i === 0 ? 'bg-orange-500 w-8' : 'bg-gray-300'}" data-index="${i}"></button>
                    `).join('')}
                </div>
            </div>
        `;
    }

    renderTestimonial(testimonial) {
        return `
            <div class="max-w-4xl mx-auto">
                <!-- Stars -->
                <div class="flex justify-center mb-6">
                    ${[...Array(5)].map((_, i) =>
                        `<i class="fas fa-star text-2xl ${i < testimonial.rating ? 'text-yellow-400' : 'text-gray-300'} mx-1"></i>`
                    ).join('')}
                </div>

                <!-- Quote -->
                <p class="text-2xl md:text-3xl text-gray-700 italic mb-8 leading-relaxed">
                    "${testimonial.testimonial_text}"
                </p>

                <!-- Client -->
                <div class="flex items-center justify-center">
                    <img src="${testimonial.client_avatar}" alt="${testimonial.client_name}" class="w-20 h-20 rounded-full mr-4">
                    <div class="text-left">
                        <h4 class="text-xl font-bold text-gray-800">${testimonial.client_name}</h4>
                        <p class="text-gray-600">${testimonial.client_position}</p>
                        <p class="text-orange-500 font-semibold">${testimonial.client_company}</p>
                    </div>
                </div>
            </div>
        `;
    }

    setupControls() {
        document.getElementById('prevBtn')?.addEventListener('click', () => this.prev());
        document.getElementById('nextBtn')?.addEventListener('click', () => this.next());

        document.querySelectorAll('#carouselDots button').forEach((dot, index) => {
            dot.addEventListener('click', () => this.goTo(index));
        });
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.testimonials.length;
        this.update();
        this.resetAutoplay();
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.testimonials.length) % this.testimonials.length;
        this.update();
        this.resetAutoplay();
    }

    goTo(index) {
        this.currentIndex = index;
        this.update();
        this.resetAutoplay();
    }

    update() {
        const content = document.getElementById('carouselContent');
        if (content) {
            content.innerHTML = this.renderTestimonial(this.testimonials[this.currentIndex]);
        }

        // Update dots
        document.querySelectorAll('#carouselDots button').forEach((dot, i) => {
            if (i === this.currentIndex) {
                dot.classList.add('bg-orange-500', 'w-8');
                dot.classList.remove('bg-gray-300');
            } else {
                dot.classList.remove('bg-orange-500', 'w-8');
                dot.classList.add('bg-gray-300');
            }
        });
    }

    startAutoplay() {
        this.autoplayInterval = setInterval(() => this.next(), 5000);
    }

    resetAutoplay() {
        clearInterval(this.autoplayInterval);
        this.startAutoplay();
    }
}

// ==========================================
// INITIALIZE
// ==========================================

let testimonialsManager;
let testimonialsCarousel;

document.addEventListener('DOMContentLoaded', () => {
    // Initialize testimonials page
    if (document.getElementById('testimonialsContainer')) {
        testimonialsManager = new TestimonialsManager();
    }

    // Initialize carousel (for homepage)
    if (document.getElementById('testimonialsCarousel')) {
        testimonialsCarousel = new TestimonialsCarousel('testimonialsCarousel');
    }
});
