<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign in to SIPOS</title>
    <?php require __DIR__ . '/../components/theme-init.php'; ?>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="login-page">
<div class="login-theme-toggle">
    <label class="theme-switch" title="Toggle theme">
        <span class="visually-hidden">Toggle theme</span>
        <input id="theme-toggle" type="checkbox" aria-label="Toggle theme">
        <span class="theme-slider"></span>
    </label>
</div>
<div class="github-login-box">
    <div class="github-login-logo">
        <div class="sipos-logo-mark">S</div>
    </div>
    <h2>Sign in to SIPOS</h2>
    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="/login">
        <label for="username">Username or email address</label>
        <input type="text" id="username" name="username" required autocomplete="username">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
        <button type="submit">Sign in</button>
    </form>
</div>
<div class="github-login-footer">
    <span>© <?= date('Y') ?> SIPOS</span>
</div>
<script src="/js/theme.js"></script>
</body>
</html>
