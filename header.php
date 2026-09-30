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
    <a class="logo" href="index.php">Co-Founder Finder</a>
    <div class="links">
    <?php if ($me): ?>
      <a href="home.php">Home</a>
      <a href="add_post.php">Post an idea</a>
      <a href="logout.php">Logout (<?php echo e($me['name']); ?>)</a>
    <?php else: ?>
      <a href="index.php">Login</a>
      <a href="register.php">Register</a>
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
