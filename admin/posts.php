<?php
include '../config.php';
admin_required();
$title = "Manage posts";

// delete a post that breaks the rules
if (isset($_POST['delete'])) {
    $id = (int) $_POST['post_id'];
    $q = $pdo->prepare("SELECT image FROM startups WHERE id = ?");
    $q->execute([$id]);
    $p = $q->fetch();
    if ($p && $p['image'] != '') @unlink("../uploads/" . $p['image']);
    $pdo->prepare("DELETE FROM startups WHERE id = ?")->execute([$id]);
    flash("Post deleted.");
    header("Location: posts.php");
    exit;
}

$posts = $pdo->query("SELECT s.*, u.name, u.uid FROM startups s JOIN users u ON u.id = s.user_id ORDER BY s.id DESC")->fetchAll();
include '../header.php';
?>
<h2>Manage posts</h2>
<table>
  <tr><th>Title</th><th>Posted by</th><th>Date</th><th>Action</th></tr>
  <?php foreach ($posts as $p): ?>
  <tr>
    <td><a href="../view_post.php?id=<?php echo $p['id']; ?>"><?php echo e($p['title']); ?></a></td>
    <td><?php echo e($p['name']); ?> (<?php echo e($p['uid']); ?>)</td>
    <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
    <td>
      <form method="post" style="margin:0" onsubmit="return confirm('Delete this post?');">
        <input type="hidden" name="post_id" value="<?php echo $p['id']; ?>">
        <button class="btn btn-red btn-small" name="delete" value="1">Delete</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (count($posts) == 0) echo '<tr><td colspan="4">No posts.</td></tr>'; ?>
</table>
<?php include '../footer.php'; ?>
