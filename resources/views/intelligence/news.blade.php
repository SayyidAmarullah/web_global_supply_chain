@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-hidden pe-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center flex-shrink-0">
        <div>
            <h3 class="text-white fw-bold tracking-tight mb-1">
                Global News Intelligence
            </h3>
            <span class="text-muted fs-7">Real-time aggregated global supply chain and economic news</span>
        </div>
        <div class="d-flex gap-2">
            <x-button variant="outline" icon="tune">Sources</x-button>
            <x-button variant="primary" icon="sync" onclick="fetchNews()">Refresh Feed</x-button>
        </div>
    </div>

    <div class="d-flex flex-column flex-grow-1 overflow-hidden gap-4 mt-2">
        
        <!-- Filters -->
        <div class="d-flex justify-content-between align-items-center flex-shrink-0 pb-2 border-bottom border-secondary border-opacity-25">
            <!-- Category Filters -->
            <div class="d-flex gap-3 overflow-auto pe-2 pb-1" style="scrollbar-width: none;">
                <button class="btn news-filter-btn active transition-all hover-glow text-nowrap" onclick="setCategory('Logistics', this)">
                    <span class="material-symbols-outlined fs-5">local_shipping</span> Logistics
                </button>
                <button class="btn news-filter-btn transition-all hover-glow text-nowrap" onclick="setCategory('Trade', this)">
                    <span class="material-symbols-outlined fs-5">storefront</span> Trade
                </button>
                <button class="btn news-filter-btn transition-all hover-glow text-nowrap" onclick="setCategory('Shipping', this)">
                    <span class="material-symbols-outlined fs-5">directions_boat</span> Shipping
                </button>
                <button class="btn news-filter-btn transition-all hover-glow text-nowrap" onclick="setCategory('Economy', this)">
                    <span class="material-symbols-outlined fs-5">show_chart</span> Economy
                </button>
            </div>
            
            <!-- Country Filter -->
            <div class="d-flex align-items-center gap-2 ms-3 bg-dark px-3 py-1 rounded-pill border border-secondary border-opacity-25">
                <span class="material-symbols-outlined text-muted fs-5">public</span>
                <select id="countryFilter" class="form-select form-select-sm bg-transparent border-0 text-white fw-bold shadow-none cursor-pointer" onchange="setCountry(this.value)" style="min-width: 140px; cursor: pointer;">
                    <option value="any" style="color: #fff; background: #212529;">Global (All)</option>
                    @foreach($countries as $country)
                        <option value="{{ strtolower($country['code']) }}" style="color: #fff; background: #212529;">{{ $country['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- News Grid Container -->
        <div class="row g-4 overflow-auto flex-grow-1 pb-4 pe-2" id="newsGrid">
            <!-- Loading State -->
            <div class="col-12" id="loadingState">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="skeleton-card" style="height: 380px; border-radius: 24px;"></div>
                    </div>
                    <div class="col-md-4"><div class="skeleton-card" style="height: 340px; border-radius: 16px;"></div></div>
                    <div class="col-md-4"><div class="skeleton-card" style="height: 340px; border-radius: 16px;"></div></div>
                    <div class="col-md-4"><div class="skeleton-card" style="height: 340px; border-radius: 16px;"></div></div>
                </div>
            </div>
        </div>

    </div>

</main>

<style>
/* Adaptive Variables */
:root {
    --news-card-bg: rgba(255, 255, 255, 0.03);
    --news-card-border: rgba(255, 255, 255, 0.08);
    --news-card-hover-bg: rgba(255, 255, 255, 0.06);
    --news-text-main: #ffffff;
    --news-text-muted: #a0aec0;
    --news-skeleton: linear-gradient(90deg, rgba(255,255,255,0.05) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.05) 75%);
    --news-filter-active-bg: rgba(255, 255, 255, 0.05);
    --news-filter-active-border: rgba(255, 255, 255, 0.1);
    --news-filter-hover: rgba(255, 255, 255, 0.02);
}

[data-theme="light"] {
    --news-card-bg: rgba(255, 255, 255, 0.7);
    --news-card-border: rgba(0, 0, 0, 0.1);
    --news-card-hover-bg: rgba(255, 255, 255, 0.9);
    --news-text-main: #1e293b;
    --news-text-muted: #64748b;
    --news-skeleton: linear-gradient(90deg, rgba(0,0,0,0.05) 25%, rgba(0,0,0,0.08) 50%, rgba(0,0,0,0.05) 75%);
    --news-filter-active-bg: rgba(0, 0, 0, 0.05);
    --news-filter-active-border: rgba(0, 0, 0, 0.1);
    --news-filter-hover: rgba(0, 0, 0, 0.03);
}

.text-adaptive {
    color: var(--news-text-main) !important;
}
.text-adaptive-muted {
    color: var(--news-text-muted) !important;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-filter-btn {
    background: transparent;
    color: var(--news-text-muted);
    border: 1px solid transparent;
    border-radius: 10px;
    padding: 0.5rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}
.news-filter-btn.active {
    background: var(--news-filter-active-bg);
    color: var(--news-text-main);
    border-color: var(--news-filter-active-border);
}
.news-filter-btn.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 15%;
    width: 70%;
    height: 3px;
    background: var(--cyan-glow);
    border-radius: 5px 5px 0 0;
    box-shadow: 0 -2px 10px var(--cyan-glow);
}
.news-filter-btn:hover:not(.active) {
    background: var(--news-filter-hover);
    color: var(--news-text-main);
}

#countryFilter:focus {
    box-shadow: none;
    border-color: transparent;
}

/* Featured Hero Card */
.featured-news-card {
    position: relative;
    height: 380px;
    border-radius: 24px;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
}
.featured-news-card .bg-img {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    object-fit: cover;
    z-index: 1;
    transition: transform 0.7s ease;
}
.featured-news-card:hover .bg-img {
    transform: scale(1.05);
}
.featured-news-card .overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    /* Overlay uses hardcoded dark gradient so white text always shows properly */
    background: linear-gradient(to top, rgba(10, 17, 40, 0.95) 10%, rgba(10, 17, 40, 0.4) 60%, transparent 100%);
    z-index: 2;
}
.featured-news-card .content {
    position: relative;
    z-index: 3;
    padding: 2.5rem;
    width: 100%;
}

