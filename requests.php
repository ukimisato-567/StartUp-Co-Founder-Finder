<?php
include 'config.php';
login_required();
if ($me['role'] != 'user') { header("Location: home.php"); exit; }
$title = "Join requests";
include 'header.php';
?>
<h2>Requests I received</h2>
<div id="received">Loading...</div>

<h2 style="margin-top:30px">Requests I sent</h2>
<div id="sent">Loading...</div>

<script src="js/requests.js"></script>
<?php include 'footer.php'; ?>
