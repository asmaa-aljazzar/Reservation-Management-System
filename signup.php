<?php
$pageTitle = 'Sign up | Study Nook';
$pageDescription = 'Create an account to request library study rooms.';
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
            <form class="auth-form" action="<?= htmlspecialchars(app_url('actions/auth/signup.php'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                <div class="form-field">
                    <label class="form-label" for="full-name">Full name</label>
                    <input class="form-control" id="full-name" name="full_name" type="text" autocomplete="name" maxlength="120" required>
                </div>
                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-control" id="email" name="email" type="email" autocomplete="email" maxlength="190" required>
                </div>
                <div class="auth-form__passwords">
                    <div class="form-field">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" minlength="8" placeholder="At least 8 characters" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="confirm-password">Confirm password</label>
                        <input class="form-control" id="confirm-password" name="confirm_password" type="password" autocomplete="new-password" minlength="8" required>
                    </div>
                </div>
                <button class="button button--primary auth-form__submit" type="submit">Create account</button>
            </form>
            <p class="auth-card__switch">Already have an account? <a href="<?= htmlspecialchars(app_url('login.php'), ENT_QUOTES, 'UTF-8') ?>">Log in</a></p>
        </section>
    </div>
</main>
<?php require __DIR__ . '/includes/layout/footer.php'; ?>
