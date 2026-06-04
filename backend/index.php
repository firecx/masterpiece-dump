<?php
// REST API для доступа к базе данных PostgreSQL
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(204);
	exit;
}

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'lib';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASSWORD') ?: 'user';

$dsn = "pgsql:host={$host};port={$port};dbname={$db}";

try {
	$pdo = new PDO($dsn, $user, $pass, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);
} catch (Exception $e) {
	http_response_code(500);
	echo json_encode(['error' => 'DB connection failed', 'detail' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
	exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

function send_json($data, $status = 200)
{
	http_response_code($status);
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

// GET /api/games and GET /api/games/{id}
if ($method === 'GET' && preg_match('#^/api/games(?:/(\d+))?$#', $path, $m)) {
	if (isset($m[1]) && $m[1] !== '') {
		$id = (int)$m[1];
		$sql = "SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price, g.annotation, g.system_requirements,
					   d.dName AS developer, p.pName AS publisher,
					   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
				FROM games g
				LEFT JOIN developers d ON g.developer_id = d.id
				LEFT JOIN publishers p ON g.publisher_id = p.id
				LEFT JOIN games_genres gg ON gg.game_id = g.id
				LEFT JOIN genres gen ON gen.id = gg.genre_id
				WHERE g.id = :id
				GROUP BY g.id, d.dName, p.pName
				LIMIT 1";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':id' => $id]);
		$row = $stmt->fetch();
		if (!$row) {
			send_json(['error' => 'Not found'], 404);
		}
		$row['genres'] = json_decode($row['genres'], true);
		send_json($row);
	}

	// list games
	$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
	if ($limit < 1) $limit = 20;
	$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
	$q = isset($_GET['q']) ? trim($_GET['q']) : null;

	$params = [':limit' => $limit, ':offset' => $offset];
	if ($q !== null && $q !== '') {
		$sql = "SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
					   d.dName AS developer, p.pName AS publisher,
					   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
				FROM games g
				LEFT JOIN developers d ON g.developer_id = d.id
				LEFT JOIN publishers p ON g.publisher_id = p.id
				LEFT JOIN games_genres gg ON gg.game_id = g.id
				LEFT JOIN genres gen ON gen.id = gg.genre_id
				WHERE (g.gName ILIKE :q OR d.dName ILIKE :q OR p.pName ILIKE :q)
				GROUP BY g.id, d.dName, p.pName
				ORDER BY g.id
				LIMIT :limit OFFSET :offset";
		$params[':q'] = "%{$q}%";
	} else {
		$sql = "SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
					   d.dName AS developer, p.pName AS publisher,
					   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
				FROM games g
				LEFT JOIN developers d ON g.developer_id = d.id
				LEFT JOIN publishers p ON g.publisher_id = p.id
				LEFT JOIN games_genres gg ON gg.game_id = g.id
				LEFT JOIN genres gen ON gen.id = gg.genre_id
				GROUP BY g.id, d.dName, p.pName
				ORDER BY g.id
				LIMIT :limit OFFSET :offset";
	}

	$stmt = $pdo->prepare($sql);
	foreach ($params as $k => $v) {
		if ($k === ':limit' || $k === ':offset') {
			$stmt->bindValue($k, $v, PDO::PARAM_INT);
		} else {
			$stmt->bindValue($k, $v, PDO::PARAM_STR);
		}
	}
	$stmt->execute();
	$rows = $stmt->fetchAll();
	foreach ($rows as &$r) {
		$r['genres'] = json_decode($r['genres'], true);
	}
	send_json(['items' => $rows, 'count' => count($rows)]);
}

// GET /api/genres (supports ?q=)
if ($method === 'GET' && $path === '/api/genres') {
	$q = isset($_GET['q']) ? trim($_GET['q']) : '';
	if ($q !== '') {
		$stmt = $pdo->prepare('SELECT id, gName FROM genres WHERE gName ILIKE :q ORDER BY gName');
		$stmt->execute([':q' => "%{$q}%"]);
		$rows = $stmt->fetchAll();
	} else {
		$stmt = $pdo->query('SELECT id, gName FROM genres ORDER BY gName');
		$rows = $stmt->fetchAll();
	}
	send_json($rows);
}

// GET /api/developers (supports ?q=)
if ($method === 'GET' && $path === '/api/developers') {
	$q = isset($_GET['q']) ? trim($_GET['q']) : '';
	if ($q !== '') {
		$stmt = $pdo->prepare('SELECT id, dName FROM developers WHERE dName ILIKE :q ORDER BY dName');
		$stmt->execute([':q' => "%{$q}%"]);
		$rows = $stmt->fetchAll();
	} else {
		$stmt = $pdo->query('SELECT id, dName FROM developers ORDER BY dName');
		$rows = $stmt->fetchAll();
	}
	send_json($rows);
}

// GET /api/publishers (supports ?q=)
if ($method === 'GET' && $path === '/api/publishers') {
	$q = isset($_GET['q']) ? trim($_GET['q']) : '';
	if ($q !== '') {
		$stmt = $pdo->prepare('SELECT id, pName FROM publishers WHERE pName ILIKE :q ORDER BY pName');
		$stmt->execute([':q' => "%{$q}%"]);
		$rows = $stmt->fetchAll();
	} else {
		$stmt = $pdo->query('SELECT id, pName FROM publishers ORDER BY pName');
		$rows = $stmt->fetchAll();
	}
	send_json($rows);
}

// GET /api/publishers/{id}/games
if ($method === 'GET' && preg_match('#^/api/publishers/(\d+)/games$#', $path, $m)) {
	$pubId = (int)$m[1];
	$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
	if ($limit < 1) $limit = 20;
	$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
	$q = isset($_GET['q']) ? trim($_GET['q']) : '';

	if ($q !== '') {
		$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
FROM games g
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg_all ON gg_all.game_id = g.id
LEFT JOIN genres gen ON gen.id = gg_all.genre_id
WHERE g.publisher_id = :pubId AND g.gName ILIKE :q
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;
		$params = [':limit' => $limit, ':offset' => $offset, ':q' => "%{$q}%"];
	} else {
		$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
FROM games g
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg_all ON gg_all.game_id = g.id
LEFT JOIN genres gen ON gen.id = gg_all.genre_id
WHERE g.publisher_id = :pubId
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;
		$params = [':limit' => $limit, ':offset' => $offset];
	}

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':pubId', $pubId, PDO::PARAM_INT);
	foreach ($params as $k => $v) {
		if ($k === ':limit' || $k === ':offset') {
			$stmt->bindValue($k, $v, PDO::PARAM_INT);
		} else {
			$stmt->bindValue($k, $v, PDO::PARAM_STR);
		}
	}
	$stmt->execute();
	$rows = $stmt->fetchAll();
	foreach ($rows as &$r) {
		$r['genres'] = json_decode($r['genres'], true);
	}
	send_json(['items' => $rows, 'count' => count($rows)]);
}

// GET /api/developers/{id}/games
if ($method === 'GET' && preg_match('#^/api/developers/(\d+)/games$#', $path, $m)) {
	$devId = (int)$m[1];
	$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
	if ($limit < 1) $limit = 20;
	$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
	$q = isset($_GET['q']) ? trim($_GET['q']) : '';

	if ($q !== '') {
		$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
FROM games g
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg_all ON gg_all.game_id = g.id
LEFT JOIN genres gen ON gen.id = gg_all.genre_id
WHERE g.developer_id = :devId AND g.gName ILIKE :q
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;
		$params = [':limit' => $limit, ':offset' => $offset, ':q' => "%{$q}%"];
	} else {
		$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
FROM games g
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg_all ON gg_all.game_id = g.id
LEFT JOIN genres gen ON gen.id = gg_all.genre_id
WHERE g.developer_id = :devId
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;
		$params = [':limit' => $limit, ':offset' => $offset];
	}

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':devId', $devId, PDO::PARAM_INT);
	foreach ($params as $k => $v) {
		if ($k === ':limit' || $k === ':offset') {
			$stmt->bindValue($k, $v, PDO::PARAM_INT);
		} else {
			$stmt->bindValue($k, $v, PDO::PARAM_STR);
		}
	}
	$stmt->execute();
	$rows = $stmt->fetchAll();
	foreach ($rows as &$r) {
		$r['genres'] = json_decode($r['genres'], true);
	}
	send_json(['items' => $rows, 'count' => count($rows)]);
}

// GET /api/genres/{id}/games
if ($method === 'GET' && preg_match('#^/api/genres/(\d+)/games$#', $path, $m)) {
	$genreId = (int)$m[1];
	$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
	if ($limit < 1) $limit = 20;
	$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
	$q = isset($_GET['q']) ? trim($_GET['q']) : '';

	if ($q !== '') {
		$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
FROM games g
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg_all ON gg_all.game_id = g.id
LEFT JOIN genres gen ON gen.id = gg_all.genre_id
WHERE EXISTS (SELECT 1 FROM games_genres gg WHERE gg.game_id = g.id AND gg.genre_id = :genreId)
  AND g.gName ILIKE :q
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;
		$params = [':limit' => $limit, ':offset' => $offset, ':q' => "%{$q}%"];
	} else {
		$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
FROM games g
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg_all ON gg_all.game_id = g.id
LEFT JOIN genres gen ON gen.id = gg_all.genre_id
WHERE EXISTS (SELECT 1 FROM games_genres gg WHERE gg.game_id = g.id AND gg.genre_id = :genreId)
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;
		$params = [':limit' => $limit, ':offset' => $offset];
	}

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':genreId', $genreId, PDO::PARAM_INT);
	foreach ($params as $k => $v) {
		if ($k === ':limit' || $k === ':offset') {
			$stmt->bindValue($k, $v, PDO::PARAM_INT);
		} else {
			$stmt->bindValue($k, $v, PDO::PARAM_STR);
		}
	}
	$stmt->execute();
	$rows = $stmt->fetchAll();
	foreach ($rows as &$r) {
		$r['genres'] = json_decode($r['genres'], true);
	}
	send_json(['items' => $rows, 'count' => count($rows)]);
}

// GET /api/publishers/{id}/games
if ($method === 'GET' && preg_match('#^/api/publishers/(\d+)/games$#', $path, $m)) {
	$pub_id = (int)$m[1];
	$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
	if ($limit < 1) $limit = 100;
	$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

	$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   g.annotation, g.system_requirements,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
FROM games g
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg ON gg.game_id = g.id
LEFT JOIN genres gen ON gen.id = gg.genre_id
WHERE p.id = :id
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':id', $pub_id, PDO::PARAM_INT);
	$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
	$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
	$stmt->execute();
	$rows = $stmt->fetchAll();
	foreach ($rows as &$r) {
		$r['genres'] = json_decode($r['genres'], true);
	}
	send_json(['items' => $rows, 'count' => count($rows)]);
}

