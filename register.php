<?php
include 'config.php';
if ($me) { header("Location: home.php"); exit; }
$error = "";
$selected = $_POST['skills'] ?? [];
if (!is_array($selected)) $selected = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    $pass = $_POST['password'];
    $skillList = collect_skills('skills', 'skills_other');

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($name == '' || $email == '' || $pass == '') {
        $error = "Please fill name, email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email is not valid.";
    } elseif (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($pass != $_POST['confirm']) {
        $error = "Passwords do not match.";
    } elseif ($check->fetch()) {
        $error = "This email is already registered.";
    } elseif (skills_error($skillList) != '') {
        $error = skills_error($skillList);
    } else {
        $q = $pdo->prepare("INSERT INTO users (name, email, phone, password, skills, bio) VALUES (?, ?, ?, ?, ?, ?)");
        $q->execute([$name, $email, $phone, password_hash($pass, PASSWORD_DEFAULT), skills_to_text($skillList), $bio]);

        // give the user a unique id after registration
        $id = $pdo->lastInsertId();
        $uid = "SCF" . (1000 + $id);
        $pdo->prepare("UPDATE users SET uid = ? WHERE id = ?")->execute([$uid, $id]);

        $_SESSION['user_id'] = $id;
        flash("Registration successful. Your user ID is " . $uid);
        header("Location: home.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register - Startup Co-Founder Finder</title>
<link rel="stylesheet" href="css/style.css?v=<?php echo filemtime(__DIR__ . '/css/style.css'); ?>">
</head>
<body class="lp-page">

<div class="lp">
  <div class="lp-left">
    <a class="lp-logo" href="index.php">CF</a>
    <h1>Build your<br><span class="blue">dream team.</span></h1>
    <p class="lp-sub">Tell us what you are good at. Pick your skills once and the right startup ideas will be easy to find.</p>
    <img class="lp-hero" src="images/hero.png" alt="Two people teaming up like puzzle pieces">
  </div>

  <div class="lp-right reg-right">
    <form class="reg-form" method="post">
      <h2>Create your profile</h2>
      <p class="reg-note">It is free and takes less than a minute.</p>
      <?php if ($error) echo '<div class="msg error">' . e($error) . '</div>'; ?>

      <div class="reg-grid">
        <div>
          <label for="name">Full name</label>
          <input type="text" id="name" name="name" placeholder="Your full name" value="<?php echo e($_POST['name'] ?? ''); ?>" required>
        </div>
        <div>
          <label for="phone">Mobile number</label>
          <input type="text" id="phone" name="phone" placeholder="Optional" value="<?php echo e($_POST['phone'] ?? ''); ?>">
        </div>
        <div class="span2">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
        </div>
        <div>
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
        </div>
        <div>
          <label for="confirm">Confirm password</label>
          <input type="password" id="confirm" name="confirm" placeholder="Type it again" required>
        </div>
      </div>

      <label>Your skills <small>(tick everything you can do)</small></label>
      <?php skill_picker('skills', $selected, $_POST['skills_other'] ?? ''); ?>

      <label for="bio">Short bio <small>(optional)</small></label>
      <textarea id="bio" name="bio" rows="2" placeholder="Tell others a little about yourself"><?php echo e($_POST['bio'] ?? ''); ?></textarea>

      <div class="reg-actions">
        <button class="lp-btn" type="submit">Create account</button>
        <p class="reg-login">Already have an account? <a href="index.php">Log in</a></p>
      </div>
    </form>
  </div>
</div>

<script src="js/skills.js"></script>
</body>
</html>
