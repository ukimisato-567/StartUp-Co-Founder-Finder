<?php
include '../config.php';
admin_required();
$title = "Manage users";

// suspend / activate / delete a user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['user_id'];
    $action = $_POST['action'];

    if ($action == 'suspend') {
        $pdo->prepare("UPDATE users SET status = 'suspended' WHERE id = ? AND role = 'user'")->execute([$id]);
        flash("User suspended.");
    } elseif ($action == 'activate') {
        $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ? AND role = 'user'")->execute([$id]);
        flash("User activated.");
    } elseif ($action == 'delete') {
        $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'")->execute([$id]);
        flash("User deleted.");
    }
    header("Location: users.php?search=" . urlencode($_POST['search']));
    exit;
}

$search = trim($_GET['search'] ?? '');
$q = $pdo->prepare("SELECT * FROM users WHERE role = 'user' AND (uid LIKE ? OR name LIKE ? OR email LIKE ?) ORDER BY id DESC");
$q->execute(["%$search%", "%$search%", "%$search%"]);
$users = $q->fetchAll();
include '../header.php';
?>
<h2>Manage users</h2>
<form class="search" method="get">
  <input type="text" name="search" placeholder="Search by user ID, name or email" value="<?php echo e($search); ?>">
  <button class="btn" type="submit">Search</button>
</form>

<table>
  <tr><th>User ID</th><th>Name</th><th>Email</th><th>Skills</th><th>Status</th><th>Action</th></tr>
  <?php foreach ($users as $u): ?>
  <tr>
    <td><?php echo e($u['uid']); ?></td>
    <td><?php echo e($u['name']); ?></td>
    <td><?php echo e($u['email']); ?></td>
    <td><?php echo e($u['skills']); ?></td>
    <td><?php echo $u['status']; ?></td>
    <td>
      <form method="post" style="margin:0" onsubmit="return confirm('Are you sure?');">
        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
        <input type="hidden" name="search" value="<?php echo e($search); ?>">
        <?php if ($u['status'] == 'active'): ?>
          <button class="btn btn-small" name="action" value="suspend">Suspend</button>
        <?php else: ?>
          <button class="btn btn-small" name="action" value="activate">Activate</button>
        <?php endif; ?>
        <button class="btn btn-red btn-small" name="action" value="delete">Delete</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (count($users) == 0) echo '<tr><td colspan="6">No users found.</td></tr>'; ?>
</table>
<?php include '../footer.php'; ?>
