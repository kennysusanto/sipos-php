<?php
$layoutTitle = $layoutTitle ?? 'SIPOS';
$activePage = $activePage ?? '';
$contentView = $contentView ?? '';
$contentData = $contentData ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($layoutTitle) ?></title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php require __DIR__ . '/../components/sidebar.php'; ?>
    <div class="main-content">
        <?php $topbarTitle = $layoutTitle; require __DIR__ . '/../components/topbar.php'; ?>
        <section class="content">
            <?php extract($contentData); require __DIR__ . '/../' . $contentView . '.php'; ?>
        </section>
    </div>
</div>
<script src="/js/theme.js"></script>
</body>
</html>
