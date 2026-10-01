<?php
include '../config.php';
admin_required();
$title = "Admin dashboard";

$users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$suspended = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'suspended'")->fetchColumn();
$posts = $pdo->query("SELECT COUNT(*) FROM startups")->fetchColumn();
$teams = $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn();
include '../header.php';
?>
<h2>Admin dashboard</h2>
<div class="cols">
  <div class="box"><div class="count"><?php echo $users; ?></div>Registered users</div>
  <div class="box"><div class="count"><?php echo $suspended; ?></div>Suspended users</div>
  <div class="box"><div class="count"><?php echo $posts; ?></div>Posts</div>
  <div class="box"><div class="count"><?php echo $teams; ?></div>Teams</div>
</div>
<a class="btn" href="users.php">Manage users</a>
<a class="btn" href="posts.php">Manage posts</a>
<?php include '../footer.php'; ?>
