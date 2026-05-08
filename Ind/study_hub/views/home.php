<?php require __DIR__ . '/layout/header.php'; ?>

<div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
    <div class="container-fluid py-5 text-center">
        <h1 class="display-5 fw-bold">Welcome to Study Hub</h1>
        <p class="col-md-8 mx-auto fs-4">Your central repository for educational materials. Discover PDFs, video lectures, and articles across various categories.</p>
        <a href="/resources/search" class="btn btn-primary btn-lg mt-3">Explore Resources</a>
        <?php if (!isAuthenticated()): ?>
            <a href="/register" class="btn btn-outline-secondary btn-lg mt-3 ms-2">Join Now</a>
        <?php endif; ?>
    </div>
</div>

<h2 class="mb-4">Recently Added Materials</h2>
<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php if (empty($latestResources)): ?>
        <div class="col-12">
            <p class="text-muted">No resources available yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($latestResources as $resource): ?>
            <div class="col">
                <div class="card resource-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2"><?= htmlspecialchars($resource['category_name'] ?? 'Uncategorized') ?></span>
                        <span class="badge bg-info mb-2 text-dark"><?= strtoupper(htmlspecialchars($resource['format'])) ?></span>
                        <h5 class="card-title"><?= htmlspecialchars($resource['title']) ?></h5>
                        <p class="card-text text-muted small">
                            <?= htmlspecialchars(substr($resource['description'], 0, 100)) ?>...
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0">
                        <small class="text-muted">
                            Added by <?= htmlspecialchars($resource['author_name']) ?> <br>
                            Difficulty: <?= ucfirst(htmlspecialchars($resource['difficulty'])) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
