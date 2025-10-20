<?php $title = 'Admin Login - OneID'; ?>
<?php require __DIR__ . '/partials/head.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<style>
    .login-container {
        max-width: 400px;
        margin: 4rem auto;
        padding: 0 1rem;
    }

    .login-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .login-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 0.5rem;
    }

    .login-header p {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.15s;
    }

    .form-input:focus {
        outline: none;
        border-color: #111;
    }

    .login-button {
        width: 100%;
        padding: 0.75rem;
        background: #111;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s;
    }

    .login-button:hover {
        background: #000;
    }

    .error-message {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
</style>

<main class="page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Admin Login</h1>
                <p>Enter your credentials to access the dashboard</p>
            </div>

            <?php if (isset($loginError)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($loginError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin.php">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input"
                        placeholder="Enter username"
                        required
                        autofocus>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Enter password"
                        required>
                </div>

                <button type="submit" name="login" class="login-button">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>