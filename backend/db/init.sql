-- Таблица разработчиков
CREATE TABLE IF NOT EXISTS developers (
    id SERIAL PRIMARY KEY,
    dName VARCHAR(100) UNIQUE NOT NULL
);

-- Таблица издателей
CREATE TABLE IF NOT EXISTS publishers (
    id SERIAL PRIMARY KEY,
    pName VARCHAR(100) UNIQUE NOT NULL
);

-- Таблица игр
CREATE TABLE IF NOT EXISTS games (
    id SERIAL PRIMARY KEY,
    gName VARCHAR(200) NOT NULL,
    cover_url VARCHAR(1000),
    cover_url_horizontal VARCHAR(1000),
    cover_url_big_horizontal VARCHAR(1000),
    rating VARCHAR(4),
    release_date DATE,
    likes INT,
    price FLOAT,
    annotation TEXT,
    system_requirements TEXT,
    developer_id INTEGER NOT NULL REFERENCES developers(id) ON DELETE CASCADE,
    publisher_id INTEGER NOT NULL REFERENCES publishers(id) ON DELETE CASCADE
);

-- Таблица жанров
CREATE TABLE IF NOT EXISTS genres (
    id SERIAL PRIMARY KEY,
    gName VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS games_genres (
    game_id INTEGER NOT NULL REFERENCES games(id) ON DELETE CASCADE,
    genre_id INTEGER NOT NULL REFERENCES genres(id) ON DELETE CASCADE,
    PRIMARY KEY (game_id, genre_id)
);