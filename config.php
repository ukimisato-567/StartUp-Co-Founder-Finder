<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=scf_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    die("Database connection failed. Start MySQL in XAMPP and import database.sql first.");
}

$root = '';                                
$page = basename($_SERVER['SCRIPT_NAME']);


$me = null;
if (isset($_SESSION['user_id'])) {
    $q = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $q->execute([$_SESSION['user_id']]);
    $me = $q->fetch();

    if (!$me) {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}


function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES);
}

function login_required() {
    global $me;
    if (!$me) {
        header("Location: index.php");
        exit;
    }
}


function flash($text) {
    $_SESSION['flash'] = $text;
}


function skill_tags($skills) {
    $out = '';
    foreach (explode(',', $skills ?? '') as $s) {
        $s = trim($s);
        if ($s != '') {
            $out .= '<a class="tag" href="home.php?skill=' . urlencode($s) . '">' . e($s) . '</a> ';
        }
    }
    return $out;
}

require_once __DIR__ . '/skills.php';
