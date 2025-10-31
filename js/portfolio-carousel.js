// ========================================
// PYRAMEDIA - Portfolio Carousel
// ========================================

class PortfolioCarousel {
    constructor() {
        this.currentSlide = 0;
        this.itemsPerSlide = this.getItemsPerSlide();
        this.portfolioData = [
            {
                title: "TechVision UAE - Complete Rebranding",
                category: "Branding",
                image: "https://images.unsplash.com/photo-1634942537034-2531766767d1?w=800&h=600&fit=crop",
                description: "Complete brand identity redesign for leading tech company"
            },
            {
                title: "E-Commerce Fashion Platform",
                category: "Web Development",
                image: "https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800&h=600&fit=crop",
                description: "Custom e-commerce platform with AR try-on features"
            },
            {
                title: "Social Media Growth Campaign",
                category: "Social Media",
                image: "https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop",
                description: "500% engagement increase through strategic campaigns"
            },
            {
                title: "Digital Marketing ROI 400%",
                category: "Marketing",
                image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop",
                description: "Multi-channel campaign achieving exceptional ROI"
            },
            {
                title: "Corporate Video Production",
                category: "Video",
                image: "https://images.unsplash.com/photo-1492619375914-88005aa9e8fb?w=800&h=600&fit=crop",
                description: "Professional corporate video with drone footage"
            },
            {
                title: "Restaurant Branding & Website",
                category: "Branding",
                image: "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&h=600&fit=crop",
                description: "Complete branding for premium restaurant chain"
            }
        ];
        this.init();
    }

    init() {
        this.renderCarousel();
        this.renderDots();
        this.attachEvents();
        this.startAutoPlay();
    }

    getItemsPerSlide() {
        if (window.innerWidth >= 1024) return 3;
        if (window.innerWidth >= 768) return 2;
        return 1;
    }

    getTotalSlides() {
        return Math.ceil(this.portfolioData.length / this.itemsPerSlide);
    }

    renderCarousel() {
        const carousel = document.getElementById('portfolioCarousel');
        if (!carousel) return;

        carousel.innerHTML = '';

        this.portfolioData.forEach((item, index) => {
            const slideWidth = 100 / this.itemsPerSlide;
            const card = document.createElement('div');
            card.className = 'portfolio-card flex-shrink-0 px-3';
            card.style.width = `${slideWidth}%`;

            card.innerHTML = `
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 cursor-pointer h-full" onclick="window.location.href='portfolio.html'">
                    <div class="relative overflow-hidden group" style="padding-top: 66.67%;">
                        <img src="${item.image}" alt="${item.title}" class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                            <div class="text-white">
                                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm mb-2">
                                    ${item.category}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2 line-clamp-2">${item.title}</h3>
                        <p class="text-gray-600 line-clamp-2">${item.description}</p>
                        <div class="mt-4 flex items-center text-orange-500 font-semibold">
                            View Project
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </div>
            `;

            carousel.appendChild(card);
        });

        this.updateCarousel();
    }

    renderDots() {
        const dotsContainer = document.getElementById('portfolioDots');
        if (!dotsContainer) return;

        dotsContainer.innerHTML = '';

        const totalSlides = this.getTotalSlides();

        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('button');
            dot.className = `w-3 h-3 rounded-full transition-all ${i === this.currentSlide ? 'bg-orange-500 w-8' : 'bg-gray-300'}`;
            dot.onclick = () => this.goToSlide(i);
            dotsContainer.appendChild(dot);
        }
    }

    updateCarousel() {
        const carousel = document.getElementById('portfolioCarousel');
        if (!carousel) return;

        const offset = -this.currentSlide * 100;
        carousel.style.transform = `translateX(${offset}%)`;

        this.renderDots();
    }

    next() {
        const totalSlides = this.getTotalSlides();
        this.currentSlide = (this.currentSlide + 1) % totalSlides;
        this.updateCarousel();
        this.resetAutoPlay();
    }

    prev() {
        const totalSlides = this.getTotalSlides();
        this.currentSlide = (this.currentSlide - 1 + totalSlides) % totalSlides;
        this.updateCarousel();
        this.resetAutoPlay();
    }

    goToSlide(index) {
        this.currentSlide = index;
        this.updateCarousel();
        this.resetAutoPlay();
    }

    startAutoPlay() {
        this.autoPlayInterval = setInterval(() => {
            this.next();
        }, 5000); // Auto-advance every 5 seconds
    }

    resetAutoPlay() {
        clearInterval(this.autoPlayInterval);
        this.startAutoPlay();
    }

    attachEvents() {
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        });

        // Touch swipe support
        let touchStartX = 0;
        let touchEndX = 0;

        const carousel = document.getElementById('portfolioCarousel');
        if (!carousel) return;

        carousel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        carousel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe();
        });

        const handleSwipe = () => {
            if (touchEndX < touchStartX - 50) this.next();
            if (touchEndX > touchStartX + 50) this.prev();
        };

        this.handleSwipe = handleSwipe;

        // Responsive resize
        window.addEventListener('resize', () => {
            const newItemsPerSlide = this.getItemsPerSlide();
            if (newItemsPerSlide !== this.itemsPerSlide) {
                this.itemsPerSlide = newItemsPerSlide;
                this.currentSlide = 0;
                this.renderCarousel();
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('portfolioCarousel')) {
        window.portfolioCarousel = new PortfolioCarousel();
    }
});

// Export
window.PortfolioCarousel = PortfolioCarousel;
