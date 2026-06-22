/**
 * لوحة الإدارة
 * Admin Panel
 */

const API_URL = '/api.php';

document.addEventListener('DOMContentLoaded', function() {
    setupLoginForm();
    setupNavigation();
    setupModals();
});

/**
 * إعداد نموذج تسجيل الدخول
 */
function setupLoginForm() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const password = document.getElementById('password').value;
        
        fetch(`${API_URL}?action=login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showError(document.getElementById('loginError'), data.error);
            }
        })
        .catch(error => {
            showError(document.getElementById('loginError'), 'خطأ في الاتصال');
        });
    });
}

/**
 * إعداد التنقل
 */
function setupNavigation() {
    const navLinks = document.querySelectorAll('.nav-link[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.dataset.tab;
            
            // إزالة الحالة النشطة من جميع الروابط والعلامات
            navLinks.forEach(l => l.classList.remove('active'));
            tabContents.forEach(t => t.classList.remove('active'));
            
            // إضافة الحالة النشطة للرابط والعلامة المختارة
            this.classList.add('active');
            document.getElementById(tabName).classList.add('active');
            
            // تحميل البيانات المناسبة
            if (tabName === 'dashboard') {
                loadStatistics();
            } else if (tabName === 'channels') {
                loadChannels();
            } else if (tabName === 'matches') {
                loadMatches();
            }
        });
    });
    
    // تحميل لوحة التحكم افتراضياً
    loadStatistics();
}

/**
 * تحميل الإحصائيات
 */
function loadStatistics() {
    fetch(`${API_URL}?action=statistics`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.statistics) {
                const stats = data.statistics;
                document.getElementById('totalChannels').textContent = stats.total_channels;
                document.getElementById('activeChannels').textContent = stats.active_channels;
                document.getElementById('totalMatches').textContent = stats.total_matches;
            }
        })
        .catch(error => console.error('خطأ في تحميل الإحصائيات:', error));
}

/**
 * تحميل القنوات
 */
function loadChannels() {
    fetch(`${API_URL}?action=channels`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.channels) {
                displayChannelsTable(data.channels);
            }
        })
        .catch(error => console.error('خطأ:', error));
}

/**
 * عرض جدول القنوات
 */
function displayChannelsTable(channels) {
    const container = document.getElementById('channelsList');
    
    if (!channels || channels.length === 0) {
        container.innerHTML = '<p style="text-align: center; padding: 20px;">لا توجد قنوات</p>';
        return;
    }
    
    container.innerHTML = `
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>الفئة</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                ${channels.map(ch => `
                    <tr>
                        <td>${escapeHtml(ch.name)}</td>
                        <td>${ch.category}</td>
                        <td>${ch.active ? '✅ نشط' : '❌ معطل'}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-edit" onclick="editChannel(${ch.id})">تعديل</button>
                                <button class="action-btn btn-delete" onclick="deleteChannel(${ch.id})">حذف</button>
                            </div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

/**
 * تحميل المباريات
 */
function loadMatches() {
    fetch(`${API_URL}?action=matches`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.matches) {
                displayMatchesTable(data.matches);
            }
        })
        .catch(error => console.error('خطأ:', error));
}

/**
 * عرض جدول المباريات
 */
function displayMatchesTable(matches) {
    const container = document.getElementById('matchesList');
    
    if (!matches || matches.length === 0) {
        container.innerHTML = '<p style="text-align: center; padding: 20px;">لا توجد مباريات</p>';
        return;
    }
    
    container.innerHTML = `
        <table>
            <thead>
                <tr>
                    <th>الفريقان</th>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>البطولة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                ${matches.map(m => `
                    <tr>
                        <td>${escapeHtml(m.team1)} vs ${escapeHtml(m.team2)}</td>
                        <td>${m.date}</td>
                        <td>${m.time || 'TBA'}</td>
                        <td>${escapeHtml(m.league)}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-delete" onclick="deleteMatch(${m.id})">حذف</button>
                            </div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

/**
 * إعداد المودالات
 */
function setupModals() {
    document.querySelectorAll('.close-modal').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
}

/**
 * فتح نموذج إضافة قناة
 */
function openChannelForm() {
    document.getElementById('modalTitle').textContent = 'إضافة قناة جديدة';
    document.getElementById('channelId').value = '';
    document.getElementById('channelForm').reset();
    document.getElementById('channelModal').style.display = 'block';
}

/**
 * تعديل قناة
 */
function editChannel(id) {
    fetch(`${API_URL}?action=channels`)
        .then(response => response.json())
        .then(data => {
            const channel = data.channels.find(c => c.id == id);
            if (channel) {
                document.getElementById('modalTitle').textContent = 'تعديل القناة';
                document.getElementById('channelId').value = channel.id;
                document.getElementById('channelName').value = channel.name;
                document.getElementById('channelUrl').value = channel.url;
                document.getElementById('channelLogo').value = channel.logo;
                document.getElementById('channelCategory').value = channel.category;
                document.getElementById('channelCountry').value = channel.country;
                document.getElementById('channelLanguage').value = channel.language;
                document.getElementById('channelActive').checked = channel.active;
                document.getElementById('channelModal').style.display = 'block';
            }
        });
}

/**
 * حذف قناة
 */
function deleteChannel(id) {
    if (confirm('هل تريد حذف هذه القناة؟')) {
        fetch(`${API_URL}?action=delete_channel`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadChannels();
            }
        });
    }
}

/**
 * حفظ القناة
 */
document.getElementById('channelForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const channelId = document.getElementById('channelId').value;
    const channelData = {
        name: document.getElementById('channelName').value,
        url: document.getElementById('channelUrl').value,
        logo: document.getElementById('channelLogo').value,
        category: document.getElementById('channelCategory').value,
        country: document.getElementById('channelCountry').value,
        language: document.getElementById('channelLanguage').value,
        active: document.getElementById('channelActive').checked
    };
    
    const action = channelId ? 'update_channel' : 'add_channel';
    if (channelId) channelData.id = channelId;
    
    fetch(`${API_URL}?action=${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(channelData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('channelModal').style.display = 'none';
            loadChannels();
        }
    });
});

/**
 * فتح نموذج إضافة مباراة
 */
function openMatchForm() {
    document.getElementById('matchModalTitle').textContent = 'إضافة مباراة جديدة';
    document.getElementById('matchId').value = '';
    document.getElementById('matchForm').reset();
    document.getElementById('matchModal').style.display = 'block';
}

/**
 * حذف مباراة
 */
function deleteMatch(id) {
    if (confirm('هل تريد حذف هذه المباراة؟')) {
        fetch(`${API_URL}?action=delete_match`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMatches();
            }
        });
    }
}