/* Regular Grid Card */
.news-grid-card {
    background: var(--news-card-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--news-card-border);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.news-grid-card:hover {
    transform: translateY(-5px);
    background: var(--news-card-hover-bg);
    border-color: rgba(13, 202, 240, 0.5);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15), 0 0 20px rgba(13, 202, 240, 0.1);
}
.news-grid-card .img-wrapper {
    height: 180px;
    overflow: hidden;
    position: relative;
}
.news-grid-card .img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.news-grid-card:hover .img-wrapper img {
    transform: scale(1.1);
}
.news-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    backdrop-filter: blur(8px);
    background: rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 2;
}

/* Skeleton Loading */
.skeleton-card {
    background: var(--news-skeleton);
    background-size: 200% 100%;
    animation: skeletonLoading 1.5s infinite;
}
@keyframes skeletonLoading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

<script>
let currentCategory = 'Logistics';
let currentCountry = 'any';

// GNews API Key provided by User
const GNEWS_API_KEY = '9a252cfad09875a5b6d58650366cd827';

document.addEventListener('DOMContentLoaded', () => {
    fetchNews();
});

function setCategory(category, btnElement) {
    currentCategory = category;
    document.querySelectorAll('.news-filter-btn').forEach(btn => btn.classList.remove('active'));
    btnElement.classList.add('active');
    fetchNews();
}

function setCountry(countryCode) {
    currentCountry = countryCode;
    fetchNews();
}

function getCountryName() {
    if (currentCountry === 'any') return '';
    const select = document.getElementById('countryFilter');
    return select.options[select.selectedIndex].text;
}

