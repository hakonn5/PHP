<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="mb-3">Search Resources</h2>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="/resources/search" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-lg" name="q" placeholder="Search by title or description..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-lg" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php if (empty($resources)): ?>
        <div class="col-12 text-center mt-5">
            <h4 class="text-muted">No resources found matching your criteria.</h4>
        </div>
    <?php else: ?>
        <?php foreach ($resources as $resource): ?>
            <div class="col">
                <div class="card resource-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2"><?= htmlspecialchars($resource['category_name'] ?? 'Uncategorized') ?></span>
                        <span class="badge bg-info mb-2 text-dark"><?= strtoupper(htmlspecialchars($resource['format'])) ?></span>
                        <h5 class="card-title"><?= htmlspecialchars($resource['title']) ?></h5>
                        <p class="card-text text-muted small">
                            <?= htmlspecialchars(substr($resource['description'], 0, 150)) ?><?= strlen($resource['description']) > 150 ? '...' : '' ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        <small class="text-muted">
                            Author: <?= htmlspecialchars($resource['author_name']) ?> <br>
                            Difficulty: <?= ucfirst(htmlspecialchars($resource['difficulty'])) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
