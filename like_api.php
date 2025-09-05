<?php
// like_api.php

session_start();

$db = new SQLite3('likes.db');
$db->exec("CREATE TABLE IF NOT EXISTS likes (url TEXT PRIMARY KEY, count INTEGER DEFAULT 0)");
$db->exec("CREATE TABLE IF NOT EXISTS like_users (
    url TEXT,
    ip TEXT,
    session TEXT,
    cookie TEXT,
    PRIMARY KEY (url, ip, session, cookie)
)");

function getRealUserIp() {
	switch(true){
		case (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) : return $_SERVER['HTTP_CF_CONNECTING_IP'];
		case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
		case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
		case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
		default : return $_SERVER['REMOTE_ADDR'];
	}
}

$url = $_GET['url'] ?? '';
$action = $_GET['action'] ?? '';
$ip = getRealUserIp();
$session = session_id();
$cookie_name = 'like_' . md5($url);
if (!isset($_COOKIE[$cookie_name])) {
    $cookie = bin2hex(random_bytes(16));
    setcookie($cookie_name, $cookie, time() + 365*24*60*60, "/");
} else {
    $cookie = $_COOKIE[$cookie_name];
}

if (!$url) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing URL']);
    exit;
}


// Check if user already liked
$stmt = $db->prepare("SELECT 1 FROM like_users WHERE url = :url AND (ip = :ip OR session = :session OR cookie = :cookie)");
$stmt->bindValue(':url', $url, SQLITE3_TEXT);
$stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
$stmt->bindValue(':session', $session, SQLITE3_TEXT);
$stmt->bindValue(':cookie', $cookie, SQLITE3_TEXT);
$result = $stmt->execute();
$alreadyLiked = $result->fetchArray() ? true : false;

if ($action === 'like' && !$alreadyLiked) {
    // Not liked yet, increment
    $stmt = $db->prepare("INSERT INTO likes (url, count) VALUES (:url, 1)
        ON CONFLICT(url) DO UPDATE SET count = count + 1");
    $stmt->bindValue(':url', $url, SQLITE3_TEXT);
    $stmt->execute();

    $stmt = $db->prepare("INSERT INTO like_users (url, ip, session, cookie) VALUES (:url, :ip, :session, :cookie)");
    $stmt->bindValue(':url', $url, SQLITE3_TEXT);
    $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
    $stmt->bindValue(':session', $session, SQLITE3_TEXT);
    $stmt->bindValue(':cookie', $cookie, SQLITE3_TEXT);
    $stmt->execute();
    $alreadyLiked = true;
}


$stmt = $db->prepare("SELECT count FROM likes WHERE url = :url");
$stmt->bindValue(':url', $url, SQLITE3_TEXT);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);
$count = $row ? $row['count'] : 0;
header('Content-Type: application/json');
echo json_encode(['count' => $count, 'liked' => $alreadyLiked]);
?>