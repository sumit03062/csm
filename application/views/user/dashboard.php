<div class="container-fluid mt-4">

    <h2 class="mb-4">User Dashboard</h2>

    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">

                    <h5 class="card-title">My Posts</h5>
                    <h2 class="text-primary"><?= $my_posts ?? 0 ?></h2>

                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">

                    <h5 class="card-title">My Profile</h5>

                    <a href="<?= base_url('user/profile') ?>" class="btn btn-info w-100">
                        <i class="fas fa-user"></i> View Profile
                    </a>

                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">

                    <h5 class="card-title">Create Post</h5>

                    <a href="<?= base_url('posts/create') ?>" class="btn btn-success w-100">
                        <i class="fas fa-plus"></i> New Post
                    </a>

                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">

                    <h5 class="card-title">Total Users</h5>
                    <h2 class="text-info"><?= $total_users ?? 0 ?></h2>

                </div>
            </div>
        </div>

    </div>

    <hr class="my-4">

    <h4>My Posts</h4>

    <div class="table-responsive">

        <table class="table table-hover">

            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($posts)): ?>

                    <?php foreach ($posts as $post): ?>

                        <tr>

                            <td><?= html_escape($post->title) ?></td>
                            <td><?= html_escape($post->created_at) ?></td>

                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= base_url('posts/edit/' . $post->id) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <a href="#" class="btn btn-sm btn-danger" onclick="return confirmDelete('<?= base_url('posts/delete/' . $post->id) ?>', 'this post')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="3" class="text-center py-4">No posts found</td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>