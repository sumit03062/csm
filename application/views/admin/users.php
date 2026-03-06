<div class="container-fluid mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">
<h2><i class="fas fa-users me-2"></i>Manage Users</h2>
<a href="<?= base_url('admin/user_create') ?>" class="btn btn-success">
<i class="fas fa-user-plus me-2"></i>Create User
</a>
</div>

<div class="table-responsive">
<table class="table table-hover">

<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Joined</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php if (!empty($users)): ?>

<?php foreach ($users as $user): ?>

<tr>

<td>
<strong><?= html_escape($user->name) ?></strong>
</td>

<td><?= html_escape($user->email) ?></td>

<td>
<span class="badge <?= $user->role == 'admin' ? 'bg-danger' : 'bg-info' ?>">
<?= ucfirst($user->role) ?>
</span>
</td>

<td><?= date('M d, Y', strtotime($user->created_at)) ?></td>

<td>
<div class="btn-group btn-group-sm" role="group">
<a href="<?= base_url('admin/user_edit/' . $user->id) ?>" class="btn btn-sm btn-primary">
<i class="fas fa-edit"></i> Edit
</a>

<a href="#" class="btn btn-sm btn-danger" onclick="return confirmDelete('<?= base_url('admin/user_delete/' . $user->id) ?>', 'this user and all their posts')">
<i class="fas fa-trash"></i> Delete
</a>
</div>
</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>
<td colspan="5" class="text-center py-4">No users found</td>
</tr>

<?php endif; ?>

</tbody>

</table>
</div>

</div>
