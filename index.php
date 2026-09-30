<?php
include 'config.php';
if ($me) { header("Location: home.php"); exit; }

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $login = trim($_POST['login']);
    $q = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $q->execute([$login, $login]);

    foreach ($q->fetchAll() as $user) {
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.php");
            exit;
        }
    }
    $error = "Wrong email/mobile number or password.";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Startup Co-Founder Finder</title>
<link rel="stylesheet" href="css/style.css?v=<?php echo filemtime(__DIR__ . '/css/style.css'); ?>">
</head>
<body class="lp-page">

<div class="lp">
  <div class="lp-left">
    <div class="lp-logo">CF</div>
    <h1>Find your<br><span class="blue">co-founder.</span></h1>
    <p class="lp-sub">Post your startup idea, list the skills you need, and team up with students who complete you.</p>
    <img class="lp-hero" src="images/hero.png" alt="Two people teaming up like puzzle pieces">
  </div>

  <div class="lp-right">
    <form class="lp-form" method="post">
      <h2>Log in to Co-Founder Finder</h2>
      <?php if ($error) echo '<div class="msg error">' . e($error) . '</div>'; ?>
      <input type="text" name="login" placeholder="Email address or mobile number" value="<?php echo e($_POST['login'] ?? ''); ?>" required>
      <input type="password" name="password" placeholder="Password" required>
      <button class="lp-btn" type="submit">Log in</button>

      <a class="lp-forgot" href="#" onclick="alert('Please contact the admin to reset your password.'); return false;">Forgot password?</a>
      <hr class="lp-divider">
      <a class="lp-create" href="register.php">Create new profile</a>
    </form>
  </div>
</div>

</body>
</html>
