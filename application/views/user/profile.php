<div class="container mt-4">

<div class="row">
<div class="col-12 col-md-8 col-lg-6 mx-auto">

<h2 class="mb-4"><i class="fas fa-user-circle me-2"></i>My Profile</h2>

<div class="card shadow-sm">
<div class="card-body p-4">

<form method="post" action="<?= base_url('user/profile_update') ?>">

<?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

<div class="mb-3">
<label class="form-label">Full Name</label>
<input type="text" name="name" value="<?= html_escape($user->name) ?>" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Email Address</label>
<input type="email" name="email" value="<?= html_escape($user->email) ?>" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Role</label>
<input type="text" value="<?= ucfirst($user->role) ?>" class="form-control" disabled>
</div>

<div class="mb-3">
<label class="form-label">Member Since</label>
<input type="text" value="<?= date('F d, Y', strtotime($user->created_at)) ?>" class="form-control" disabled>
</div>

<hr class="my-4">

<h5 class="mb-3">Change Password (Optional)</h5>

<div class="mb-3">
<label class="form-label">New Password</label>
<input type="password" name="password" placeholder="Leave blank to keep current password" class="form-control">
<small class="text-muted">Minimum 6 characters</small>
</div>

<div class="d-grid gap-2 d-sm-flex">
<button type="submit" class="btn btn-primary">
<i class="fas fa-save me-2"></i>Save Changes
</button>

<a href="<?= base_url('user/dashboard') ?>" class="btn btn-secondary">
<i class="fas fa-arrow-left me-2"></i>Back to Dashboard
</a>
</div>

</form>

</div>
</div>

</div>
</div>

</div>
