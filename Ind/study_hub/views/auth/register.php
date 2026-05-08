<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm mt-4 border-0">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">Register Account</h3>
                <form action="/register" method="POST" id="registerForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required minlength="3">
                        <div class="invalid-feedback">Username must be at least 3 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <div class="invalid-feedback">Password must be at least 6 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        <div class="invalid-feedback">Passwords do not match.</div>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                    <div class="text-center">
                        <span class="small">Already have an account? <a href="/login" class="text-decoration-none">Login</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let isValid = true;
    let pass = document.getElementById('password').value;
    let passConf = document.getElementById('password_confirm').value;

    if (pass !== passConf) {
        document.getElementById('password_confirm').classList.add('is-invalid');
        isValid = false;
    } else {
        document.getElementById('password_confirm').classList.remove('is-invalid');
    }

    if (!isValid) {
        e.preventDefault();
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
