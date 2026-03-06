<div class="container">
<div class="row min-vh-100 align-items-center justify-content-center">
<div class="col-12 col-sm-8 col-md-6 col-lg-4">

<div class="card shadow-lg border-0">
<div class="card-body p-5">

<h3 class="text-center mb-4"><i class="fas fa-user-plus me-2"></i>Register</h3>

<form action="<?= base_url('auth/register') ?>" method="post">

<?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
<input type="hidden" name="role" value="user">

<div class="mb-3">
<label class="form-label">Full Name</label>
<input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
</div>

<div class="mb-3">
<label class="form-label">Email Address</label>
<input type="email" name="email" class="form-control" placeholder="Enter your email" required>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
</div>

<button type="submit" class="btn btn-success w-100 py-2">
<i class="fas fa-user-plus me-2"></i>Register
</button>

<hr class="my-3">

<div class="text-center">
<p class="text-muted small">Already have an account?</p>
<a href="<?= base_url('login') ?>" class="btn btn-outline-primary btn-sm">Sign In</a>
</div>

</form>

</div>
</div>

</div>
</div>
</div>