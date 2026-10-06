<?php
include 'config.php';
login_required();
if ($me['status'] == 'active') { header("Location: home.php"); exit; }
$title = "Account suspended";
include 'header.php';
?>
<div class="box small-box">
  <h2>Your account is suspended</h2>
  <p>The admin has suspended your account. You can log in, but you cannot post, send join requests or use team chat until the admin activates your account again.</p>
  <a class="btn btn-grey" href="logout.php">Logout</a>
</div>
<?php include 'footer.php'; ?>
