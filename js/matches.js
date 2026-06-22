/**
 * صفحة المباريات
 * Matches Page
 */

const API_URL = '/api.php';

document.addEventListener('DOMContentLoaded', function() {
    loadAllMatches();
    setupFilters();
    setupModal();
});

/**
 * تحميل جميع المباريات
 */
function loadAllMatches() {
    fetch(`${API_URL}?action=matches`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.matches) {
                displayMatches(data.matches);
            }
        })
        .catch(error => console.error('خطأ في تحميل المباريات:', error));
}

/**
 * عرض المباريات
 */
function displayMatches(matches) {
    const container = document.getElementById('matchesContainer');
    const noMatches = document.getElementById('noMatches');
    
    if (!matches || matches.length === 0) {
        container.innerHTML = '';
        noMatches.style.display = 'block';
        return;
    }
    
    container.innerHTML = matches.map(match => `
        <div class="match-card" onclick="openMatchDetails(${match.id})">
            <div class="match-league">${escapeHtml(match.league)}</div>
            <div class="match-teams-large">
                <div class="team-name">${escapeHtml(match.team1)}</div>
                <div class="vs">VS</div>
                <div class="team-name">${escapeHtml(match.team2)}</div>
            </div>
            <div class="match-time-large">
                📅 ${formatDate(match.date)}<br>
                ⏰ ${match.time || 'TBA'}
            </div>
            <div class="match-channel">على: ${escapeHtml(match.channel)}</div>
        </div>
    `).join('');
    
    noMatches.style.display = 'none';
}

/**
 * إعداد المرشحات
 */
function setupFilters() {
    const filterSelect = document.getElementById('dateFilter');
    if (!filterSelect) return;
    
    filterSelect.addEventListener('change', function() {
        const value = this.value;
        let filteredMatches = [];
        const today = new Date();
        
        fetch(`${API_URL}?action=matches`)
            .then(response => response.json())
            .then(data => {
                if (!data.success || !data.matches) return;
                
                const matches = data.matches;
                
                if (value === '') {
                    filteredMatches = matches;
                } else if (value === 'today') {
                    filteredMatches = matches.filter(m => m.date === today.toISOString().split('T')[0]);
                } else if (value === 'tomorrow') {
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    filteredMatches = matches.filter(m => m.date === tomorrow.toISOString().split('T')[0]);
                } else if (value === 'week') {
                    const nextWeek = new Date(today);
                    nextWeek.setDate(nextWeek.getDate() + 7);
                    filteredMatches = matches.filter(m => {
                        const matchDate = new Date(m.date);
                        return matchDate >= today && matchDate <= nextWeek;
                    });
                }
                
                displayMatches(filteredMatches);
            })
            .catch(error => console.error('خطأ في التصفية:', error));
    });
}

/**
 * فتح تفاصيل المباراة
 */
function openMatchDetails(matchId) {
    fetch(`${API_URL}?action=matches`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.matches) {
                const match = data.matches.find(m => m.id == matchId);
                if (match) {
                    displayMatchDetails(match);
                }
            }
        })
        .catch(error => console.error('خطأ:', error));
}

/**
 * عرض تفاصيل المباراة
 */
function displayMatchDetails(match) {
    document.getElementById('matchTitle').textContent = `${escapeHtml(match.team1)} vs ${escapeHtml(match.team2)}`;
    document.getElementById('matchLeague').textContent = escapeHtml(match.league);
    document.getElementById('team1').textContent = escapeHtml(match.team1);
    document.getElementById('team2').textContent = escapeHtml(match.team2);
    document.getElementById('matchDate').textContent = '📅 ' + formatDate(match.date);
    document.getElementById('matchTime').textContent = '⏰ ' + (match.time || 'TBA');
    document.getElementById('broadcastChannel').textContent = escapeHtml(match.channel);
    
    const watchBtn = document.getElementById('watchButton');
    watchBtn.href = '#';
    watchBtn.onclick = () => {
        alert('سيتم فتح القناة: ' + match.channel);
    };
    
    document.getElementById('matchModal').style.display = 'block';
}

/**
 * إعداد المودال
 */
function setupModal() {
    const modal = document.getElementById('matchModal');
    const closeBtn = modal?.querySelector('.close-modal');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }
    
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
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
 * تحويل نص HTML
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