/**
 * حفظ المباراة
 */
document.getElementById('matchForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const matchData = {
        team1: document.getElementById('team1').value,
        team2: document.getElementById('team2').value,
        date: document.getElementById('matchDate').value,
        time: document.getElementById('matchTime').value,
        league: document.getElementById('league').value,
        channel: document.getElementById('channel').value
    };
    
    fetch(`${API_URL}?action=add_match`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(matchData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('matchModal').style.display = 'none';
            loadMatches();
        }
    });
});

/**
 * استيراد ملف M3U
 */
function importM3U() {
    const file = document.getElementById('m3uFile')?.files[0];
    if (!file) {
        alert('الرجاء اختيار ملف');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', file);
    
    fetch(`${API_URL}?action=import_m3u`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const statusDiv = document.getElementById('importStatus');
        if (data.success) {
            statusDiv.className = 'status-message success';
            statusDiv.textContent = data.message;
            loadChannels();
        } else {
            statusDiv.className = 'status-message error';
            statusDiv.textContent = data.error;
        }
        statusDiv.style.display = 'block';
    });
}

/**
 * تسجيل الخروج
 */
document.getElementById('logoutBtn')?.addEventListener('click', function(e) {
    e.preventDefault();
    fetch(`${API_URL}?action=logout`, { method: 'POST' })
        .then(() => location.reload());
});

/**
 * عرض الخطأ
 */
function showError(element, message) {
    element.textContent = message;
    element.style.display = 'block';
    setTimeout(() => {
        element.style.display = 'none';
    }, 5000);
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