async function fetchNews() {
    const grid = document.getElementById('newsGrid');
    
    // Show Loading Skeleton
    grid.innerHTML = `
        <div class="col-12">
            <div class="skeleton-card" style="height: 380px; border-radius: 24px;"></div>
        </div>
        <div class="col-md-4"><div class="skeleton-card" style="height: 340px; border-radius: 16px;"></div></div>
        <div class="col-md-4"><div class="skeleton-card" style="height: 340px; border-radius: 16px;"></div></div>
        <div class="col-md-4"><div class="skeleton-card" style="height: 340px; border-radius: 16px;"></div></div>
    `;

    try {
        // Use a simple, highly compatible query string
        let query = currentCategory.toLowerCase();
        
        // Smart Context Search: Append country name simply with a space (implicit AND)
        if (currentCountry !== 'any') {
            query += ` ${getCountryName()}`; 
        }
        
        // Fetch from our internal Laravel Google News proxy route
        const countryParam = currentCountry !== 'any' ? getCountryName() : '';
        const url = `/intelligence/google-news?q=${encodeURIComponent(query)}&country=${encodeURIComponent(countryParam)}&category=${encodeURIComponent(currentCategory)}`;
        const response = await fetch(url);
        const data = await response.json();
        
        // Handle errors
        if (!data.success || data.errors) {
            throw new Error(data.errors ? data.errors[0] : 'Unknown News API Error');
        }
        
        if (data.articles && data.articles.length > 0) {
            renderNews(data.articles);
        } else {
            // Empty State (Real API response with 0 articles)
            grid.innerHTML = `
                <div class="col-12 d-flex flex-column align-items-center justify-content-center h-100" style="min-height: 400px;">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 4rem;">news_off</span>
                    <h5 class="text-adaptive fw-bold">No Recent News Found</h5>
                    <p class="text-adaptive-muted text-center" style="max-width: 400px;">
                        We couldn't find any recent real news from Google News regarding <b>${currentCategory}</b> in <b>${getCountryName() || 'the world'}</b>. Try selecting a different category or country.
                    </p>
                </div>
            `;
        }
        
    } catch (error) {
        console.error("News API fetch failed:", error);
        
        // Explicitly show the API Error to the user instead of secretly falling back to mock data
        grid.innerHTML = `
            <div class="col-12 d-flex flex-column align-items-center justify-content-center h-100" style="min-height: 400px;">
                <span class="material-symbols-outlined text-danger mb-3" style="font-size: 4rem;">warning</span>
                <h5 class="text-adaptive fw-bold text-danger">Google News API Connection Error</h5>
                <div class="bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 p-3 my-3 text-center" style="max-width: 600px;">
                    <code class="text-danger fw-bold fs-6">${error.message}</code>
                </div>
                <p class="text-adaptive-muted text-center mb-4" style="max-width: 500px;">
                    Failed to fetch data from the server.
                </p>
                <button class="btn btn-outline-info rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 hover-glow" onclick="renderNews(getMockNews('${currentCategory}', '${currentCountry}'))">
                    <span class="material-symbols-outlined fs-5">science</span> Load Simulation Data
                </button>
            </div>
        `;
    }
}

