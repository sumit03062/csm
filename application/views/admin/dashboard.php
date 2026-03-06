<div class="container-fluid mt-4">

    <h2 class="mb-4">Admin Dashboard</h2>

    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2 class="text-primary"><?= $total_users ?? 0 ?></h2>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-primary mt-2">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Posts</h5>
                    <h2 class="text-success"><?= $total_posts ?? 0 ?></h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Latest Posts</h5>
                    <h2 class="text-info"><?= $latest_posts ?? 0 ?></h2>
                </div>
            </div>
        </div>

    </div>

    <!-- API CARD -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-laugh me-2"></i>Random Joke (API)</h5>
        </div>
        <div class="card-body">
            <p class="mb-0"><?= html_escape($random_joke) ?></p>
        </div>
    </div>

    <hr class="my-4">

    <h4>Recent Posts</h4>

    <div class="table-responsive">

    <table class="table table-hover">

        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($posts)): ?>

                <?php foreach ($posts as $post): ?>

                    <tr>

                        <td><?= html_escape($post->title) ?></td>
                        <td><?= html_escape($post->name) ?></td>
                        <td><?= html_escape($post->created_at) ?></td>

                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= base_url('posts/edit/' . $post->id) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>

                                <a href="#"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirmDelete('<?= base_url('posts/delete/' . $post->id) ?>', 'this post')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="4" class="text-center py-4">No posts found</td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>
    </div>