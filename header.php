<?php $pending = pending_requests(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($title ?? 'Startup Co-Founder Finder'); ?></title>
<link rel="stylesheet" href="<?php echo $root; ?>css/style.css">
</head>
<body class="site">

<div class="navbar">
  <div class="wrap">
    <a class="logo" href="<?php echo $root; ?>index.php">Co-Founder Finder</a>
    <div class="links">
    <?php if ($me && $me['status'] == 'suspended'): ?>
      <a href="<?php echo $root; ?>logout.php">Logout</a>
    <?php elseif ($me && $me['role'] == 'admin'): ?>
      <a href="<?php echo $root; ?>admin/index.php">Dashboard</a>
      <a href="<?php echo $root; ?>admin/users.php">Users</a>
      <a href="<?php echo $root; ?>admin/posts.php">Posts</a>
      <a href="<?php echo $root; ?>home.php">Feed</a>
      <a href="<?php echo $root; ?>logout.php">Logout</a>
    <?php elseif ($me): ?>
      <a href="<?php echo $root; ?>home.php">Home</a>
      <a href="<?php echo $root; ?>add_post.php">Post an idea</a>
      <a href="<?php echo $root; ?>requests.php">Requests
        <span class="badge" id="reqCount" <?php if ($pending == 0) echo 'style="display:none"'; ?>><?php echo $pending; ?></span></a>
      <a href="<?php echo $root; ?>teams.php">My Teams</a>
      <a href="<?php echo $root; ?>logout.php">Logout (<?php echo e($me['name']); ?>)</a>
    <?php else: ?>
      <a href="<?php echo $root; ?>index.php">Login</a>
      <a href="<?php echo $root; ?>register.php">Register</a>
    <?php endif; ?>
    </div>
  </div>
</div>

<div class="wrap main">
<?php
if (isset($_SESSION['flash'])) {
    echo '<div class="msg">' . e($_SESSION['flash']) . '</div>';
    unset($_SESSION['flash']);
}
?>
