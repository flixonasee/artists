const state = {
    user: null,
    artists: [],
    selectedArtist: null,
    artworks: [],
    editingArtwork: null,
};

const levels = [
    { points: 0, label: 'Novice Archivist' },
    { points: 200, label: 'Researcher' },
    { points: 500, label: 'Connoisseur' },
    { points: 1000, label: 'Curator' },
    { points: 2000, label: 'Archivist-in-Chief' },
    { points: 5000, label: 'Collector Whisperer' },
];

const qs = (sel) => document.querySelector(sel);

async function api(action, data = null, method = 'POST') {
    const opts = { method };
    if (data instanceof FormData) {
        opts.body = data;
    } else if (method === 'GET') {
        // no body
    } else {
        const form = new FormData();
        if (data) Object.entries(data).forEach(([k, v]) => form.append(k, v));
        opts.body = form;
    }
    const url = method === 'GET' && data ? `api.php?action=${action}&${new URLSearchParams(data)}` : `api.php?action=${action}`;
    const res = await fetch(url, opts);
    const json = await res.json();
    if (!res.ok) throw json;
    return json;
}

function switchTab(tab) {
    document.querySelectorAll('.tab').forEach((btn) => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach((el) => el.classList.add('hidden'));
    qs(`[data-tab="${tab}"]`).classList.add('active');
    qs(`#${tab}-tab`).classList.remove('hidden');
}

function showApp() {
    qs('#auth-pane').classList.add('hidden');
    qs('#app').classList.remove('hidden');
}

function showAuth() {
    qs('#auth-pane').classList.remove('hidden');
    qs('#app').classList.add('hidden');
}

function renderBadges(badges) {
    const container = qs('#badges');
    container.innerHTML = '';
    badges.forEach((b) => {
        const span = document.createElement('span');
        span.className = 'badge';
        span.textContent = b;
        container.appendChild(span);
    });
}

function updateProgress(points, level) {
    const currentIndex = levels.findIndex((l, idx) => points >= l.points && (idx === levels.length - 1 || points < levels[idx + 1].points));
    const currentLevel = currentIndex >= 0 ? levels[currentIndex] : levels[0];
    const nextLevel = levels[Math.min(currentIndex + 1, levels.length - 1)];
    const spanPoints = qs('#user-points');
    const spanLevel = qs('#user-level');
    spanPoints.textContent = points;
    spanLevel.textContent = level;
    const range = nextLevel.points - currentLevel.points || 1;
    const progress = currentIndex === levels.length - 1 ? 100 : ((points - currentLevel.points) / range) * 100;
    qs('#progress-fill').style.width = `${Math.min(100, Math.max(0, progress))}%`;
}

async function loadStats() {
    try {
        const res = await api('get_stats', null, 'GET');
        updateProgress(res.points, res.level);
        renderBadges(res.badges || []);
    } catch (err) {
        console.error(err);
    }
}

function renderArtists() {
    const list = qs('#artist-list');
    list.innerHTML = '';
    if (!state.artists.length) {
        list.innerHTML = '<li>No artists yet.</li>';
        return;
    }
    state.artists.forEach((artist) => {
        const li = document.createElement('li');
        li.textContent = artist.name;
        li.dataset.id = artist.id;
        if (state.selectedArtist && state.selectedArtist.id === artist.id) li.classList.add('active');
        li.addEventListener('click', () => selectArtist(artist.id));
        list.appendChild(li);
    });
}

function clearArtistForm() {
    qs('#artist-name').value = '';
    qs('#artist-nationality').value = '';
    qs('#artist-birth').value = '';
    qs('#artist-death').value = '';
    qs('#artist-tag1').value = '';
    qs('#artist-tag2').value = '';
    qs('#artist-notes').value = '';
    qs('#artist-image').value = '';
    qs('#artist-image-preview').src = '';
}

function populateArtist(artist) {
    state.selectedArtist = artist;
    qs('#empty-state').classList.add('hidden');
    qs('#artist-detail').classList.remove('hidden');
    qs('#artist-name').value = artist.name || '';
    qs('#artist-nationality').value = artist.nationality || '';
    qs('#artist-birth').value = artist.birth_year || '';
    qs('#artist-death').value = artist.death_year || '';
    qs('#artist-tag1').value = artist.tag1 || '';
    qs('#artist-tag2').value = artist.tag2 || '';
    qs('#artist-notes').value = artist.notes || '';
    qs('#artist-image-preview').src = artist.image_path ? artist.image_path : '';
}

async function selectArtist(id) {
    try {
        const res = await api('get_artist', { id }, 'GET');
        populateArtist(res.artist);
        renderArtists();
        await loadArtworks(id);
    } catch (err) {
        console.error(err);
    }
}

async function loadArtists(q = '') {
    try {
        const res = await api('list_artists', { q }, 'GET');
        state.artists = res.artists || [];
        renderArtists();
    } catch (err) {
        console.error(err);
    }
}

async function uploadImage(file, target) {
    const fd = new FormData();
    fd.append('file', file);
    fd.append('target', target);
    const res = await api('upload_image', fd, 'POST');
    return res.path;
}

async function saveArtist() {
    const artist = state.selectedArtist;
    const fd = new FormData();
    if (artist && artist.id) fd.append('id', artist.id);
    fd.append('name', qs('#artist-name').value.trim());
    fd.append('nationality', qs('#artist-nationality').value.trim());
    fd.append('birth_year', qs('#artist-birth').value.trim());
    fd.append('death_year', qs('#artist-death').value.trim());
    fd.append('tag1', qs('#artist-tag1').value.trim());
    fd.append('tag2', qs('#artist-tag2').value.trim());
    fd.append('notes', qs('#artist-notes').value.trim());
    if (qs('#artist-image').files[0]) {
        const path = await uploadImage(qs('#artist-image').files[0], 'artists');
        fd.append('image_path', path);
    } else if (artist && artist.image_path) {
        fd.append('image_path', artist.image_path);
    }
    try {
        const res = await api('save_artist', fd, 'POST');
        state.selectedArtist = res.artist;
        await loadArtists(qs('#search').value);
        await loadStats();
        populateArtist(res.artist);
        qs('#artist-error').textContent = '';
    } catch (err) {
        qs('#artist-error').textContent = err.error || 'Save failed';
    }
}

async function deleteArtist() {
    if (!state.selectedArtist) return;
    if (!confirm('Delete this artist and all artworks?')) return;
    try {
        await api('delete_artist', { id: state.selectedArtist.id });
        state.selectedArtist = null;
        state.artworks = [];
        qs('#artist-detail').classList.add('hidden');
        qs('#empty-state').classList.remove('hidden');
        await loadArtists();
    } catch (err) {
        qs('#artist-error').textContent = err.error || 'Delete failed';
    }
}

function renderArtworks() {
    const container = qs('#artwork-list');
    container.innerHTML = '';
    if (!state.artworks.length) {
        container.innerHTML = '<div class="empty">No artworks yet.</div>';
        return;
    }
    state.artworks.forEach((art) => {
        const card = document.createElement('div');
        card.className = 'artwork-card';
        const img = document.createElement('img');
        img.src = art.image_path || '';
        const info = document.createElement('div');
        info.innerHTML = `<strong>${art.title}</strong><div class="meta">${art.year_text || ''} · ${art.medium || ''}</div><div>${art.notes || ''}</div>`;
        const actions = document.createElement('div');
        actions.className = 'actions';
        const editBtn = document.createElement('button');
        editBtn.className = 'pill secondary';
        editBtn.textContent = 'Edit';
        editBtn.onclick = () => startEditArtwork(art);
        const delBtn = document.createElement('button');
        delBtn.className = 'pill danger';
        delBtn.textContent = 'Delete';
        delBtn.onclick = () => deleteArtwork(art.id);
        actions.append(editBtn, delBtn);
        card.append(img, info, actions);
        container.appendChild(card);
    });
}

async function loadArtworks(artist_id) {
    try {
        const res = await api('list_artworks', { artist_id }, 'GET');
        state.artworks = res.artworks || [];
        renderArtworks();
    } catch (err) {
        console.error(err);
    }
}

function resetArtworkForm() {
    state.editingArtwork = null;
    qs('#artwork-title').value = '';
    qs('#artwork-year').value = '';
    qs('#artwork-medium').value = '';
    qs('#artwork-notes').value = '';
    qs('#artwork-image').value = '';
    qs('#artwork-image-preview').src = '';
    qs('#artwork-form-title').textContent = 'New Artwork';
}

function startEditArtwork(art) {
    state.editingArtwork = art;
    qs('#artwork-form').classList.remove('hidden');
    qs('#artwork-form-title').textContent = 'Edit Artwork';
    qs('#artwork-title').value = art.title || '';
    qs('#artwork-year').value = art.year_text || '';
    qs('#artwork-medium').value = art.medium || '';
    qs('#artwork-notes').value = art.notes || '';
    qs('#artwork-image-preview').src = art.image_path || '';
}

async function saveArtwork() {
    if (!state.selectedArtist) return;
    const fd = new FormData();
    if (state.editingArtwork) fd.append('id', state.editingArtwork.id);
    fd.append('artist_id', state.selectedArtist.id);
    fd.append('title', qs('#artwork-title').value.trim());
    fd.append('year_text', qs('#artwork-year').value.trim());
    fd.append('medium', qs('#artwork-medium').value.trim());
    fd.append('notes', qs('#artwork-notes').value.trim());
    if (qs('#artwork-image').files[0]) {
        const path = await uploadImage(qs('#artwork-image').files[0], 'artworks');
        fd.append('image_path', path);
    } else if (state.editingArtwork && state.editingArtwork.image_path) {
        fd.append('image_path', state.editingArtwork.image_path);
    }
    try {
        const res = await api('save_artwork', fd, 'POST');
        qs('#artwork-error').textContent = '';
        qs('#artwork-form').classList.add('hidden');
        resetArtworkForm();
        await loadArtworks(state.selectedArtist.id);
        await loadStats();
    } catch (err) {
        qs('#artwork-error').textContent = err.error || 'Save failed';
    }
}

async function deleteArtwork(id) {
    if (!confirm('Delete this artwork?')) return;
    try {
        await api('delete_artwork', { id });
        await loadArtworks(state.selectedArtist.id);
    } catch (err) {
        qs('#artwork-error').textContent = err.error || 'Delete failed';
    }
}

function setupAuth() {
    document.querySelectorAll('.tab').forEach((btn) => {
        btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });
    qs('#login-btn').onclick = async () => {
        try {
            const res = await api('login', {
                email: qs('#login-email').value,
                password: qs('#login-password').value,
            });
            state.user = res.user;
            afterLogin();
        } catch (err) {
            qs('#login-error').textContent = err.error || 'Login failed';
        }
    };
    qs('#register-btn').onclick = async () => {
        try {
            const res = await api('register', {
                name: qs('#reg-name').value,
                email: qs('#reg-email').value,
                password: qs('#reg-password').value,
            });
            state.user = res.user;
            afterLogin();
        } catch (err) {
            qs('#register-error').textContent = err.error || 'Registration failed';
        }
    };
}

async function afterLogin() {
    showApp();
    qs('#user-name').textContent = state.user.name;
    qs('#export-btn').classList.toggle('hidden', state.user.role !== 'admin');
    await Promise.all([loadArtists(), loadStats()]);
}

function setupAppActions() {
    qs('#logout-btn').onclick = async () => {
        await api('logout');
        showAuth();
        state.user = null;
    };
    qs('#search').oninput = (e) => loadArtists(e.target.value);
    qs('#add-artist').onclick = () => {
        state.selectedArtist = null;
        clearArtistForm();
        qs('#empty-state').classList.add('hidden');
        qs('#artist-detail').classList.remove('hidden');
    };
    qs('#save-artist').onclick = saveArtist;
    qs('#delete-artist').onclick = deleteArtist;
    qs('#add-artwork').onclick = () => {
        resetArtworkForm();
        qs('#artwork-form').classList.remove('hidden');
    };
    qs('#save-artwork').onclick = saveArtwork;
    qs('#cancel-artwork').onclick = () => {
        qs('#artwork-form').classList.add('hidden');
        resetArtworkForm();
    };
    qs('#export-btn').onclick = () => {
        window.location = 'api.php?action=export_json';
    };
}

async function init() {
    setupAuth();
    setupAppActions();
    if (LOGGED_IN) {
        try {
            const res = await api('current_user', null, 'GET');
            if (res.user) {
                state.user = res.user;
                await afterLogin();
            } else {
                showAuth();
            }
        } catch {
            showAuth();
        }
    } else {
        showAuth();
    }
}

document.addEventListener('DOMContentLoaded', init);
