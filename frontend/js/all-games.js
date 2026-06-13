document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.genres-container');
    if (!container) return;
    container.innerHTML = '';

    const API_BASE = `http://144.31.78.248:8000`;
    const limit = 10; // Load 10 games per request
    let offset = 0;
    let loading = false;
    let hasMore = true;
    let genreMap = new Map();
    let genreSections = new Map();

    // Create loader element
    const loader = document.createElement('div');
    loader.className = 'games-loader';
    loader.textContent = 'Загрузка...';
    loader.style.display = 'none';

    // Create sentinel for Intersection Observer
    const sentinel = document.createElement('div');

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

    function addGamesToGenres(games) {
        games.forEach(g => {
            if (Array.isArray(g.genres) && g.genres.length) {
                g.genres.forEach(gg => {
                    const gname = gg.gName || gg.name || gg.gname || String(gg);
                    if (!gname) return;
                    if (!genreMap.has(gname)) {
                        genreMap.set(gname, []);
                        // Create genre section on first encounter
                        const section = document.createElement('div');
                        section.className = 'genre-section';
                        const title = document.createElement('p');
                        title.className = 'genre-title';
                        title.textContent = gname;
                        const strip = document.createElement('div');
                        strip.className = 'genre-games';
                        section.appendChild(title);
                        section.appendChild(strip);
                        container.appendChild(section);
                        genreSections.set(gname, strip);
                    }
                    genreMap.get(gname).push(g);
                });
            } else {
                // put into 'Без жанра'
                const key = 'Без жанра';
                if (!genreMap.has(key)) {
                    genreMap.set(key, []);
                    const section = document.createElement('div');
                    section.className = 'genre-section';
                    const title = document.createElement('p');
                    title.className = 'genre-title';
                    title.textContent = key;
                    const strip = document.createElement('div');
                    strip.className = 'genre-games';
                    section.appendChild(title);
                    section.appendChild(strip);
                    container.appendChild(section);
                    genreSections.set(key, strip);
                }
                genreMap.get(key).push(g);
            }
        });

        // Add cards to genre sections
        games.forEach(g => {
            const genres = (Array.isArray(g.genres) && g.genres.length)
                ? g.genres.map(gg => gg.gName || gg.name || gg.gname).filter(Boolean)
                : ['Без жанра'];

            genres.forEach(genre => {
                if (genreSections.has(genre)) {
                    const card = createCard(g);
                    genreSections.get(genre).appendChild(card);
                }
            });
        });
    }

    function loadMore() {
        if (loading || !hasMore) return;
        loading = true;
        loader.style.display = 'block';
        container.appendChild(loader);

        fetch(`${API_BASE}/api/games?limit=${limit}&offset=${offset}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(json => {
                const games = normalizeResponse(json);
                if (!games.length) {
                    hasMore = false;
                    if (offset === 0) {
                        container.innerHTML = '<p class="no-games">Нет игр для отображения</p>';
                    }
                } else {
                    // Initial load - sort genres
                    if (offset === 0) {
                        addGamesToGenres(games);
                        // Sort genres alphabetically if needed
                        const genres = Array.from(genreMap.keys()).sort((a,b) => a.localeCompare(b, 'ru'));
                    } else {
                        addGamesToGenres(games);
                    }
                    offset += limit;

                    if (games.length < limit) {
                        hasMore = false;
                    }
                }
                loader.style.display = 'none';
                loading = false;
            })
            .catch(err => {
                console.error('Failed to load games', err);
                if (offset === 0) {
                    container.innerHTML = '<p class="no-games">Ошибка загрузки игр</p>';
                }
                loader.style.display = 'none';
                loading = false;
            });
    }

    // Intersection Observer for progressive loading
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadMore();
            }
        });
    }, { rootMargin: '200px' });

    observer.observe(sentinel);
    container.appendChild(sentinel);

    // Load initial games
    loadMore();
});
