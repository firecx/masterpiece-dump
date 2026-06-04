document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.new-games');
    if (!container) return;
    container.innerHTML = ''; // clear static placeholders

    const API_BASE = `http://localhost:8080`;

    const limit = 10;
    let offset = 0;
    let loading = false;
    let hasMore = true;

    const loader = document.createElement('div');
    loader.className = 'games-loader';
    loader.textContent = 'Загрузка...';

    const sentinel = document.createElement('div');
    sentinel.className = 'games-sentinel';

    function normalizeResponse(json) {
        return Array.isArray(json) ? json : (json.items || json.games || json.data || []);
    }

    function createCard(g) {
        // Map API fields from backend example
        //заменить на g.cover_url
        const img =  'img/plug-game-card.png';
        const score = (g.rating !== undefined && g.rating !== null) ? g.rating : (g.score || '0.0');
        const name = g.gname || g.name || g.title || 'Название игры';
        let genres = 'Жанр1, жанр2';
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
        likeBtn.addEventListener('click', () => {
            likeCount += 1;
                likeStatus.textContent = `Понравилось ${likeCount}`;
                if (g.id) fetch(`${API_BASE}/api/games/${g.id}/like`, { method: 'POST' }).catch(() => {});
        });

        const buyBtn = card.querySelector('.buy-button');
        buyBtn.addEventListener('click', () => {
            if (g.buy_url) window.location.href = g.buy_url;
            else alert('Переход к покупке не настроен');
        });

        // make the whole card clickable (except the like and buy buttons)
        card.style.cursor = 'pointer';
        card.addEventListener('click', (e) => {
            if (e.target.closest('.like-button') || e.target.closest('.buy-button')) return;
            const id = g.id ? encodeURIComponent(g.id) : encodeURIComponent(name);
            window.location.href = `game-page.html?id=${id}`;
        });

        return card;
    }

    function showNoGames() {
        container.innerHTML = '<p class="no-games">Нет игр для отображения</p>';
    }

    function loadMore() {
        if (loading || !hasMore) return;
        loading = true;
        container.appendChild(loader);

        fetch(`${API_BASE}/api/games?limit=${limit}&offset=${offset}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(json => {
                const games = normalizeResponse(json);
                if (!games.length && offset === 0) {
                    showNoGames();
                    hasMore = false;
                    if (observer) observer.disconnect();
                    container.removeEventListener('scroll', onScroll);
                    return;
                }

                games.forEach(g => container.appendChild(createCard(g)));
                offset += games.length;
                if (games.length < limit) {
                    hasMore = false;
                    // no more: remove sentinel
                    if (observer) observer.disconnect();
                    container.removeEventListener('scroll', onScroll);
                }
            })
            .catch(err => {
                console.error('Failed to load games', err);
                if (offset === 0) container.innerHTML = '<p class="no-games">Ошибка загрузки новинок</p>';
                hasMore = false;
                if (observer) observer.disconnect();
                container.removeEventListener('scroll', onScroll);
            })
            .finally(() => {
                loading = false;
                if (loader.parentNode === container) container.removeChild(loader);
            });
    }

    // IntersectionObserver for infinite scroll
    let observer = null;
    try {
        // If the games container is scrollable, use it as the observer root
        observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) loadMore();
            });
        }, { root: container, rootMargin: '200px' });
    } catch (e) {
        observer = null;
    }

    container.appendChild(sentinel);
    if (observer) observer.observe(sentinel);

    // Fallback: also trigger loadMore when user scrolls near the end of the container
    const SCROLL_THRESHOLD = 100; // px from edge to trigger (works for vertical and horizontal)
    function onScroll() {
        if (loading || !hasMore) return;
        // detect dominant scroll axis
        const isHorizontal = container.scrollWidth > container.clientWidth && container.scrollWidth > container.scrollHeight;
        if (isHorizontal) {
            const distanceFromRight = container.scrollWidth - container.scrollLeft - container.clientWidth;
            if (distanceFromRight < SCROLL_THRESHOLD) loadMore();
        } else {
            const distanceFromBottom = container.scrollHeight - container.scrollTop - container.clientHeight;
            if (distanceFromBottom < SCROLL_THRESHOLD) loadMore();
        }
    }
    container.addEventListener('scroll', onScroll, { passive: true });

    // initial load
    loadMore();
});