// GET /api/developers/{id}/games
if ($method === 'GET' && preg_match('#^/api/developers/(\d+)/games$#', $path, $m)) {
	$dev_id = (int)$m[1];
	$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
	if ($limit < 1) $limit = 100;
	$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

	$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   g.annotation, g.system_requirements,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen.id, 'gName', gen.gName)) FILTER (WHERE gen.id IS NOT NULL), '[]') AS genres
FROM games g
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg ON gg.game_id = g.id
LEFT JOIN genres gen ON gen.id = gg.genre_id
WHERE d.id = :id
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':id', $dev_id, PDO::PARAM_INT);
	$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
	$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
	$stmt->execute();
	$rows = $stmt->fetchAll();
	foreach ($rows as &$r) {
		$r['genres'] = json_decode($r['genres'], true);
	}
	send_json(['items' => $rows, 'count' => count($rows)]);
}

// GET /api/genres/{id}/games
if ($method === 'GET' && preg_match('#^/api/genres/(\d+)/games$#', $path, $m)) {
	$genre_id = (int)$m[1];
	$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
	if ($limit < 1) $limit = 100;
	$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

	$sql = <<<SQL
SELECT g.id, g.gName, g.cover_url, g.rating, g.release_date, g.likes, g.price,
	   g.annotation, g.system_requirements,
	   d.dName AS developer, p.pName AS publisher,
	   COALESCE(json_agg(json_build_object('id', gen_all.id, 'gName', gen_all.gName)) FILTER (WHERE gen_all.id IS NOT NULL), '[]') AS genres
FROM games g
JOIN games_genres gg_filter ON gg_filter.game_id = g.id AND gg_filter.genre_id = :genre_id
LEFT JOIN developers d ON g.developer_id = d.id
LEFT JOIN publishers p ON g.publisher_id = p.id
LEFT JOIN games_genres gg ON gg.game_id = g.id
LEFT JOIN genres gen_all ON gen_all.id = gg.genre_id
GROUP BY g.id, d.dName, p.pName
ORDER BY g.id
LIMIT :limit OFFSET :offset
SQL;

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':genre_id', $genre_id, PDO::PARAM_INT);
	$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
	$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
	$stmt->execute();
	$rows = $stmt->fetchAll();
	foreach ($rows as &$r) {
		$r['genres'] = json_decode($r['genres'], true);
	}
	send_json(['items' => $rows, 'count' => count($rows)]);
}

send_json(['error' => 'Not found'], 404);

?>