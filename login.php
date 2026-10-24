<?php
$pageTitle = 'Log in | Study Nook';
$pageDescription = 'Log in to manage your library study room reservations.';

require __DIR__ . '/includes/auth_functions.php';
redirectLoggedinUser();

require __DIR__ . '/includes/layout/header.php';
require __DIR__ . '/includes/layout/navbar.php';

?>
<main id="main-content" class="auth-page">
    <div class="container auth-layout">
        <section class="auth-intro" aria-labelledby="login-intro-title">
            <p class="eyebrow">STUDY NOOK</p>
            <h1 id="login-intro-title">Welcome back.</h1>
            <p class="auth-intro__text">Your quiet study space is just a moment away.</p>
        </section>

        <section class="auth-card card" aria-labelledby="login-title">
            <div class="auth-card__heading auth-card__heading--demos">
                <h2 id="login-title">Log in</h2>
                <div class="demo-login-buttons" aria-label="Fill in a demo account">
                    <button class="demo-login-button demo-login-button--admin" type="button" data-demo-login data-email="demo.admin@example.com" data-password="Demo123!">Demo admin</button>
                    <button class="demo-login-button" type="button" data-demo-login data-email="demo.user1@example.com" data-password="Demo123!">Demo user 1</button>
                    <button class="demo-login-button" type="button" data-demo-login data-email="demo.user2@example.com" data-password="Demo123!">Demo user 2</button>
                    <button class="demo-login-button" type="button" data-demo-login data-email="demo.user3@example.com" data-password="Demo123!">Demo user 3</button>
                </div>
            </div>
            <form class="auth-form" action="<?= htmlspecialchars(app_url('actions/auth/login.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-control" id="email" name="email" type="email" autocomplete="email" maxlength="190" required>
                </div>
                <div class="form-field">
                    <label class="form-label" for="password">Password</label>
                    <div class="password-input">
                        <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
                        <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="password" data-password-toggle>
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <g data-icon-hidden><path d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A11.7 11.7 0 0112 5c5.5 0 9 7 9 7a14.8 14.8 0 01-3.1 3.8M6.2 6.2C4.1 7.6 3 12 3 12s3.5 7 9 7a9.8 9.8 0 003-.5" /></g>
                                <g data-icon-visible><path d="M2.5 12s3.4-7 9.5-7 9.5 7 9.5 7-3.4 7-9.5 7-9.5-7-9.5-7z" /><circle cx="12" cy="12" r="3" /></g>
                            </svg>
                        </button>
                    </div>
                </div>
                <button class="button button--primary auth-form__submit" type="submit">Log in</button>
            </form>
            <p class="auth-card__switch">New to Study Nook? <a href="<?= htmlspecialchars(app_url('signup.php'), ENT_QUOTES, 'UTF-8') ?>">Create an account</a></p>
        </section>
    </div>
</main>
<?php require __DIR__ . '/includes/layout/footer.php'; ?>
