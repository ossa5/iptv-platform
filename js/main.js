/**
 * منصة IPTV - السكريبت الرئيسي
 * IPTV Platform - Main JavaScript
 */

const API_URL = '/api.php';
const SITE_URL = location.origin + location.pathname.split('/').slice(0, -1).join('/');

// تحميل القنوات عند فتح الصفحة
document.addEventListener('DOMContentLoaded', function() {
    loadChannels();
    setupSearch();
    setupCategoryFilter();
    setupModal();
});

/**
 * تحميل القنوات من API
 */
function loadChannels(category = null) {
    const url = category ? `${API_URL}?action=channels&category=${category}` : `${API_URL}?action=channels`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.channels) {
                displayChannels(data.channels);
            } else {
                showNoChannels();
            }
        })
        .catch(error => {
            console.error('خطأ في تحميل القنوات:', error);
            showNoChannels();
        });
}

/**
 * عرض القنوات في الشاشة
 */
function displayChannels(channels) {
    const grid = document.getElementById('channelsGrid');
    const noData = document.getElementById('noChannels');
    
    if (!channels || channels.length === 0) {
        showNoChannels();
        return;
    }
    
    grid.innerHTML = channels.map(channel => `
        <div class="channel-card" onclick="openChannel(${channel.id})">
            <img src="${escapeHtml(channel.logo)}" alt="${escapeHtml(channel.name)}" class="channel-logo" onerror="this.src='https://via.placeholder.com/200x150?text=Logo'">
            <div class="channel-content">
                <div class="channel-name">${escapeHtml(channel.name)}</div>
                <span class="channel-category">${getCategoryLabel(channel.category)}</span>
            </div>
        </div>
    `).join('');
    
    noData.style.display = 'none';
}

/**
 * عرض رسالة عدم توفر القنوات
 */
function showNoChannels() {
    document.getElementById('channelsGrid').innerHTML = '';
    document.getElementById('noChannels').style.display = 'block';
}

/**
 * فتح صفحة القناة
 */
function openChannel(channelId) {
    window.location.href = `/watch.php?id=${channelId}`;
}

/**
 * إعداد البحث عن القنوات
 */
function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        
        if (this.value.trim() === '') {
            loadChannels();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchChannels(this.value);
        }, 300);
    });
}

/**
 * البحث عن القنوات
 */
function searchChannels(query) {
    fetch(`${API_URL}?action=search&q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayChannels(data.channels);
            }
        })
        .catch(error => console.error('خطأ البحث:', error));
}

/**
 * إعداد مرشحات الفئات
 */
function setupCategoryFilter() {
    const categoryBtns = document.querySelectorAll('.category-btn');
    
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.dataset.category;
            if (category === 'all') {
                loadChannels();
            } else {
                loadChannels(category);
            }
        });
    });
}

/**
 * إعداد المودال (النافذة المنبثقة)
 */
function setupModal() {
    const modal = document.getElementById('channelModal');
    if (!modal) return;
    
    const closeBtn = modal.querySelector('.close-modal');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            const video = modal.querySelector('#videoPlayer');
            if (video) video.pause();
        });
    }
    
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            const video = modal.querySelector('#videoPlayer');
            if (video) video.pause();
        }
    });
}

/**
 * تحويل نص HTML لتجنب الهجمات
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * الحصول على اسم الفئة
 */
function getCategoryLabel(category) {
    const labels = {
        'sports': '⚽ رياضة',
        'news': '📰 أخبار',
        'movies': '🎬 أفلام',
        'kids': '🎨 أطفال',
        'general': '📺 عام'
    };
    return labels[category] || category;
}
