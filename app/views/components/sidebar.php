<?php
$activePage = $activePage ?? '';
$currentRole = $_SESSION['role'] ?? 'user';

$menuItems = [
    ['key' => 'dashboard', 'label' => 'Home', 'href' => '/dashboard'],
];

if ($currentRole === 'admin') {
    $menuItems[] = ['key' => 'users', 'label' => 'Users', 'href' => '/users'];
}

$menuItems[] = ['key' => 'profile', 'label' => 'Profile', 'href' => '/profile'];
$menuItems[] = ['key' => 'settings', 'label' => 'Settings', 'href' => '#'];
$menuItems[] = ['key' => 'logout', 'label' => 'Logout', 'href' => '/logout'];
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-mark">S</div>
        <h2>SIPOS</h2>
    </div>
    <ul>
        <?php foreach ($menuItems as $item): ?>
            <?php $isActive = $activePage === $item['key']; ?>
            <li>
                <a href="<?= htmlspecialchars($item['href']) ?>" class="<?= $isActive ? 'active' : '' ?>">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>