function renderNews(articles) {
    const grid = document.getElementById('newsGrid');
    grid.innerHTML = '';
    
    const fallbackImages = {
        'Logistics': [
            'https://images.unsplash.com/photo-1580674285054-bed31e145f59?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1501523460185-2aa5d2a0f981?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519003722824-194d4455a60c?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1553413077-190dd305871c?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1577705998148-6da4f3963bc8?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?q=80&w=1200&auto=format&fit=crop'
        ],
        'Trade': [
            'https://images.unsplash.com/photo-1578575437130-527eed3abbec?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1444653614773-995cb1ef9efa?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1616401784845-180882ba9ba8?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1605810230434-7631ac76ec81?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=1200&auto=format&fit=crop'
        ],
        'Shipping': [
            'https://images.unsplash.com/photo-1494412519320-aa613dfb7738?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1551281473-cbcf138e4a9e?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1534008897995-27a23e859048?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1586528116311-ad8ed7c83a7f?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1605647540924-852290f6b0d5?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1503431128871-16fd15a4e4d5?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1585038332194-469a47313a1a?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1614030126544-b2580b016335?q=80&w=1200&auto=format&fit=crop'
        ],
        'Economy': [
            'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1535320903710-d993d3d77d29?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1553729459-efe14ef6055d?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1604594849809-dfedbc827105?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1579532537598-459ecdaf39cc?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1200&auto=format&fit=crop'
        ]
    };
    
    articles.forEach((article, index) => {
        const dateStr = new Date(article.publishedAt).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        // Use a categorized, index-based fallback image if the original API image fails or is missing
        const categoryImgArray = fallbackImages[currentCategory] || fallbackImages['Logistics'];
        const fallbackImg = categoryImgArray[index % categoryImgArray.length];
        const imgUrl = article.image && article.image.trim() !== '' ? article.image : fallbackImg;
        
        const sentiment = article.sentiment;
        let sentimentBadge = '';
        if (sentiment) {
            if (sentiment.label === 'Positive') sentimentBadge = `<span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50"><span class="material-symbols-outlined fs-8 align-middle">trending_up</span> Positive ${sentiment.positive_pct}%</span>`;
            else if (sentiment.label === 'Negative') sentimentBadge = `<span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50"><span class="material-symbols-outlined fs-8 align-middle">trending_down</span> Negative ${sentiment.negative_pct}%</span>`;
            else sentimentBadge = `<span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50"><span class="material-symbols-outlined fs-8 align-middle">horizontal_rule</span> Neutral ${sentiment.neutral_pct}%</span>`;
        }

        if (index === 0) {
            // Featured Article (Full Width Hero) - Text stays white here due to dark overlay
            const html = `
                <div class="col-12 mb-2">
                    <a href="${article.url}" target="_blank" class="featured-news-card">
                        <img src="${imgUrl}" class="bg-img" onload="if((this.naturalWidth == 1 || this.naturalWidth == 0) && this.src !== '${fallbackImg}') { this.src='${fallbackImg}'; }" onerror="this.onerror=null; this.src='${fallbackImg}'">
                        <div class="overlay"></div>
                        <div class="content">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-bold" style="box-shadow: 0 0 10px rgba(13,202,240,0.5);">FEATURED</span>
                                <span class="badge bg-dark bg-opacity-75 border border-secondary px-3 py-2 rounded-pill text-white">${article.source.name}</span>
                                <span class="text-white text-opacity-75 fs-7 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-6">schedule</span> ${dateStr}</span>
                                ${sentimentBadge}
                            </div>
                            <h2 class="text-white fw-bold display-6 mb-3 text-glow" style="max-width: 800px; line-height: 1.2;">${article.title}</h2>
                            <p class="text-white text-opacity-75 fs-5 mb-0 line-clamp-2" style="max-width: 700px;">${article.description}</p>
                            
                            <!-- Sentiment Bar -->
                            ${sentiment ? `
                            <div class="mt-4" style="max-width: 400px;">
                                <div class="d-flex justify-content-between fs-8 text-white text-opacity-75 mb-1">
                                    <span>AI Sentiment Analysis (Lexicon Based)</span>
                                </div>
                                <div class="progress" style="height: 6px; background-color: rgba(255,255,255,0.1);">
                                    <div class="progress-bar bg-success" style="width: ${sentiment.positive_pct}%" title="Positive: ${sentiment.positive_score} words"></div>
                                    <div class="progress-bar bg-secondary" style="width: ${sentiment.neutral_pct}%"></div>
                                    <div class="progress-bar bg-danger" style="width: ${sentiment.negative_pct}%" title="Negative: ${sentiment.negative_score} words"></div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </a>
                </div>
            `;
            grid.insertAdjacentHTML('beforeend', html);
        } else {
            // Regular Grid Card - Uses text-adaptive
            const html = `
                <div class="col-md-6 col-lg-4">
                    <div class="news-grid-card">
                        <div class="img-wrapper">
                            <span class="news-badge">${article.source.name}</span>
                            <img src="${imgUrl}" onload="if((this.naturalWidth == 1 || this.naturalWidth == 0) && this.src !== '${fallbackImg}') { this.src='${fallbackImg}'; }" onerror="this.onerror=null; this.src='${fallbackImg}'">
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="text-info fs-8 fw-bold text-uppercase tracking-wider">${currentCategory}</span>
                                ${sentimentBadge}
                            </div>
                            <h5 class="text-adaptive fw-bold mb-3 lh-base line-clamp-2" style="min-height: 48px;">${article.title}</h5>
                            <p class="text-adaptive-muted fs-7 mb-4 flex-grow-1 line-clamp-3">${article.description}</p>
                            
                            ${sentiment ? `
                            <div class="mb-3">
                                <div class="progress" style="height: 4px; background-color: rgba(255,255,255,0.05);">
                                    <div class="progress-bar bg-success" style="width: ${sentiment.positive_pct}%"></div>
                                    <div class="progress-bar bg-secondary" style="width: ${sentiment.neutral_pct}%"></div>
                                    <div class="progress-bar bg-danger" style="width: ${sentiment.negative_pct}%"></div>
                                </div>
                            </div>
                            ` : ''}
                            
                            <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                <span class="text-adaptive-muted fs-8">${dateStr}</span>
                                <a href="${article.url}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 fw-bold transition-all d-flex align-items-center gap-1">
                                    Read Article <span class="material-symbols-outlined fs-6">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            grid.insertAdjacentHTML('beforeend', html);
        }
    });
}

