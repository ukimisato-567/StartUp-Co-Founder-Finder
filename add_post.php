<?php
include 'config.php';
login_required();
if ($me['role'] != 'user') { header("Location: home.php"); exit; }
$title = "Post an idea";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ptitle = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $category = $_POST['category'];
    $skillList = collect_skills('skills_needed', 'skills_other');
    $skills = skills_to_text($skillList);
    $imageName = "";

    if ($ptitle == '' || $desc == '') {
        $error = "Please fill title and description.";
    } elseif (skills_error($skillList) != '') {
        $error = skills_error($skillList);
    }

    // photo upload (optional)
    if ($error == '' && $_FILES['image']['name'] != '') {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if ($_FILES['image']['error'] != 0) {
            $error = "Photo upload failed. The file may be too large.";
        } elseif (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $error = "Photo must be jpg, jpeg, png, gif or webp.";
        } else {
            if (!is_dir('uploads')) mkdir('uploads');
            $imageName = time() . rand(100, 999) . "." . $ext;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imageName)) {
                $error = "Could not save the photo. Check that the uploads folder is writable.";
            }
        }
    }

    if ($error == '') {
        $q = $pdo->prepare("INSERT INTO startups (user_id, title, description, category, skills_needed, image) VALUES (?, ?, ?, ?, ?, ?)");
        $q->execute([$me['id'], $ptitle, $desc, $category, $skills, $imageName]);
        flash("Your idea has been posted.");
        header("Location: view_post.php?id=" . $pdo->lastInsertId());
        exit;
    }
}
include 'header.php';
?>
<div class="box" style="max-width:650px;margin:auto">
  <h2>Post a startup idea</h2>
  <?php if ($error) echo '<div class="msg error">' . e($error) . '</div>'; ?>
  <form method="post" enctype="multipart/form-data">
    <label>Title</label>
    <input type="text" name="title" value="<?php echo e($_POST['title'] ?? ''); ?>">
    <label>Description</label>
    <textarea name="description"><?php echo e($_POST['description'] ?? ''); ?></textarea>
    <label>Category</label>
    <select name="category">
      <?php foreach (['Technology', 'Education', 'Agriculture', 'Finance', 'Health', 'E-commerce', 'Other'] as $c): ?>
        <option<?php if (($_POST['category'] ?? '') == $c) echo ' selected'; ?>><?php echo $c; ?></option>
      <?php endforeach; ?>
    </select>
    <label>Skills needed <small style="font-weight:normal">(select all the skills you need in your team)</small></label>
    <?php skill_picker('skills_needed', is_array($_POST['skills_needed'] ?? null) ? $_POST['skills_needed'] : [], $_POST['skills_other'] ?? ''); ?>
    <label>Photo (optional)</label>
    <input type="file" name="image" accept="image/*"><br><br>
    <button class="btn" type="submit">Post</button>
  </form>
</div>
<script src="js/skills.js"></script>
<?php include 'footer.php'; ?>
