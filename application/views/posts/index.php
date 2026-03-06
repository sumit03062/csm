<div class="container mt-4">

    <h2 class="mb-4">Posts</h2>

    <a href="<?= base_url('posts/create') ?>" class="btn btn-primary mb-3">
        Create Post
    </a>

    <div class="row mb-3 g-2">

        <div class="col-12 col-sm-6 col-md-3">
            <input type="text" id="search" class="form-control" placeholder="Search posts...">
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <input type="text" id="author" class="form-control" placeholder="Filter by author">
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <input type="date" id="date" class="form-control">
        </div>

        <div class="col-12 col-md-3">
            <button class="btn btn-primary w-100" id="filterBtn" onclick="filterPosts()">Filter</button>
        </div>

    </div>

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

            <tbody id="postTableBody">

                <?php if (!empty($posts)): ?>

                    <?php foreach ($posts as $post): ?>

                        <tr>

                            <td><?= html_escape($post->title) ?></td>

                            <td>
                                <?= isset($post->name) ? html_escape($post->name) : 'You' ?>
                            </td>

                            <td><?= html_escape($post->created_at) ?></td>

                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= base_url('posts/edit/' . $post->id) ?>"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
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

    <!-- Add pagination links -->
    <div class="row mt-4">
        <div class="col-md-12">
            <?= isset($pagination) ? $pagination : '' ?>
        </div>
    </div>

</div>


<script>
    let searchTimeout;

    // Live search as user types
    document.getElementById('search').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        const keyword = this.value.trim();

        // Reset filter fields to avoid confusion
        document.getElementById('author').value = '';
        document.getElementById('date').value = '';

        if (keyword.length < 2) {
            // If search is less than 2 characters, reset to show all posts
            if (keyword.length === 0) {
                location.reload();
            }
            return;
        }

        searchTimeout = setTimeout(function() {
            searchPosts(keyword);
        }, 300); // Debounce: wait 300ms after user stops typing
    });

    function searchPosts(keyword) {
        $.ajax({
            url: "<?= base_url('posts/search') ?>",
            type: "POST",
            data: {
                keyword: keyword,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            beforeSend: function() {
                $('#filterBtn').prop('disabled', true);
                $('#postTableBody').html('<tr><td colspan="4" class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Searching...</td></tr>');
            },
            success: function(response) {
                if (response.trim() === '') {
                    $('#postTableBody').html('<tr><td colspan="4" class="text-center">No posts found matching "' + keyword + '"</td></tr>');
                } else {
                    $('#postTableBody').html(response);
                }
            },
            error: function(xhr, status, error) {
                $('#postTableBody').html('<tr><td colspan="4" class="text-center text-danger"><i class="fas fa-times-circle me-2"></i>Error searching posts</td></tr>');
                console.error('Search Error:', error);
            },
            complete: function() {
                $('#filterBtn').prop('disabled', false);
            }
        });
    }

    function filterPosts() {

        var author = $('#author').val();
        var date = $('#date').val();
        var search = $('#search').val();

        // If search box has content, use search instead
        if (search.trim().length >= 2) {
            searchPosts(search);
            return;
        }

        $.ajax({

            url: "<?= base_url('posts/filter') ?>",
            type: "POST",

            data: {
                author: author,
                date: date,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },

            beforeSend: function() {

                $('#filterBtn').prop('disabled', true);
                $('#postTableBody').html('<tr><td colspan="4" class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>');

            },

            success: function(response) {

                if (response.trim() === '') {
                    $('#postTableBody').html('<tr><td colspan="4" class="text-center">No posts found</td></tr>');
                } else {
                    $('#postTableBody').html(response);
                }

            },

            error: function(xhr, status, error) {

                $('#postTableBody').html('<tr><td colspan="4" class="text-center text-danger"><i class="fas fa-times-circle me-2"></i>Error loading posts</td></tr>');
                console.error('AJAX Error:', error);

            },

            complete: function() {

                $('#filterBtn').prop('disabled', false);

            }

        });

    }
</script>