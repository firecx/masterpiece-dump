-- Заполнение таблицы разработчиков
INSERT INTO developers (dName) VALUES
('FromSoftware'),
('CD Projekt Red'),
('Nintendo'),
('Rockstar Games'),
('Bethesda Game Studios'),
('Naughty Dog'),
('Ubisoft Montreal'),
('BioWare'),
('Blizzard Entertainment'),
('Square Enix'),
('Valve Corporation'),
('Mojang Studios'),
('Capcom'),
('Santa Monica Studio'),
('Guerrilla Games');

-- Заполнение таблицы издателей
INSERT INTO publishers (pName) VALUES
('Bandai Namco Entertainment'),
('CD Projekt'),
('Nintendo'),
('Rockstar Games'),
('Bethesda Softworks'),
('Sony Interactive Entertainment'),
('Ubisoft'),
('Electronic Arts'),
('Blizzard Entertainment'),
('Square Enix'),
('Valve Corporation'),
('Mojang Studios'),
('Capcom'),
('Activision'),
('Take-Two Interactive');

-- Заполнение таблицы игр
INSERT INTO games (gName, cover_url, rating, release_date, likes, price, annotation, system_requirements, developer_id, publisher_id) VALUES
('Elden Ring', 'https://i.pinimg.com/1200x/82/8e/0b/828e0b822038dfa8e178126af226aa1b.jpg', '9.5', '2022-02-25', 15234, 59.99, 'Эпическая фэнтезийная RPG с открытым миром от создателей Dark Souls.', 'ОС: Windows 10, Процессор: Intel Core i5-8400, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1060', 1, 1),
('The Witcher 3: Wild Hunt', 'https://i.pinimg.com/736x/b1/40/2a/b1402a5bfb92feee210beaee99b88f32.jpg', '9.8', '2015-05-19', 28456, 39.99, 'Мастерски созданная RPG в огромном фэнтезийном мире.', 'ОС: Windows 7, Процессор: Intel Core i5-2500K, ОЗУ: 8 GB, Видеокарта: NVIDIA GTX 660', 2, 2),
('The Legend of Zelda: Breath of the Wild', 'https://i.pinimg.com/1200x/0e/90/bc/0e90bcedeee6f2d0e0a8b023ccc4a2a0.jpg', '9.9', '2017-03-03', 19876, 49.99, 'Революционная приключенческая игра в открытом мире.', 'Nintendo Switch', 3, 3),
('Red Dead Redemption 2', 'https://i.pinimg.com/1200x/c2/30/ce/c230ceb7a3d03b6fd0bac465161ad140.jpg', '9.7', '2018-10-26', 22345, 59.99, 'Эпическая история жизни в Америке на закате эпохи дикого запада.', 'ОС: Windows 10, Процессор: Intel Core i7-4770K, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1060', 4, 4),
('The Elder Scrolls V: Skyrim', 'https://i.pinimg.com/736x/b1/ff/05/b1ff0585528cd5b263ac7fe3b7d23c05.jpg', '9.4', '2011-11-11', 32109, 29.99, 'Легендарная RPG в мире скандинавской мифологии.', 'ОС: Windows 7, Процессор: Intel Core i5-750, ОЗУ: 8 GB, Видеокарта: NVIDIA GTX 470', 5, 5),
('The Last of Us Part II', 'https://i.pinimg.com/1200x/4f/bc/46/4fbc466ff15ac4d84dc23db8dfd85b48.jpg', '9.3', '2020-06-19', 16789, 49.99, 'Напряженный экшен-хоррор с глубоким сюжетом.', 'ОС: Windows 10, Процессор: Intel Core i7-8700, ОЗУ: 16 GB, Видеокарта: NVIDIA RTX 2060', 6, 6),
('Assassin''s Creed Valhalla', 'https://i.pinimg.com/736x/b4/83/f3/b483f3eb9e9ececa9259f29905fb768a.jpg', '8.5', '2020-11-10', 12345, 49.99, 'Станьте викингом и захватите Англию.', 'ОС: Windows 10, Процессор: Intel Core i5-4460, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1060', 7, 7),
('Mass Effect Legendary Edition', 'https://i.pinimg.com/736x/4e/68/ab/4e68ab0ac6981623a8f7f1c04f5cb77b.jpg', '9.2', '2021-05-14', 9876, 59.99, 'Коллекция легендарной космической RPG-трилогии.', 'ОС: Windows 10, Процессор: Intel Core i5-3570, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 970', 8, 8),
('Diablo IV', 'https://i.pinimg.com/1200x/5f/08/d8/5f08d8a8975b69a1fe9e1a12e30b8d62.jpg', '8.8', '2023-06-06', 34567, 69.99, 'Темный экшен-RPG с открытым миром.', 'ОС: Windows 10, Процессор: Intel Core i5-8600, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 970', 9, 9),
('Final Fantasy VII Remake', 'https://i.pinimg.com/1200x/ab/55/15/ab5515998dd5f88423eecdc9bc0ddde8.jpg', '8.9', '2021-12-16', 14567, 49.99, 'Современное переосмысление культовой RPG.', 'ОС: Windows 10, Процессор: Intel Core i5-3330, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 780', 10, 10),
('Half-Life 2', 'https://i.pinimg.com/736x/25/06/1e/25061e5f33e84d63fd4f767c5c3bcabc.jpg', '9.6', '2004-11-16', 45678, 9.99, 'Легендарный шутер от первого лица.', 'ОС: Windows XP, Процессор: 1.7 GHz, ОЗУ: 512 MB, Видеокарта: DirectX 8.1', 11, 11),
('Minecraft', 'https://i.pinimg.com/736x/25/16/d5/2516d5f232d2f20201a231b24a9492f9.jpg', '9.4', '2011-11-18', 98765, 26.99, 'Игра о строительстве из кубиков.', 'ОС: Windows 10, Процессор: Intel Core i3-3210, ОЗУ: 4 GB, Видеокарта: Intel HD Graphics 4000', 12, 12),
('Resident Evil 4 Remake', 'https://i.pinimg.com/736x/73/79/a8/7379a8598e87966329b09ff921b3bf99.jpg', '9.1', '2023-03-24', 23456, 59.99, 'Шедевр хоррора в новом исполнении.', 'ОС: Windows 10, Процессор: Intel Core i5-7500, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1050 Ti', 13, 13),
('God of War Ragnarök', 'https://i.pinimg.com/1200x/92/97/c3/9297c3fcdfae3138596b65379496a6bc.jpg', '9.6', '2022-11-09', 18765, 69.99, 'Эпическое завершение скандинавской саги.', 'PlayStation 5 / PlayStation 4', 14, 6),
('Horizon Forbidden West', 'https://i.pinimg.com/736x/db/be/da/dbbeda64fcd2c51283841b5e17301010.jpg', '9.0', '2022-02-18', 15678, 59.99, 'Продолжение захватывающего приключения Элой.', 'ОС: Windows 10, Процессор: Intel Core i5-8600, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1650', 15, 6);

