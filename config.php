<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=scf_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    die("Database connection failed. Start MySQL in XAMPP and import database.sql first.");
}

// pages inside admin/ and ajax/ need "../" before links
$folder = basename(dirname($_SERVER['SCRIPT_FILENAME']));
$root = ($folder == 'admin' || $folder == 'ajax') ? '../' : '';
$page = basename($_SERVER['SCRIPT_NAME']);

// find out who is logged in (checked from the database on every request)
$me = null;
if (isset($_SESSION['user_id'])) {
    $q = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $q->execute([$_SESSION['user_id']]);
    $me = $q->fetch();

    if (!$me) {
        // admin deleted this account
        session_destroy();
        header("Location: " . $root . "index.php");
        exit;
    }

    // suspended users can log in, but nothing else
    if ($me['status'] == 'suspended' && $page != 'suspended.php' && $page != 'logout.php') {
        if ($folder == 'ajax') {
            http_response_code(403);
            echo json_encode(['error' => 'Your account is suspended.']);
        } else {
            header("Location: " . $root . "suspended.php");
        }
        exit;
    }
}

// safe output (stops HTML/script injection)
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES);
}

function login_required() {
    global $me, $root;
    if (!$me) {
        header("Location: " . $root . "index.php");
        exit;
    }
}

function admin_required() {
    global $me, $root;
    if (!$me || $me['role'] != 'admin') {
        header("Location: " . $root . "index.php");
        exit;
    }
}

// one-time message shown at the top of the next page
function flash($text) {
    $_SESSION['flash'] = $text;
}

// "PHP, Design" -> little tags. Each tag is a link that searches posts by that skill.
function skill_tags($skills) {
    global $root;
    $out = '';
    foreach (explode(',', $skills ?? '') as $s) {
        $s = trim($s);
        if ($s != '') {
            $out .= '<a class="tag" href="' . $root . 'home.php?skill=' . urlencode($s) . '">' . e($s) . '</a> ';
        }
    }
    return $out;
}

// number shown on the "Requests" menu: pending join requests for MY ideas
function pending_requests() {
    global $pdo, $me;
    if (!$me || $me['role'] != 'user' || $me['status'] != 'active') return 0;
    $q = $pdo->prepare("SELECT COUNT(*) FROM join_requests r JOIN startups s ON s.id = r.startup_id
                        WHERE s.user_id = ? AND r.status = 'pending'");
    $q->execute([$me['id']]);
    return (int) $q->fetchColumn();
}

require_once __DIR__ . '/skills.php';
