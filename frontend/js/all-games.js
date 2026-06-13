document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.genres-container');
    if (!container) return;
    container.innerHTML = '';

    const API_BASE = `http://144.31.78.248:8000`;
    const limit = 200;

    function normalizeResponse(json) {
        return Array.isArray(json) ? json : (json.items || json.games || json.data || []);
    }

    function createCard(g) {
        const img = g.cover_url || g.cover || 'img/plug-game-card.png';
        const score = (g.rating !== undefined && g.rating !== null) ? g.rating : (g.score || '0.0');
        const name = g.gname || g.name || g.title || 'Название игры';
        let genres = '—';
        if (Array.isArray(g.genres)) genres = g.genres.map(x => x.gName || x.name || x.gname).filter(Boolean).join(', ');
        const releaseDate = g.release_date || g.release || '00.00.0000';
        const likes = (g.likes !== undefined && g.likes !== null) ? g.likes : (g.like_count || 0);
        const price = (g.price !== undefined && g.price !== null) ? g.price : (g.min_price || null);

        const card = document.createElement('div');
        card.className = 'game-card';
        card.innerHTML = `
            <div class="game-card-info">
                <img src="${img}" alt="${name}">
                <div class="game-description">
                    <p class="game-score">${score}</p>
                    <p class="game-name">${name}</p>
                    <p class="game-genre">${genres}</p>
                    <p class="game-release-date">${releaseDate}</p>
                </div>
            </div>
            <div class="buttons-case">
                <button class="like-button">
                    <p class="like-status">Понравилось ${likes}</p>
                </button>
                <button class="buy-button">
                    <p class="buy-status">${price ? `Купить от ${price} ₽` : 'Бесплатно'}</p>
                </button>
            </div>
        `;

        const likeBtn = card.querySelector('.like-button');
        const likeStatus = card.querySelector('.like-status');
        let likeCount = Number(likes) || 0;
        likeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            likeCount += 1;
            likeStatus.textContent = `Понравилось ${likeCount}`;
            if (g.id) fetch(`${API_BASE}/api/games/${g.id}/like`, { method: 'POST' }).catch(() => {});
        });

        const buyBtn = card.querySelector('.buy-button');
        buyBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (g.buy_url) window.location.href = g.buy_url;
            else alert('Переход к покупке не настроен');
        });

        card.style.cursor = 'pointer';
        card.addEventListener('click', () => {
            const id = g.id ? encodeURIComponent(g.id) : encodeURIComponent(name);
            try { localStorage.setItem('selectedGame', JSON.stringify(g)); } catch (err) {}
            window.location.href = `game-page.html?id=${id}`;
        });

        return card;
    }

    fetch(`${API_BASE}/api/games?limit=${limit}&offset=0`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(json => {
            const games = normalizeResponse(json);
            if (!games.length) {
                container.innerHTML = '<p class="no-games">Нет игр для отображения</p>';
                return;
            }

            // Build genre -> games map
            const genreMap = new Map();
            games.forEach(g => {
                if (Array.isArray(g.genres) && g.genres.length) {
                    g.genres.forEach(gg => {
                        const gname = gg.gName || gg.name || gg.gname || String(gg);
                        if (!gname) return;
                        if (!genreMap.has(gname)) genreMap.set(gname, []);
                        genreMap.get(gname).push(g);
                    });
                } else {
                    // put into 'Без жанра'
                    const key = 'Без жанра';
                    if (!genreMap.has(key)) genreMap.set(key, []);
                    genreMap.get(key).push(g);
                }
            });

            // Sort genres alphabetically
            const genres = Array.from(genreMap.keys()).sort((a,b) => a.localeCompare(b, 'ru'));

            genres.forEach(genre => {
                const section = document.createElement('div');
                section.className = 'genre-section';
                const title = document.createElement('p');
                title.className = 'genre-title';
                title.textContent = genre;
                const strip = document.createElement('div');
                strip.className = 'genre-games';

                genreMap.get(genre).forEach(g => {
                    const card = createCard(g);
                    strip.appendChild(card);
                });

                section.appendChild(title);
                section.appendChild(strip);
                container.appendChild(section);
            });
        })
        .catch(err => {
            console.error('Failed to load games', err);
            container.innerHTML = '<p class="no-games">Ошибка загрузки игр</p>';
        });
});
