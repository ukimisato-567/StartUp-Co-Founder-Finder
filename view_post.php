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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']) && ($mine || $me['role'] == 'admin')) {
    if ($post['image'] != '') @unlink("uploads/" . $post['image']);
    $pdo->prepare("DELETE FROM startups WHERE id = ?")->execute([$id]);
    flash("Post deleted.");
    header("Location: home.php");
    exit;
}

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
  <a class="btn btn-grey btn-small" href="home.php">Back to feed</a>
</div>

<?php include 'footer.php'; ?>
