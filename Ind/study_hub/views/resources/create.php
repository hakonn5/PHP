<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm mt-3 border-0">
            <div class="card-body p-4">
                <h3 class="card-title mb-4">Add New Resource</h3>
                <form action="/resources/create" method="POST" id="resourceForm">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Choose a category...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Format</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="format" id="format_pdf" value="pdf" required>
                            <label class="form-check-label" for="format_pdf">PDF</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="format" id="format_video" value="video" required>
                            <label class="form-check-label" for="format_video">Video</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="format" id="format_article" value="article" required>
                            <label class="form-check-label" for="format_article">Article</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="difficulty" class="form-label">Difficulty</label>
                        <select class="form-select" id="difficulty" name="difficulty" required>
                            <option value="">Select difficulty...</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required minlength="10"></textarea>
                        <div class="form-text">Provide a brief description of the material.</div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Save Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('resourceForm').addEventListener('submit', function(e) {
    let title = document.getElementById('title').value.trim();
    let desc = document.getElementById('description').value.trim();
    
    if (title === '' || desc === '') {
        e.preventDefault();
        alert('Title and description cannot be empty.');
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
