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
INSERT INTO games (
    gName,
    cover_url,
    cover_url_horizontal,
    cover_url_big_horizontal,
    rating,
    release_date,
    likes,
    price,
    annotation,
    system_requirements,
    developer_id,
    publisher_id
) VALUES
(
    'Elden Ring',
    'https://i.pinimg.com/1200x/82/8e/0b/828e0b822038dfa8e178126af226aa1b.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1245620/header.jpg?t=1767883716',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1245620/ss_943bf6fe62352757d9070c1d33e50b92fe8539f1.1920x1080.jpg?t=1767883716',
    '9.5',
    '2022-02-25',
    15234,
    59.99,
    'Эпическая фэнтезийная RPG с открытым миром от создателей Dark Souls.',
    'ОС: Windows 10, Процессор: Intel Core i5-8400, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1060',
    1,
    1
),
(
    'The Witcher 3: Wild Hunt',
    'https://i.pinimg.com/736x/b1/40/2a/b1402a5bfb92feee210beaee99b88f32.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/292030/47273f809e429a272f35c252b49d9bba83e2cf30/header_russian.jpg?t=1768303991',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/292030/ss_107600c1337accc09104f7a8aa7f275f23cad096.1920x1080.jpg?t=1768303991',
    '9.8',
    '2015-05-19',
    28456,
    39.99,
    'Мастерски созданная RPG в огромном фэнтезийном мире.',
    'ОС: Windows 7, Процессор: Intel Core i5-2500K, ОЗУ: 8 GB, Видеокарта: NVIDIA GTX 660',
    2,
    2
),
(
    'The Legend of Zelda: Breath of the Wild',
    'https://i.pinimg.com/1200x/0e/90/bc/0e90bcedeee6f2d0e0a8b023ccc4a2a0.jpg',
    'https://i.pinimg.com/1200x/cb/58/42/cb58429b4325cc95c36743c76abfea6d.jpg',
    'https://i.pinimg.com/1200x/ea/23/ff/ea23ff81265646a2286c1e10ec08a9e4.jpg',
    '9.9',
    '2017-03-03',
    19876,
    49.99,
    'Революционная приключенческая игра в открытом мире.',
    'Nintendo Switch',
    3,
    3
),
(
    'Red Dead Redemption 2',
    'https://i.pinimg.com/1200x/c2/30/ce/c230ceb7a3d03b6fd0bac465161ad140.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1174180/header.jpg?t=1759502961',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1174180/ss_d1a8f5a69155c3186c65d1da90491fcfd43663d9.1920x1080.jpg?t=1759502961',
    '9.7',
    '2018-10-26',
    22345,
    59.99,
    'Эпическая история жизни в Америке на закате эпохи дикого запада.',
    'ОС: Windows 10, Процессор: Intel Core i7-4770K, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1060',
    4,
    4
),
(
    'The Elder Scrolls V: Skyrim',
    'https://i.pinimg.com/736x/b1/ff/05/b1ff0585528cd5b263ac7fe3b7d23c05.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/489830/header.jpg?t=1753715778',
    'https://i.pinimg.com/1200x/6c/00/8b/6c008b629f39dd946490d2b292276f45.jpg',
    '9.4',
    '2011-11-11',
    32109,
    29.99,
    'Легендарная RPG в мире скандинавской мифологии.',
    'ОС: Windows 7, Процессор: Intel Core i5-750, ОЗУ: 8 GB, Видеокарта: NVIDIA GTX 470',
    5,
    5
),
(
    'The Last of Us Part II',
    'https://i.pinimg.com/1200x/4f/bc/46/4fbc466ff15ac4d84dc23db8dfd85b48.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2531310/header.jpg?t=1750959180',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2531310/0549d5b3b556abfea5ea02e2a8937fc986e4eba0/ss_0549d5b3b556abfea5ea02e2a8937fc986e4eba0.1920x1080.jpg?t=1750959180',
    '9.3',
    '2020-06-19',
    16789,
    49.99,
    'Напряженный экшен-хоррор с глубоким сюжетом.',
    'ОС: Windows 10, Процессор: Intel Core i7-8700, ОЗУ: 16 GB, Видеокарта: NVIDIA RTX 2060',
    6,
    6
),
(
    'Assassin''s Creed Valhalla',
    'https://i.pinimg.com/736x/b4/83/f3/b483f3eb9e9ececa9259f29905fb768a.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2208920/header.jpg?t=1754572990',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2208920/ss_103481084a59b34837113daf27c04679caf743f3.1920x1080.jpg?t=1754572990',
    '8.5',
    '2020-11-10',
    12345,
    49.99,
    'Станьте викингом и захватите Англию.',
    'ОС: Windows 10, Процессор: Intel Core i5-4460, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1060',
    7,
    7
),
(
    'Mass Effect Legendary Edition',
    'https://i.pinimg.com/736x/4e/68/ab/4e68ab0ac6981623a8f7f1c04f5cb77b.jpg',
    'https://i.pinimg.com/1200x/3c/a9/64/3ca9644f709dd49764061739cd603ab9.jpg',
    'https://i.pinimg.com/1200x/87/1f/0f/871f0fd97875f6e2fbaf88ddb40af0a3.jpg',
    '9.2',
    '2021-05-14',
    9876,
    59.99,
    'Коллекция легендарной космической RPG-трилогии.',
    'ОС: Windows 10, Процессор: Intel Core i5-3570, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 970',
    8,
    8
),
(
    'Diablo IV',
    'https://i.pinimg.com/1200x/5f/08/d8/5f08d8a8975b69a1fe9e1a12e30b8d62.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2344520/80f21a42e378b93e8fbb68ee43103be8ab84891b/header.jpg?t=1780334052',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2344520/14880a1527241507d9a2d26f439f83fac20c4246/ss_14880a1527241507d9a2d26f439f83fac20c4246.1920x1080.jpg?t=1780334052',
    '8.8',
    '2023-06-06',
    34567,
    69.99,
    'Темный экшен-RPG с открытым миром.',
    'ОС: Windows 10, Процессор: Intel Core i5-8600, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 970',
    9,
    9
),
(
    'Final Fantasy VII Remake',
    'https://i.pinimg.com/1200x/ab/55/15/ab5515998dd5f88423eecdc9bc0ddde8.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1462040/header.jpg?t=1773895755',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1462040/ss_170c616f08812827d70ac2d9099447a52e114546.1920x1080.jpg?t=1773895755',
    '8.9',
    '2021-12-16',
    14567,
    49.99,
    'Современное переосмысление культовой RPG.',
    'ОС: Windows 10, Процессор: Intel Core i5-3330, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 780',
    10,
    10
),
(
    'Half-Life 2',
    'https://i.pinimg.com/736x/25/06/1e/25061e5f33e84d63fd4f767c5c3bcabc.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/220/header.jpg?t=1745368545',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/220/ss_0e499071a60a20b24149ad65a8edb769250f2921.1920x1080.jpg?t=1745368545',
    '9.6',
    '2004-11-16',
    45678,
    9.99,
    'Легендарный шутер от первого лица.',
    'ОС: Windows XP, Процессор: 1.7 GHz, ОЗУ: 512 MB, Видеокарта: DirectX 8.1',
    11,
    11
),
(
    'Minecraft',
    'https://i.pinimg.com/736x/25/16/d5/2516d5f232d2f20201a231b24a9492f9.jpg',
    'https://i.pinimg.com/1200x/02/8d/b9/028db91f448f42890439403aeca89d5a.jpg',
    'https://i.pinimg.com/736x/2e/10/a1/2e10a1f6af75ef72c7efadfb00b90d94.jpg',
    '9.4',
    '2011-11-18',
    98765,
    26.99,
    'Игра о строительстве из кубиков.',
    'ОС: Windows 10, Процессор: Intel Core i3-3210, ОЗУ: 4 GB, Видеокарта: Intel HD Graphics 4000',
    12,
    12
),
(
    'Resident Evil 4 Remake',
    'https://i.pinimg.com/736x/73/79/a8/7379a8598e87966329b09ff921b3bf99.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2050650/header.jpg?t=1772502922',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2050650/ss_0554b945aafc847d55f780f7968de00aafa968a3.1920x1080.jpg?t=1772502922',
    '9.1',
    '2023-03-24',
    23456,
    59.99,
    'Шедевр хоррора в новом исполнении.',
    'ОС: Windows 10, Процессор: Intel Core i5-7500, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1050 Ti',
    13,
    13
),
(
    'God of War Ragnarök',
    'https://i.pinimg.com/1200x/92/97/c3/9297c3fcdfae3138596b65379496a6bc.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2322010/header.jpg?t=1776465233',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2322010/ss_974a7b998c0c14da7fe52a342cf36c98850a57ac.1920x1080.jpg?t=1776465233',
    '9.6',
    '2022-11-09',
    18765,
    69.99,
    'Эпическое завершение скандинавской саги.',
    'PlayStation 5 / PlayStation 4',
    14,
    6
),
(
    'Horizon Forbidden West',
    'https://i.pinimg.com/736x/db/be/da/dbbeda64fcd2c51283841b5e17301010.jpg',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2420110/header.jpg?t=1776465869',
    'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2420110/ss_a4a19b86e80488f3d608e835e5ae3086760db866.1920x1080.jpg?t=1776465869',
    '9.0',
    '2022-02-18',
    15678,
    59.99,
    'Продолжение захватывающего приключения Элой.',
    'ОС: Windows 10, Процессор: Intel Core i5-8600, ОЗУ: 16 GB, Видеокарта: NVIDIA GTX 1650',
    15,
    6
);

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
(1, 1), (1, 2), (1, 4),
(2, 1), (2, 2), (2, 4),
(3, 2), (3, 3), (3, 4),
(4, 2), (4, 3), (4, 4),
(5, 1), (5, 2), (5, 4),
(6, 2), (6, 3), (6, 6),
(7, 2), (7, 1), (7, 4),
(8, 1), (8, 5), (8, 4),
(9, 1), (9, 2), (9, 6),
(10, 1), (10, 2),
(11, 5), (11, 2),
(12, 7), (12, 14), (12, 4),
(13, 6), (13, 2),
(14, 2), (14, 3), (14, 1),
(15, 2), (15, 1), (15, 4);

-- Проверочные запросы
SELECT 'developers' as table_name, COUNT(*) as count FROM developers
UNION ALL
SELECT 'publishers', COUNT(*) FROM publishers
UNION ALL
SELECT 'games', COUNT(*) FROM games
UNION ALL
SELECT 'genres', COUNT(*) FROM genres
UNION ALL
SELECT 'games_genres', COUNT(*) FROM games_genres;

SELECT g.gname as game, STRING_AGG(gen.gname, ', ') as genres, g.rating, g.price
FROM games g
LEFT JOIN games_genres gg ON g.id = gg.game_id
LEFT JOIN genres gen ON gg.genre_id = gen.id
GROUP BY g.id, g.gname, g.rating, g.price
ORDER BY g.rating DESC;