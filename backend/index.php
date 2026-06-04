<?php
/*
 * REST API для доступа к базе данных PostgreSQL (Игровая библиотека)
 * Версия: 1.1
 */

// Заголовки безопасности и CORS
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// CORS (настройте origins для production)
$allowedOrigins = ['http://localhost:3000', 'http://localhost:8080'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: {$origin}");
} else {
    header('Access-Control-Allow-Origin: *'); // Только для разработки
}

header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 3600');

// Обработка preflight запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Разрешены только GET запросы
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Конфигурация БД
$config = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '5432',
    'dbname' => getenv('DB_NAME') ?: 'lib',
    'user' => getenv('DB_USER') ?: 'user',
    'password' => getenv('DB_PASSWORD') ?: 'user',
    'charset' => 'utf8'
];

// Подключение к БД
try {
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};options='--client_encoding={$config['charset']}'";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false
    ]);
} catch (PDOException $e) {
    error_log('DB Connection Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Вспомогательные функции
function sendJson($data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function sendError(string $message, int $status = 400): void
{
    sendJson(['error' => $message], $status);
}

/*
 * Экранирование спецсимволов для LIKE оператора
 */
function escapeLike(string $value): string
{
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
}

/*
 * Валидация и получение параметров пагинации
 */
function getPaginationParams(): array
{
    $limit = isset($_GET['limit']) ? filter_var($_GET['limit'], FILTER_VALIDATE_INT, ['options' => [
        'default' => 20,
        'min_range' => 1,
        'max_range' => 100
    ]]) : 20;
    
    $offset = isset($_GET['offset']) ? filter_var($_GET['offset'], FILTER_VALIDATE_INT, ['options' => [
        'default' => 0,
        'min_range' => 0
    ]]) : 0;
    
    return [$limit, $offset];
}

/*
 * Получение поискового запроса
 */
function getSearchQuery(): ?string
{
    $q = isset($_GET['q']) ? trim($_GET['q']) : null;
    return ($q !== null && $q !== '') ? $q : null;
}

/*
 * Основные поля для SELECT игр
 */
function getGameSelectFields(): string
{
    return "g.id, g.gName, g.cover_url, g.rating, g.release_date, 
            g.likes, g.price, g.annotation, g.system_requirements,
            d.dName AS developer, p.pName AS publisher,
            COALESCE(
                json_agg(
                    DISTINCT jsonb_build_object('id', gen.id, 'gName', gen.gName)
                ) FILTER (WHERE gen.id IS NOT NULL), 
                '[]'::json
            ) AS genres";
}

/*
 * Базовые JOIN'ы для запросов игр
 */
function getGameJoins(): string
{
    return "FROM games g
            LEFT JOIN developers d ON g.developer_id = d.id
            LEFT JOIN publishers p ON g.publisher_id = p.id
            LEFT JOIN games_genres gg ON gg.game_id = g.id
            LEFT JOIN genres gen ON gen.id = gg.genre_id";
}

/*
 * Выполнение запроса с параметрами
 */
function executeQuery(PDO $pdo, string $sql, array $params): array
{
    $stmt = $pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $value, $type);
    }
    
    $stmt->execute();
    return $stmt->fetchAll();
}

/*
 * Декодирование жанров в результатах
 */
function decodeGenres(array &$rows): void
{
    foreach ($rows as &$row) {
        if (isset($row['genres']) && is_string($row['genres'])) {
            $row['genres'] = json_decode($row['genres'], true) ?? [];
        }
    }
}

/*
 * Построение WHERE условия для поиска
 */
function buildSearchCondition(?string $q, array &$params, string $field = 'g.gName'): string
{
    if ($q === null) return '';
    
    $params[':q'] = '%' . escapeLike($q) . '%';
    return "AND ({$field} ILIKE :q OR d.dName ILIKE :q OR p.pName ILIKE :q)";
}

// Парсинг URL
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

// ============== МАРШРУТЫ API ==============

try {
    // GET /api/games и GET /api/games/{id}
    if (preg_match('#^/api/games(?:/(\d+))?$#', $path, $matches)) {
        // Получение одной игры
        if (!empty($matches[1])) {
            $gameId = (int)$matches[1];
            
            $sql = "SELECT " . getGameSelectFields() . "
                    " . getGameJoins() . "
                    WHERE g.id = :id
                    GROUP BY g.id, d.dName, p.pName
                    LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $gameId]);
            $game = $stmt->fetch();
            
            if (!$game) {
                sendError('Game not found', 404);
            }
            
            $game['genres'] = json_decode($game['genres'], true) ?? [];
            sendJson($game);
        }
        
        // Получение списка игр
        [$limit, $offset] = getPaginationParams();
        $q = getSearchQuery();
        $params = [];
        
        $searchCondition = buildSearchCondition($q, $params);
        
        $sql = "SELECT " . getGameSelectFields() . "
                " . getGameJoins() . "
                WHERE 1=1 {$searchCondition}
                GROUP BY g.id, d.dName, p.pName
                ORDER BY g.id
                LIMIT :limit OFFSET :offset";
        
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $games = executeQuery($pdo, $sql, $params);
        decodeGenres($games);
        
        sendJson([
            'items' => $games,
            'count' => count($games),
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    // GET /api/genres
    if ($path === '/api/genres') {
        $q = getSearchQuery();
        
        if ($q) {
            $stmt = $pdo->prepare('SELECT id, gName FROM genres WHERE gName ILIKE :q ORDER BY gName');
            $stmt->execute([':q' => '%' . escapeLike($q) . '%']);
            $genres = $stmt->fetchAll();
        } else {
            $genres = $pdo->query('SELECT id, gName FROM genres ORDER BY gName')->fetchAll();
        }
        
        sendJson($genres);
    }
    
    // GET /api/developers
    if ($path === '/api/developers') {
        $q = getSearchQuery();
        
        if ($q) {
            $stmt = $pdo->prepare('SELECT id, dName FROM developers WHERE dName ILIKE :q ORDER BY dName');
            $stmt->execute([':q' => '%' . escapeLike($q) . '%']);
            $developers = $stmt->fetchAll();
        } else {
            $developers = $pdo->query('SELECT id, dName FROM developers ORDER BY dName')->fetchAll();
        }
        
        sendJson($developers);
    }
    
    // GET /api/publishers
    if ($path === '/api/publishers') {
        $q = getSearchQuery();
        
        if ($q) {
            $stmt = $pdo->prepare('SELECT id, pName FROM publishers WHERE pName ILIKE :q ORDER BY pName');
            $stmt->execute([':q' => '%' . escapeLike($q) . '%']);
            $publishers = $stmt->fetchAll();
        } else {
            $publishers = $pdo->query('SELECT id, pName FROM publishers ORDER BY pName')->fetchAll();
        }
        
        sendJson($publishers);
    }
    
    // GET /api/publishers/{id}/games
    if (preg_match('#^/api/publishers/(\d+)/games$#', $path, $matches)) {
        $pubId = (int)$matches[1];
        [$limit, $offset] = getPaginationParams();
        $q = getSearchQuery();
        $params = [':pubId' => $pubId];
        
        $searchCondition = buildSearchCondition($q, $params);
        
        $sql = "SELECT " . getGameSelectFields() . "
                " . getGameJoins() . "
                WHERE g.publisher_id = :pubId {$searchCondition}
                GROUP BY g.id, d.dName, p.pName
                ORDER BY g.id
                LIMIT :limit OFFSET :offset";
        
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $games = executeQuery($pdo, $sql, $params);
        decodeGenres($games);
        
        sendJson([
            'items' => $games,
            'count' => count($games),
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    // GET /api/developers/{id}/games
    if (preg_match('#^/api/developers/(\d+)/games$#', $path, $matches)) {
        $devId = (int)$matches[1];
        [$limit, $offset] = getPaginationParams();
        $q = getSearchQuery();
        $params = [':devId' => $devId];
        
        $searchCondition = buildSearchCondition($q, $params);
        
        $sql = "SELECT " . getGameSelectFields() . "
                " . getGameJoins() . "
                WHERE g.developer_id = :devId {$searchCondition}
                GROUP BY g.id, d.dName, p.pName
                ORDER BY g.id
                LIMIT :limit OFFSET :offset";
        
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $games = executeQuery($pdo, $sql, $params);
        decodeGenres($games);
        
        sendJson([
            'items' => $games,
            'count' => count($games),
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    // GET /api/genres/{id}/games
    if (preg_match('#^/api/genres/(\d+)/games$#', $path, $matches)) {
        $genreId = (int)$matches[1];
        [$limit, $offset] = getPaginationParams();
        $q = getSearchQuery();
        $params = [':genreId' => $genreId];
        
        $searchCondition = buildSearchCondition($q, $params);
        
        $sql = "SELECT " . getGameSelectFields() . "
                " . getGameJoins() . "
                WHERE EXISTS (
                    SELECT 1 FROM games_genres gg2 
                    WHERE gg2.game_id = g.id AND gg2.genre_id = :genreId
                ) {$searchCondition}
                GROUP BY g.id, d.dName, p.pName
                ORDER BY g.id
                LIMIT :limit OFFSET :offset";
        
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $games = executeQuery($pdo, $sql, $params);
        decodeGenres($games);
        
        sendJson([
            'items' => $games,
            'count' => count($games),
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    // Маршрут не найден
    sendError('Endpoint not found', 404);
    
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    sendError('Internal Server Error', 500);
} catch (Exception $e) {
    error_log('Application Error: ' . $e->getMessage());
    sendError('Internal Server Error', 500);
}