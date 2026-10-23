<?php
$pageTitle = 'Log in | Study Nook';
$pageDescription = 'Log in to manage your library study room reservations.';
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
            <div class="auth-card__heading"><h2 id="login-title">Log in</h2></div>
            <form class="auth-form" action="<?= htmlspecialchars(app_url('actions/auth/login.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-control" id="email" name="email" type="email" autocomplete="email" maxlength="190" required>
                </div>
                <div class="form-field">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
                </div>
                <button class="button button--primary auth-form__submit" type="submit">Log in</button>
            </form>
            <p class="auth-card__switch">New to Study Nook? <a href="<?= htmlspecialchars(app_url('signup.php'), ENT_QUOTES, 'UTF-8') ?>">Create an account</a></p>
        </section>
    </div>
</main>
<?php require __DIR__ . '/includes/layout/footer.php'; ?>
