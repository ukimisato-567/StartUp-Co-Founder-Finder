<?php
// returns the html for the requests lists (called every few seconds by js/requests.js)
include '../config.php';
if (!$me) exit;
$root = '';   // this html is shown inside pages in the main folder, so links and pictures must not start with ../

if ($_GET['type'] == 'received') {
    $q = $pdo->prepare("SELECT r.*, s.title, u.name, u.uid FROM join_requests r
                        JOIN startups s ON s.id = r.startup_id
                        JOIN users u ON u.id = r.user_id
                        WHERE s.user_id = ? ORDER BY r.status = 'pending' DESC, r.id DESC");
    $q->execute([$me['id']]);
    $rows = $q->fetchAll();
    if (count($rows) == 0) echo '<div class="box">No requests received yet.</div>';
    foreach ($rows as $r) {
        echo '<div class="req"><div class="person"><div>';
        echo '<b>' . e($r['name']) . '</b> (' . e($r['uid']) . ') wants to join <b>' . e($r['title']) . '</b>';
        if ($r['message'] != '') echo '<br><small>"' . e($r['message']) . '"</small>';
        echo '</div></div><div>';
        if ($r['status'] == 'pending') {
            echo '<button class="btn btn-small req-btn" data-id="' . $r['id'] . '" data-action="accept">Accept</button> ';
            echo '<button class="btn btn-red btn-small req-btn" data-id="' . $r['id'] . '" data-action="reject">Reject</button>';
        } else {
            echo '<span class="status">' . $r['status'] . '</span>';
        }
        echo '</div></div>';
    }
} else {
    $q = $pdo->prepare("SELECT r.*, s.title, u.name FROM join_requests r
                        JOIN startups s ON s.id = r.startup_id
                        JOIN users u ON u.id = s.user_id
                        WHERE r.user_id = ? ORDER BY r.id DESC");
    $q->execute([$me['id']]);
    $rows = $q->fetchAll();
    if (count($rows) == 0) echo '<div class="box">You have not sent any request.</div>';
    foreach ($rows as $r) {
        echo '<div class="req"><div>';
        echo 'You applied to <a href="view_post.php?id=' . $r['startup_id'] . '">' . e($r['title']) . '</a> by ' . e($r['name']);
        echo '</div><div><span class="status">' . $r['status'] . '</span></div></div>';
    }
}
