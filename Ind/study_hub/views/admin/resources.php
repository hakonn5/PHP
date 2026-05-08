<?php require __DIR__ . '/../layout/header.php'; ?>

<h2 class="mb-4">Manage Resources</h2>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Format</th>
                        <th>Difficulty</th>
                        <th>Author</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $res): ?>
                        <tr>
                            <td><?= $res['id'] ?></td>
                            <td><?= htmlspecialchars($res['title']) ?></td>
                            <td><?= htmlspecialchars($res['category_name'] ?? 'None') ?></td>
                            <td><span class="badge bg-info text-dark"><?= strtoupper(htmlspecialchars($res['format'])) ?></span></td>
                            <td><?= ucfirst(htmlspecialchars($res['difficulty'])) ?></td>
                            <td><?= htmlspecialchars($res['author_name']) ?></td>
                            <td>
                                <!-- Edit Button trigger modal -->
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $res['id'] ?>">
                                    Edit
                                </button>
                                
                                <!-- Delete Form -->
                                <form action="/admin/resources" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="resource_id" value="<?= $res['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $res['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form class="modal-content" action="/admin/resources" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Resource #<?= $res['id'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="resource_id" value="<?= $res['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($res['title']) ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Category</label>
                                            <select class="form-select" name="category_id" required>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $res['category_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cat['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Format</label>
                                            <select class="form-select" name="format" required>
                                                <option value="pdf" <?= $res['format'] === 'pdf' ? 'selected' : '' ?>>PDF</option>
                                                <option value="video" <?= $res['format'] === 'video' ? 'selected' : '' ?>>Video</option>
                                                <option value="article" <?= $res['format'] === 'article' ? 'selected' : '' ?>>Article</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Difficulty</label>
                                            <select class="form-select" name="difficulty" required>
                                                <option value="easy" <?= $res['difficulty'] === 'easy' ? 'selected' : '' ?>>Easy</option>
                                                <option value="medium" <?= $res['difficulty'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                                <option value="hard" <?= $res['difficulty'] === 'hard' ? 'selected' : '' ?>>Hard</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($res['description']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if(empty($resources)): ?>
                <div class="text-center py-4 text-muted">No resources found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
