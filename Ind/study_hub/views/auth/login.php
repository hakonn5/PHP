<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm mt-5 border-0">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">Login</h3>
                <form action="/login" method="POST" id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    <div class="text-center">
                        <a href="/forgot-password" class="text-decoration-none small">Forgot Password?</a>
                        <br>
                        <span class="small">Don't have an account? <a href="/register" class="text-decoration-none">Register</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    if (!email || !password) {
        e.preventDefault();
        alert('Please fill in all fields.');
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
