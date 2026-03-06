<div class="container">
<div class="row min-vh-100 align-items-center justify-content-center">
<div class="col-12 col-sm-8 col-md-6 col-lg-4">

<div class="card shadow-lg border-0">
<div class="card-body p-5">

<h3 class="text-center mb-4"><i class="fas fa-sign-in-alt me-2"></i>Login</h3>

<form action="<?= base_url('auth/login') ?>" method="post">

<?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

<div class="mb-3">
<label class="form-label">Email Address</label>
<input type="email" name="email" class="form-control" placeholder="Enter your email" required>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input type="password" name="password" class="form-control" placeholder="Enter your password" required>
</div>

<button type="submit" class="btn btn-primary w-100 py-2">
<i class="fas fa-sign-in-alt me-2"></i>Login
</button>

<hr class="my-3">

<div class="text-center">
<p class="text-muted small">Don't have an account?</p>
<a href="<?= base_url('register') ?>" class="btn btn-outline-primary btn-sm">Create Account</a>
</div>

</form>

</div>
</div>

</div>
</div>
</div>