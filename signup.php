<?php
$pageTitle = 'Sign up | Study Nook';
$pageDescription = 'Create an account to request library study rooms.';

require __DIR__ . '/includes/auth_functions.php';
redirectLoggedinUser();

require __DIR__ . '/includes/layout/header.php';
require __DIR__ . '/includes/layout/navbar.php';

?>
<main id="main-content" class="auth-page">
    <div class="container auth-layout">
        <section class="auth-intro" aria-labelledby="signup-intro-title">
            <p class="eyebrow">STUDY NOOK</p>
            <h1 id="signup-intro-title">A quiet place to focus.</h1>
            <p class="auth-intro__text">Create an account to book a library study room.</p>
        </section>

        <section class="auth-card card" aria-labelledby="signup-title">
            <div class="auth-card__heading"><h2 id="signup-title">Create account</h2></div>
            <?php
            $signupErrors = $_SESSION['signup_errors'] ?? [];
            unset($_SESSION['signup_errors']);
            ?>
            <?php if ($signupErrors !== []): ?>
                <div class="alert alert--error" role="alert">
                    <ul>
                        <?php foreach ($signupErrors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form class="auth-form" action="<?= htmlspecialchars(app_url('actions/auth/signup.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                <div class="form-field">
                    <label class="form-label" for="full-name">Full name</label>
                    <input class="form-control" id="full-name" name="fullName" type="text" autocomplete="name" maxlength="120" required>
                </div>
                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-control" id="email" name="email" type="email" autocomplete="email" maxlength="190" required>
                </div>
                <div class="auth-form__passwords">
                    <div class="form-field">
                        <label class="form-label" for="password">Password</label>
                        <div class="password-input">
                            <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" minlength="8" placeholder="At least 8 characters" required>
                            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="password" data-password-toggle>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <g data-icon-hidden><path d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A11.7 11.7 0 0112 5c5.5 0 9 7 9 7a14.8 14.8 0 01-3.1 3.8M6.2 6.2C4.1 7.6 3 12 3 12s3.5 7 9 7a9.8 9.8 0 003-.5" /></g>
                                    <g data-icon-visible><path d="M2.5 12s3.4-7 9.5-7 9.5 7 9.5 7-3.4 7-9.5 7-9.5-7-9.5-7z" /><circle cx="12" cy="12" r="3" /></g>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="confirm-password">Confirm password</label>
                        <div class="password-input">
                            <input class="form-control" id="confirm-password" name="confirmPassword" type="password" autocomplete="new-password" minlength="8" required>
                            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="confirm-password" data-password-toggle>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <g data-icon-hidden><path d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A11.7 11.7 0 0112 5c5.5 0 9 7 9 7a14.8 14.8 0 01-3.1 3.8M6.2 6.2C4.1 7.6 3 12 3 12s3.5 7 9 7a9.8 9.8 0 003-.5" /></g>
                                    <g data-icon-visible><path d="M2.5 12s3.4-7 9.5-7 9.5 7 9.5 7-3.4 7-9.5 7-9.5-7-9.5-7z" /><circle cx="12" cy="12" r="3" /></g>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button class="button button--primary auth-form__submit" type="submit">Create account</button>
            </form>
            <p class="auth-card__switch">Already have an account? <a href="<?= htmlspecialchars(app_url('login.php'), ENT_QUOTES, 'UTF-8') ?>">Log in</a></p>
        </section>
    </div>
</main>
<?php require __DIR__ . '/includes/layout/footer.php'; ?>
