<?php
include '../config.php';
if (!$me) { echo json_encode(['error' => 'Please login.']); exit; }

$id = (int) $_POST['id'];
$action = $_POST['action'];

// the request must belong to one of MY posts
$q = $pdo->prepare("SELECT r.*, s.title, s.user_id AS owner_id FROM join_requests r
                    JOIN startups s ON s.id = r.startup_id WHERE r.id = ?");
$q->execute([$id]);
$r = $q->fetch();

if (!$r || $r['owner_id'] != $me['id'] || $r['status'] != 'pending') {
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}

if ($action == 'reject') {
    $pdo->prepare("UPDATE join_requests SET status = 'rejected' WHERE id = ?")->execute([$id]);
    echo json_encode(['ok' => true]);
    exit;
}

// accept: make the team (first time only) and add the member
$pdo->prepare("UPDATE join_requests SET status = 'accepted' WHERE id = ?")->execute([$id]);

$t = $pdo->prepare("SELECT id FROM teams WHERE startup_id = ?");
$t->execute([$r['startup_id']]);
$team = $t->fetch();

if ($team) {
    $teamId = $team['id'];
} else {
    $pdo->prepare("INSERT INTO teams (startup_id, team_name, goal, start_date) VALUES (?, ?, ?, CURDATE())")
        ->execute([$r['startup_id'], $r['title'] . " Team", "Build " . $r['title']]);
    $teamId = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO team_members (team_id, user_id, role_in_team) VALUES (?, ?, 'Founder')")
        ->execute([$teamId, $me['id']]);
}
$pdo->prepare("INSERT IGNORE INTO team_members (team_id, user_id, role_in_team) VALUES (?, ?, 'Member')")
    ->execute([$teamId, $r['user_id']]);

echo json_encode(['ok' => true]);
