<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm mt-5 border-0">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">Reset Password</h3>
                <p class="text-muted text-center small">Enter your email address and we will send you a link to reset your password.</p>
                <form action="/forgot-password" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </div>
                    <div class="text-center">
                        <a href="/login" class="text-decoration-none small">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
