<?php $topbarTitle = $topbarTitle ?? 'Dashboard'; ?>
<header class="topbar">
    <div class="title"><?= htmlspecialchars($topbarTitle) ?></div>
    <div class="topbar-actions">
        <label class="theme-switch" title="Toggle theme">
            <span class="visually-hidden">Toggle theme</span>
            <input id="theme-toggle" type="checkbox" aria-label="Toggle theme">
            <span class="theme-slider"></span>
        </label>
        <div class="profile-menu">
            <button id="profile-button" class="profile-trigger" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="profile-dropdown">👤</button>
            <div id="profile-dropdown" class="profile-dropdown" role="menu">
                <a href="/profile" role="menuitem">Profile</a>
                <a href="/logout" role="menuitem">Logout</a>
            </div>
        </div>
    </div>
</header>