function getMockNews(category, countryCode) {
    let countryName = 'Global';
    if (countryCode !== 'any') {
        const select = document.getElementById('countryFilter');
        countryName = select.options[select.selectedIndex].text;
    }

    const images = {
        'Logistics': [
            'https://images.unsplash.com/photo-1580674285054-bed31e145f59?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1501523460185-2aa5d2a0f981?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519003722824-194d4455a60c?q=80&w=1200&auto=format&fit=crop'
        ],
        'Trade': [
            'https://images.unsplash.com/photo-1578575437130-527eed3abbec?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1444653614773-995cb1ef9efa?q=80&w=1200&auto=format&fit=crop'
        ],
        'Shipping': [
            'https://images.unsplash.com/photo-1494412519320-aa613dfb7738?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1551281473-cbcf138e4a9e?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1534008897995-27a23e859048?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1586528116311-ad8ed7c83a7f?q=80&w=1200&auto=format&fit=crop'
        ],
        'Economy': [
            'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1535320903710-d993d3d77d29?q=80&w=1200&auto=format&fit=crop'
        ]
    };
    
    const titles = {
        'Logistics': [
            `Supply Chains in ${countryName} Brace for New Disruptions`,
            `How Tech is Slashing Delivery Times Across ${countryName} by 30%`,
            `Major Hubs in ${countryName} Face Unprecedented Container Backlogs`,
            `Automated Warehouses in ${countryName} Cut Fulfillment Errors by 95%`,
            `${countryName} Passes Drone Delivery Safety Regulatory Hurdles`,
            `Port Automation: The Future of Freight in ${countryName}`,
            `${countryName}'s Real-time Tracking Revolutionizes Freight Management`
        ],
        'Trade': [
            `New Trade Tariffs Impact Cross-Border Tech Shipments in ${countryName}`,
            `Markets in ${countryName} Surge as Export Volumes Hit Record Highs`,
            `Trade Tensions: What the Latest Sanctions Mean for ${countryName}`,
            `E-Commerce Boom Fuels Cross-Border Trading Records for ${countryName}`,
            `${countryName} Automotive Export Tariffs Slashed by 15%`,
            `Tech Hardware Import Ban Lifted in ${countryName}`,
            `${countryName}'s Agricultural Exports See Double-Digit Growth`
        ],
        'Shipping': [
            `${countryName} Ocean Freights See 12% Drop in Carbon Emissions`,
            `Canal Alternatives: Why ${countryName} Ships are Taking the Longer Route`,
            `New Mega-Vessel Launches in ${countryName}, Breaking Capacity Records`,
            `Maritime Fuel Costs Plunge in ${countryName} After Production Increase`,
            `Autonomous Cargo Ships to Enter Commercial Use by 2027 in ${countryName}`,
            `${countryName} Piracy Incidents Drop to Lowest Levels in a Decade`,
            `Shipping Ports in ${countryName} Invest Heavily in Green Energy`
        ],
        'Economy': [
            `Inflation Rates Cool Down in ${countryName}, Spurring Growth`,
            `${countryName} Central Bank Holds Interest Rates Amid Cautious Outlook`,
            `Commodity Prices Stabilize in ${countryName} After Weeks of Volatility`,
            `Tech Stocks Rebound in ${countryName} After Q2 Earnings Reports`,
            `Unemployment Drops in Key Industrial Zones Across ${countryName}`,
            `${countryName} Housing Market Sees Unprecedented Shift Towards Rentals`,
            `${countryName}'s Renewable Energy Sector Attracts Record Investments`
        ]
    };
    
    let mock = [];
    for(let i=0; i<7; i++) {
        const categoryImages = images[category];
        const categoryTitles = titles[category];
        
        mock.push({
            title: categoryTitles[i % categoryTitles.length],
            description: `This is an exclusive AI-generated mock description for ${category} originating from ${countryName}. Real Google News RSS results will seamlessly appear here once the connection is successful.`,
            image: categoryImages[i % categoryImages.length],
            publishedAt: new Date(Date.now() - i * 3600000 * 5).toISOString(),
            source: { name: `${countryName} Intel` },
            url: '#'
        });
    }
    return mock;
}
</script>
@endsection
