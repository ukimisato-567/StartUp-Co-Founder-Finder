<?php
include '../config.php';
if (!$me) { echo json_encode([]); exit; }

$team_id = (int) $_GET['team_id'];
$last_id = (int) $_GET['last_id'];

// must be a team member
$c = $pdo->prepare("SELECT id FROM team_members WHERE team_id = ? AND user_id = ?");
$c->execute([$team_id, $me['id']]);
if (!$c->fetch()) { echo json_encode([]); exit; }

$q = $pdo->prepare("SELECT m.id, m.user_id, m.message, m.sent_at, u.name FROM messages m
                    JOIN users u ON u.id = m.user_id WHERE m.team_id = ? AND m.id > ? ORDER BY m.id");
$q->execute([$team_id, $last_id]);
$rows = $q->fetchAll();

$result = [];
foreach ($rows as $r) {
    $result[] = [
        'id' => (int) $r['id'],
        'name' => $r['name'],
        'message' => $r['message'],
        'time' => date('h:i A', strtotime($r['sent_at'])),
        'mine' => ($r['user_id'] == $me['id'])
    ];
}
echo json_encode($result);
