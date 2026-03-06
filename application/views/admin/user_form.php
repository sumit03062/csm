<div class="container mt-4">

<div class="row">
<div class="col-12 col-md-8 col-lg-6 mx-auto">

<h2 class="mb-4">
<?php if (isset($user)): ?>
<i class="fas fa-edit me-2"></i>Edit User
<?php else: ?>
<i class="fas fa-user-plus me-2"></i>Create New User
<?php endif; ?>
</h2>

<div class="card shadow-sm">
<div class="card-body p-4">

<?php if (isset($user)): ?>
<form method="post" action="<?= base_url('admin/user_update/' . $user->id) ?>">
<?php else: ?>
<form method="post" action="<?= base_url('admin/user_store') ?>">
<?php endif; ?>

<?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

<div class="mb-3">
<label class="form-label">Full Name</label>
<input type="text" name="name" value="<?= isset($user) ? html_escape($user->name) : '' ?>" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Email Address</label>
<input type="email" name="email" value="<?= isset($user) ? html_escape($user->email) : '' ?>" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Role</label>
<select name="role" class="form-select" required>
<option value="">-- Select Role --</option>
<option value="user" <?= isset($user) && $user->role == 'user' ? 'selected' : '' ?>>User</option>
<option value="admin" <?= isset($user) && $user->role == 'admin' ? 'selected' : '' ?>>Admin</option>
</select>
</div>

<div class="mb-3">
<label class="form-label">
<?php if (isset($user)): ?>
New Password (Leave blank to keep current)
<?php else: ?>
Password
<?php endif; ?>
</label>
<input type="password" name="password" class="form-control" <?= !isset($user) ? 'required' : '' ?>>
<small class="text-muted">Minimum 6 characters</small>
</div>

<div class="d-grid gap-2 d-sm-flex">
<button type="submit" class="btn btn-success">
<i class="fas fa-save me-2"></i>
<?php if (isset($user)): ?>
Update User
<?php else: ?>
Create User
<?php endif; ?>
</button>

<a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
<i class="fas fa-arrow-left me-2"></i>Cancel
</a>
</div>

</form>

</div>
</div>

</div>
</div>

</div>
