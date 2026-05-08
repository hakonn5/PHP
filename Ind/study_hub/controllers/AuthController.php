<?php

require_once __DIR__ . '/../models/User.php';

/**
 * Authentication Controller
 */
class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Handle login display and submission.
     *
     * @return void
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                flash('error', 'Please fill in all fields.');
                redirect('/login');
            }

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true); // Prevent session fixation
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['username'] = $user['username'];
                flash('success', 'Logged in successfully.');
                redirect('/');
            } else {
                flash('error', 'Invalid email or password.');
                redirect('/login');
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Handle registration display and submission.
     *
     * @return void
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($username) || empty($email) || empty($password)) {
                flash('error', 'Please fill in all fields.');
                redirect('/register');
            }

            if ($password !== $passwordConfirm) {
                flash('error', 'Passwords do not match.');
                redirect('/register');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Invalid email format.');
                redirect('/register');
            }

            if ($this->userModel->findByEmail($email)) {
                flash('error', 'Email is already registered.');
                redirect('/register');
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            if ($this->userModel->create($username, $email, $hash)) {
                flash('success', 'Registration successful. You can now login.');
                redirect('/login');
            } else {
                flash('error', 'Registration failed. Please try again.');
                redirect('/register');
            }
        }

        require __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Handle logout.
     *
     * @return void
     */
    public function logout(): void
    {
        session_destroy();
        redirect('/');
    }

    /**
     * Handle forgot password.
     *
     * @return void
     */
    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

            if (empty($email)) {
                flash('error', 'Please enter your email.');
                redirect('/forgot-password');
            }

            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $this->userModel->setResetToken($user['id'], $token);

                // Simulate sending email by writing to log
                $logMessage = "[" . date('Y-m-d H:i:s') . "] Password reset link for $email: http://localhost:8000/reset-password?token=$token\n";
                file_put_contents(__DIR__ . '/../logs/emails.log', $logMessage, FILE_APPEND);

                flash('success', 'A password reset link has been sent to your email.');
            } else {
                // Do not reveal if email exists or not
                flash('success', 'A password reset link has been sent to your email.');
            }
            redirect('/forgot-password');
        }

        require __DIR__ . '/../views/auth/forgot_password.php';
    }
}
