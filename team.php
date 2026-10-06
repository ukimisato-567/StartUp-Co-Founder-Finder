<?php
include 'config.php';
login_required();

$id = (int) ($_GET['id'] ?? 0);

// only team members can open the team page
$q = $pdo->prepare("SELECT t.* FROM teams t JOIN team_members m ON m.team_id = t.id WHERE t.id = ? AND m.user_id = ?");
$q->execute([$id, $me['id']]);
$team = $q->fetch();
if (!$team) {
    flash("You are not a member of that team.");
    header("Location: teams.php");
    exit;
}

$q = $pdo->prepare("SELECT u.name, u.uid, m.role_in_team FROM team_members m JOIN users u ON u.id = m.user_id WHERE m.team_id = ?");
$q->execute([$id]);
$members = $q->fetchAll();

$title = $team['team_name'];
include 'header.php';
?>
<h2><?php echo e($team['team_name']); ?></h2>
<div class="cols">
  <div style="flex:3">
    <div class="chat">
      <div id="messages"><p id="empty">No messages yet.</p></div>
      <form id="chatForm">
        <input type="text" id="text" placeholder="Type a message..." autocomplete="off">
        <button class="btn" type="submit">Send</button>
      </form>
    </div>
  </div>
  <div style="flex:1">
    <div class="box">
      <h3>Members</h3>
      <?php foreach ($members as $m): ?>
        <p class="person"><span><b><?php echo e($m['name']); ?></b><br><small><?php echo e($m['uid']); ?> - <?php echo e($m['role_in_team']); ?></small></span></p>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>var TEAM_ID = <?php echo $id; ?>;</script>
<script src="js/chat.js"></script>
<?php include 'footer.php'; ?>
