document.addEventListener('DOMContentLoaded', () => {
    const API_BASE = 'http://144.31.78.248:8000';

    function getQueryParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    function setText(selector, text) {
        const el = document.querySelector(selector);
        if (!el) return;
        el.textContent = text || '';
    }

    function setAttr(selector, attr, value) {
        const el = document.querySelector(selector);
        if (!el) return;
        if (value) el.setAttribute(attr, value);
    }

    function populateFromData(data) {
        if (!data) return;
        const name = data.gname || data.name || data.title || data.gameName || '';
        setText('.game-name', name);
        setAttr('.big-img', 'src', data.cover_url_big_horizontal || 'img/big-img.png');
        //вставить data.cover_url ||
        setAttr('.game-cover', 'src',  data.cover_url_horizontal || 'img/game-cover.png');
        setText('.game-description', data.description || data.long_description || data.short_description || '');

        const release = data.release_date || data.release || data.published || '';
        setText('.release-date p:nth-of-type(2)', release);

        setText('.developer p:nth-of-type(2)', data.developer || data.dev || '');
        setText('.publisher p:nth-of-type(2)', data.publisher || data.pub || '');

        const price = (data.price !== undefined && data.price !== null) ? `${data.price} ₽` : (data.min_price ? `${data.min_price} ₽` : (data.free ? 'Бесплатно' : '—'));
        document.querySelectorAll('.price').forEach(p => p.textContent = price);

        // insert game name into buy-card labels
        document.querySelectorAll('.by-name').forEach(el => el.textContent = name);

        // buy buttons
        document.querySelectorAll('.by-button').forEach(btn => {
            btn.addEventListener('click', () => {
                if (data.buy_url) window.location.href = data.buy_url;
                else alert('Переход к покупке не настроен');
            });
        });

        // system requirements
        if (data.system_requirements || data.requirements) {
            const req = data.system_requirements || data.requirements;
            const reqEl = document.querySelector('.requirements');
            if (reqEl) reqEl.innerHTML = Array.isArray(req) ? req.map(r => `<p>${r}</p>`).join('') : `<p>${req}</p>`;
        }
    }

    const id = getQueryParam('id');

    // try localStorage first (set by listing page)
    try {
        const raw = localStorage.getItem('selectedGame');
        if (raw) {
            const data = JSON.parse(raw);
            // optional: if id provided ensure it matches
            populateFromData(data);
            // clear temporary storage
            localStorage.removeItem('selectedGame');
            return;
        }
    } catch (err) {
        // ignore
    }

    // fallback: fetch from API if id is present
    if (id) {
        const decoded = decodeURIComponent(id);
        fetch(`${API_BASE}/api/games/${decoded}`)
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(json => {
                // API may wrap data
                const data = json && (json.data || json.game || json.item) ? (json.data || json.game || json.item) : json;
                populateFromData(data);
            })
            .catch(err => {
                console.error('Failed to load game details', err);
                setText('.game-name', 'Информация о игре недоступна');
            });
    } else {
        setText('.game-name', 'Игра не выбрана');
    }
});
