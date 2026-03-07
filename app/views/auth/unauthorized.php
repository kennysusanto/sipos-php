<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unauthorized - SIPOS</title>
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
        <div class="sipos-logo-mark">!</div>
    </div>
    <h2>Unauthorized Access</h2>
    <p class="unauthorized-message">You do not have permission to access this page.</p>
    <a class="btn btn-primary unauthorized-home-btn" href="/dashboard">Go Back to Home</a>
</div>
<div class="github-login-footer">
    <span>© <?= date('Y') ?> SIPOS</span>
</div>
<script src="/js/theme.js"></script>
</body>
</html>
