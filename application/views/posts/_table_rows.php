<?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= html_escape($post->title) ?></td>
            <td><?= html_escape($post->name) ?></td>
            <td><?= html_escape($post->created_at) ?></td>
            <td>
                <span class="badge <?= ($post->status == 'draft') ? 'bg-warning' : 'bg-success' ?>">
                    <?= ucfirst($post->status) ?>
                </span>
            </td>
            <td>
                <a href="<?= base_url('posts/edit/' . $post->id) ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="#" class="btn btn-sm btn-danger" onclick="return confirmDelete('<?= base_url('posts/delete/' . $post->id) ?>', 'this post')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center">No posts found</td>
    </tr>
<?php endif; ?>
