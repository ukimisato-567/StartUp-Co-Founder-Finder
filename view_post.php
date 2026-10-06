<?php
include 'config.php';
login_required();

$id = (int) ($_GET['id'] ?? 0);
$q = $pdo->prepare("SELECT s.*, u.name, u.uid FROM startups s JOIN users u ON u.id = s.user_id WHERE s.id = ?");
$q->execute([$id]);
$post = $q->fetch();
if (!$post) {
    flash("Post not found.");
    header("Location: home.php");
    exit;
}
$mine = ($post['user_id'] == $me['id']);
$isUser = ($me['role'] == 'user');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // apply to join
    if (isset($_POST['apply']) && !$mine && $isUser && $post['status'] == 'open') {
        $c = $pdo->prepare("SELECT id FROM join_requests WHERE startup_id = ? AND user_id = ?");
        $c->execute([$id, $me['id']]);
        if ($c->fetch()) {
            flash("You have already sent a request for this idea.");
        } else {
            $pdo->prepare("INSERT INTO join_requests (startup_id, user_id, message) VALUES (?, ?, ?)")
                ->execute([$id, $me['id'], trim($_POST['message'])]);
            flash("Join request sent.");
        }
    }
    // open / close the team
    if (isset($_POST['toggle']) && $mine) {
        $new = ($post['status'] == 'open') ? 'closed' : 'open';
        $pdo->prepare("UPDATE startups SET status = ? WHERE id = ?")->execute([$new, $id]);
    }
    // delete post (owner or admin)
    if (isset($_POST['delete']) && ($mine || $me['role'] == 'admin')) {
        if ($post['image'] != '') @unlink("uploads/" . $post['image']);
        $pdo->prepare("DELETE FROM startups WHERE id = ?")->execute([$id]);
        flash("Post deleted.");
        header("Location: home.php");
        exit;
    }
    header("Location: view_post.php?id=" . $id);
    exit;
}

// my request status for this post
$c = $pdo->prepare("SELECT status FROM join_requests WHERE startup_id = ? AND user_id = ?");
$c->execute([$id, $me['id']]);
$myRequest = $c->fetch();

$t = $pdo->prepare("SELECT t.id FROM teams t JOIN team_members m ON m.team_id = t.id WHERE t.startup_id = ? AND m.user_id = ?");
$t->execute([$id, $me['id']]);
$myTeam = $t->fetch();

$title = $post['title'];
include 'header.php';
?>
<div class="post">
  <h2><?php echo e($post['title']); ?></h2>
  <div class="info">
    by <b><?php echo e($post['name']); ?></b> (<?php echo e($post['uid']); ?>)
    | <?php echo date('d M Y', strtotime($post['created_at'])); ?> | <?php echo e($post['category']); ?>
    | <span class="status <?php echo $post['status']; ?>"><?php echo $post['status']; ?></span>
  </div>
  <?php if ($post['image']): ?>
    <img class="post-img" src="uploads/<?php echo e($post['image']); ?>" alt="post photo">
  <?php endif; ?>
  <p><?php echo nl2br(e($post['description'])); ?></p>
  <p><b>Skills needed:</b> <?php echo skill_tags($post['skills_needed']); ?></p>

  <?php if ($mine || $me['role'] == 'admin'): ?>
  <form method="post" style="display:inline" onsubmit="return confirm('Delete this post?');">
    <button class="btn btn-red btn-small" name="delete" value="1">Delete post</button>
  </form>
  <?php endif; ?>
  <?php if ($mine): ?>
  <form method="post" style="display:inline">
    <button class="btn btn-grey btn-small" name="toggle" value="1"><?php echo $post['status'] == 'open' ? 'Close team' : 'Reopen team'; ?></button>
  </form>
  <a class="btn btn-small" href="requests.php">See join requests</a>
  <?php endif; ?>
  <a class="btn btn-grey btn-small" href="home.php">Back to feed</a>
</div>

<?php if ($myTeam): ?>
  <div class="box">You are in this team. <a class="btn btn-small" href="team.php?id=<?php echo $myTeam['id']; ?>">Open team chat</a></div>
<?php elseif (!$mine && $isUser): ?>
  <div class="box">
  <?php if ($myRequest): ?>
    Your join request is <b><?php echo $myRequest['status']; ?></b>.
  <?php elseif ($post['status'] != 'open'): ?>
    This team is closed.
  <?php else: ?>
    <h3>Apply to join this team</h3>
    <form method="post">
      <label>Message (optional)</label>
      <input type="text" name="message" placeholder="Tell them why you fit"><br><br>
      <button class="btn" name="apply" value="1">Send join request</button>
    </form>
  <?php endif; ?>
  </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