-- Заполнение таблицы жанров
INSERT INTO genres (gName) VALUES
('RPG'),
('Action'),
('Adventure'),
('Open World'),
('Shooter'),
('Horror'),
('Survival'),
('Strategy'),
('Simulation'),
('Puzzle'),
('Fighting'),
('Racing'),
('Sports'),
('Sandbox'),
('Stealth');

-- Заполнение связей игры-жанры
INSERT INTO games_genres (game_id, genre_id) VALUES
-- Elden Ring
(1, 1), (1, 2), (1, 4),
-- The Witcher 3
(2, 1), (2, 2), (2, 4),
-- Zelda BotW
(3, 2), (3, 3), (3, 4),
-- RDR2
(4, 2), (4, 3), (4, 4),
-- Skyrim
(5, 1), (5, 2), (5, 4),
-- TLOU2
(6, 2), (6, 3), (6, 6),
-- AC Valhalla
(7, 2), (7, 1), (7, 4),
-- Mass Effect
(8, 1), (8, 5), (8, 4),
-- Diablo IV
(9, 1), (9, 2), (9, 6),
-- FF VII Remake
(10, 1), (10, 2),
-- Half-Life 2
(11, 5), (11, 2),
-- Minecraft
(12, 7), (12, 14), (12, 4),
-- RE4 Remake
(13, 6), (13, 2),
-- God of War Ragnarök
(14, 2), (14, 3), (14, 1),
-- Horizon Forbidden West
(15, 2), (15, 1), (15, 4);

-- Проверочные запросы
-- Количество записей в каждой таблице
SELECT 'developers' as table_name, COUNT(*) as count FROM developers
UNION ALL
SELECT 'publishers', COUNT(*) FROM publishers
UNION ALL
SELECT 'games', COUNT(*) FROM games
UNION ALL
SELECT 'genres', COUNT(*) FROM genres
UNION ALL
SELECT 'games_genres', COUNT(*) FROM games_genres;

-- Пример: получить все игры с их жанрами
SELECT g.gname as game, STRING_AGG(gen.gname, ', ') as genres, g.rating, g.price
FROM games g
LEFT JOIN games_genres gg ON g.id = gg.game_id
LEFT JOIN genres gen ON gg.genre_id = gen.id
GROUP BY g.id, g.gname, g.rating, g.price
ORDER BY g.rating DESC;