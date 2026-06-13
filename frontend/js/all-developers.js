document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.developers-container');
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
        let developers = '—';
        if (g.developer && typeof g.developer === 'string') {
            developers = g.developer;
        } else if (Array.isArray(g.developers)) {
            developers = g.developers.map(x => x.dName || x.name || x.dname).filter(Boolean).join(', ');
        }
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
                    <p class="game-developer">${developers}</p>
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

            // Build developer -> games map
            const developerMap = new Map();
            games.forEach(g => {
                let developers = [];
                
                // Check for single developer (string)
                if (g.developer && typeof g.developer === 'string') {
                    developers.push(g.developer);
                }
                // Check for multiple developers (array)
                else if (Array.isArray(g.developers) && g.developers.length) {
                    developers = g.developers.map(dd => dd.dName || dd.name || dd.dname || String(dd)).filter(Boolean);
                }
                
                if (developers.length) {
                    developers.forEach(dname => {
                        if (!developerMap.has(dname)) developerMap.set(dname, []);
                        developerMap.get(dname).push(g);
                    });
                } else {
                    // put into 'Без разработчика'
                    const key = 'Без разработчика';
                    if (!developerMap.has(key)) developerMap.set(key, []);
                    developerMap.get(key).push(g);
                }
            });

            // Sort developers alphabetically
            const developers = Array.from(developerMap.keys()).sort((a,b) => a.localeCompare(b, 'ru'));

            developers.forEach(developer => {
                const section = document.createElement('div');
                section.className = 'developer-section';
                const title = document.createElement('p');
                title.className = 'developer-title';
                title.textContent = developer;
                const strip = document.createElement('div');
                strip.className = 'developer-games';

                developerMap.get(developer).forEach(g => {
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
