<?php
include 'config.php';
login_required();
$title = "My teams";

$q = $pdo->prepare("SELECT t.*, s.title FROM teams t
                    JOIN team_members m ON m.team_id = t.id
                    JOIN startups s ON s.id = t.startup_id
                    WHERE m.user_id = ? ORDER BY t.id DESC");
$q->execute([$me['id']]);
$teams = $q->fetchAll();
include 'header.php';
?>
<h2>My teams</h2>
<?php if (count($teams) == 0) echo '<div class="box">You are not in any team yet. Team is created when a join request is accepted.</div>'; ?>
<?php foreach ($teams as $t): ?>
  <div class="box">
    <h3><?php echo e($t['team_name']); ?></h3>
    <p>Idea: <a href="view_post.php?id=<?php echo $t['startup_id']; ?>"><?php echo e($t['title']); ?></a> | Started: <?php echo $t['start_date']; ?></p>
    <a class="btn btn-small" href="team.php?id=<?php echo $t['id']; ?>">Open group chat</a>
  </div>
<?php endforeach; ?>
<?php include 'footer.php'; ?>
