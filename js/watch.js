/**
 * صفحة مشاهدة القناة
 * Watch Channel Page
 */

const API_URL = '/api.php';
let player;
let hls;

document.addEventListener('DOMContentLoaded', function() {
    initializePlayer();
    loadMatches();
});

/**
 * تهيئة مشغل الفيديو
 */
function initializePlayer() {
    const video = document.getElementById('videoPlayer');
    if (!video) return;
    
    const channelUrl = video.querySelector('source')?.src;
    if (!channelUrl) return;
    
    // استخدام hls.js لتشغيل HLS
    if (Hls.isSupported()) {
        hls = new Hls();
        hls.loadSource(channelUrl);
        hls.attachMedia(video);
        
        hls.on(Hls.Events.MANIFEST_PARSED, () => {
            console.log('تم تحميل البث');
            video.play().catch(e => console.error('خطأ في التشغيل:', e));
        });
        
        hls.on(Hls.Events.ERROR, (event, data) => {
            console.error('خطأ في البث:', data);
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        // Safari يدعم HLS بشكل أصلي
        video.src = channelUrl;
        video.play().catch(e => console.error('خطأ في التشغيل:', e));
    }
}

/**
 * تحميل المباريات المتعلقة بهذه القناة
 */
function loadMatches() {
    const channelName = channelData?.name || '';
    
    fetch(`${API_URL}?action=matches`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.matches) {
                const relatedMatches = data.matches.filter(m => 
                    m.channel === channelName
                );
                displayMatches(relatedMatches);
            }
        })
        .catch(error => console.error('خطأ في تحميل المباريات:', error));
}

/**
 * عرض المباريات
 */
function displayMatches(matches) {
    const container = document.getElementById('relatedMatches');
    const noMatches = document.getElementById('noMatches');
    
    if (!matches || matches.length === 0) {
        container.innerHTML = '';
        noMatches.style.display = 'block';
        return;
    }
    
    container.innerHTML = matches.map(match => `
        <div class="match-item">
            <div class="match-teams">${escapeHtml(match.team1)} vs ${escapeHtml(match.team2)}</div>
            <div class="match-time">📅 ${formatDate(match.date)} ⏰ ${match.time || 'TBA'}</div>
            <div class="match-time">${escapeHtml(match.league)}</div>
        </div>
    `).join('');
    
    noMatches.style.display = 'none';
}

/**
 * تنسيق التاريخ
 */
function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('ar-SA', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

/**
 * مشاركة القناة
 */
function shareChannel() {
    const title = channelData?.name || 'القناة';
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: `شاهد ${title} على منصة IPTV`,
            url: url
        });
    } else {
        alert('مشاركة: ' + url);
    }
}

/**
 * نسخ الرابط
 */
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('تم نسخ الرابط بنجاح');
    }).catch(err => {
        console.error('خطأ في النسخ:', err);
    });
}

/**
 * تحويل نص HTML
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
