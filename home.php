<?php
include 'config.php';
login_required();
$title = "Home";

$keyword = trim($_GET['keyword'] ?? '');
$skill = trim($_GET['skill'] ?? '');

$sql = "SELECT s.*, u.name, u.uid FROM startups s JOIN users u ON u.id = s.user_id WHERE 1=1";
$params = [];
if ($keyword != '') {
    $sql .= " AND (s.title LIKE ? OR s.description LIKE ? OR s.skills_needed LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
if ($skill != '') {
  
    $sql .= " AND FIND_IN_SET(?, REPLACE(s.skills_needed, ', ', ','))";
    $params[] = $skill;
}
$sql .= " ORDER BY s.id DESC";
$q = $pdo->prepare($sql);
$q->execute($params);
$posts = $q->fetchAll();

include 'header.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px">
  <h2 style="margin:0">Startup ideas</h2>
  <?php if ($me['role'] == 'user'): ?><a class="btn" href="add_post.php">+ Post an idea</a><?php endif; ?>
</div>

<form class="search" method="get">
  <input type="text" name="keyword" placeholder="Search by keyword" value="<?php echo e($keyword); ?>">
  <?php skill_select('skill', $skill, 'Any skill needed'); ?>
  <button class="btn" type="submit">Search</button>
  <a class="btn btn-grey" href="home.php">Reset</a>
</form>

<?php if (count($posts) == 0) echo '<div class="box">No posts found.</div>'; ?>

<?php foreach ($posts as $p): ?>
<div class="post">
  <h3><a href="view_post.php?id=<?php echo $p['id']; ?>"><?php echo e($p['title']); ?></a></h3>
  <div class="info">
    by <b><?php echo e($p['name']); ?></b> (<?php echo e($p['uid']); ?>)
    | <?php echo date('d M Y', strtotime($p['created_at'])); ?> | <?php echo e($p['category']); ?>
    | <span class="status <?php echo $p['status']; ?>"><?php echo $p['status']; ?></span>
  </div>
  <?php if ($p['image']): ?>
    <img src="uploads/<?php echo e($p['image']); ?>" alt="post photo">
  <?php endif; ?>
  <p><?php echo nl2br(e(substr($p['description'], 0, 250))); ?><?php if (strlen($p['description']) > 250) echo '...'; ?></p>
  <p><b>Skills needed:</b> <?php echo skill_tags($p['skills_needed']); ?></p>
  <a class="btn btn-small" href="view_post.php?id=<?php echo $p['id']; ?>">View details</a>
</div>
<?php endforeach; ?>

<?php include 'footer.php'; ?>
