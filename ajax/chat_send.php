<?php
include '../config.php';
if (!$me) { echo json_encode(['error' => 'Please login.']); exit; }

$team_id = (int) $_POST['team_id'];
$text = trim($_POST['message']);

$c = $pdo->prepare("SELECT id FROM team_members WHERE team_id = ? AND user_id = ?");
$c->execute([$team_id, $me['id']]);
if (!$c->fetch() || $text == '') {
    echo json_encode(['error' => 'Cannot send message.']);
    exit;
}

$pdo->prepare("INSERT INTO messages (team_id, user_id, message) VALUES (?, ?, ?)")
    ->execute([$team_id, $me['id'], $text]);
echo json_encode(['ok' => true]);
