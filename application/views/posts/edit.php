<div class="container mt-4">

<div class="row">
<div class="col-12 col-md-8 col-lg-6 mx-auto">

<h2 class="mb-4">Edit Post</h2>

<form method="post" action="<?= base_url('posts/update/'.$post->id) ?>" class="card p-4 shadow-sm">

<?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

<div class="mb-3">

<label class="form-label">Title</label>

<input type="text"
name="title"
value="<?= html_escape($post->title) ?>"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Content</label>

<textarea name="content"
class="form-control"
rows="5"
required><?= html_escape($post->content) ?></textarea>

</div>

<div class="mb-3">

<label class="form-label">Status</label>

<select name="status" class="form-select" required>
<option value="draft" <?= ($post->status == 'draft') ? 'selected' : '' ?>>Draft</option>
<option value="published" <?= ($post->status == 'published') ? 'selected' : '' ?>>Publish</option>
</select>

<small class="text-muted">Drafts are only visible to you. Published posts are visible to everyone.</small>

</div>

<div class="mb-3">

<label class="form-label">Categories</label>

<div class="card p-2" style="max-height: 150px; overflow-y: auto;">
<?php 
$post_category_ids = array();
if (!empty($post_categories)) {
    foreach ($post_categories as $pc) {
        $post_category_ids[] = $pc->id;
    }
}

if (!empty($categories)): ?>
    <?php foreach ($categories as $category): ?>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $category->id ?>" id="cat_<?= $category->id ?>" <?= in_array($category->id, $post_category_ids) ? 'checked' : '' ?>>
            <label class="form-check-label" for="cat_<?= $category->id ?>">
                <?= html_escape($category->name) ?>
            </label>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-muted">No categories available</p>
<?php endif; ?>
</div>

<small class="text-muted">Select categories for your post</small>

</div>

<div class="d-grid gap-2 d-sm-flex">
<button type="submit" class="btn btn-success">
<i class="fas fa-save me-2"></i>Update Post
</button>

<a href="<?= base_url('posts') ?>" class="btn btn-secondary">
<i class="fas fa-arrow-left me-2"></i>Cancel
</a>
</div>

</form>

</div>
</div>

</div>