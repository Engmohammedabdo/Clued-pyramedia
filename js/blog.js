// ========================================
// PYRAMEDIA - Blog JavaScript
// Modern ES6+ code for blog functionality
// ========================================

class BlogManager {
    constructor() {
        this.apiUrl = 'php/blog-api.php';
        this.currentPage = 1;
        this.currentLang = this.detectLanguage();
        this.init();
    }

    detectLanguage() {
        const path = window.location.pathname;
        return path.includes('-ar.html') || path.includes('/ar/') ? 'ar' : 'en';
    }

    async init() {
        await this.loadPosts();
        this.setupEventListeners();
    }

    async loadPosts(page = 1, category = null, tag = null) {
        try {
            const params = new URLSearchParams({
                action: 'list',
                lang: this.currentLang,
                page: page,
                per_page: 12
            });

            if (category) params.append('category', category);
            if (tag) params.append('tag', tag);

            const response = await fetch(`${this.apiUrl}?${params}`);
            const result = await response.json();

            if (result.success) {
                this.renderPosts(result.data.posts);
                this.renderPagination(result.data.pagination);
            }
        } catch (error) {
            console.error('Error loading posts:', error);
        }
    }

    async loadSinglePost(slug) {
        try {
            const response = await fetch(`${this.apiUrl}?action=single&lang=${this.currentLang}&slug=${slug}`);
            const result = await response.json();

            if (result.success) {
                this.renderSinglePost(result.data);
                this.incrementView(result.data.id);
            }
        } catch (error) {
            console.error('Error loading post:', error);
        }
    }

    async loadCategories() {
        try {
            const response = await fetch(`${this.apiUrl}?action=categories&lang=${this.currentLang}`);
            const result = await response.json();

            if (result.success) {
                this.renderCategories(result.data);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    async searchPosts(query) {
        try {
            const response = await fetch(`${this.apiUrl}?action=search&lang=${this.currentLang}&q=${encodeURIComponent(query)}`);
            const result = await response.json();

            if (result.success) {
                this.renderPosts(result.data.posts);
            }
        } catch (error) {
            console.error('Error searching:', error);
        }
    }

    async incrementView(postId) {
        try {
            await fetch(`${this.apiUrl}?action=increment-view&post_id=${postId}`);
        } catch (error) {
            console.error('Error incrementing view:', error);
        }
    }

    renderPosts(posts) {
        const container = document.getElementById('postsContainer');
        if (!container) return;

        container.innerHTML = posts.map(post => `
            <article class="blog-card bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300">
                <div class="relative">
                    <img src="${post.featured_image}" alt="${post.title}" class="w-full h-64 object-cover">
                    <span class="absolute top-4 left-4 px-4 py-2 rounded-full text-sm font-semibold text-white"
                          style="background-color: ${post.category_color || '#FF6B35'}">
                        ${post.category_name}
                    </span>
                </div>
                <div class="p-8">
                    <div class="flex items-center space-x-4 mb-4 text-sm text-gray-500">
                        <span><i class="far fa-calendar mr-2"></i>${post.published_at}</span>
                        <span><i class="far fa-clock mr-2"></i>${post.read_time} min</span>
                        <span><i class="far fa-eye mr-2"></i>${post.views || 0}</span>
                    </div>
                    <h2 class="text-2xl font-bold mb-3 hover:text-orange-500 transition-colors">
                        <a href="blog-post.html?slug=${post.slug}">${post.title}</a>
                    </h2>
                    <p class="text-gray-600 mb-4">${post.excerpt}</p>
                    <div class="flex items-center justify-between">
                        <a href="blog-post.html?slug=${post.slug}"
                           class="text-orange-500 font-semibold hover:text-orange-600">
                            Read More <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </article>
        `).join('');
    }

    renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        if (!container || pagination.total_pages <= 1) return;

        let html = '<div class="flex justify-center space-x-2 mt-12">';

        if (pagination.has_prev) {
            html += `<button onclick="blogManager.loadPosts(${pagination.current_page - 1})"
                            class="px-4 py-2 border rounded-lg hover:bg-orange-500 hover:text-white transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>`;
        }

        for (let i = 1; i <= pagination.total_pages; i++) {
            const active = i === pagination.current_page ? 'bg-orange-500 text-white' : 'border hover:bg-gray-100';
            html += `<button onclick="blogManager.loadPosts(${i})"
                            class="px-4 py-2 rounded-lg ${active} transition-colors">
                        ${i}
                    </button>`;
        }

        if (pagination.has_next) {
            html += `<button onclick="blogManager.loadPosts(${pagination.current_page + 1})"
                            class="px-4 py-2 border rounded-lg hover:bg-orange-500 hover:text-white transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>`;
        }

        html += '</div>';
        container.innerHTML = html;
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('blogSearch');
        if (searchInput) {
            let timeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    if (e.target.value.length > 2) {
                        this.searchPosts(e.target.value);
                    } else if (e.target.value.length === 0) {
                        this.loadPosts();
                    }
                }, 300);
            });
        }
    }
}

// Initialize blog manager
let blogManager;
document.addEventListener('DOMContentLoaded', () => {
    blogManager = new BlogManager();
});
