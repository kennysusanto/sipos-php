<h1 class="page-title">Menu Items</h1>
<div class="page-subtitle">Manage product cards for your catalog.</div>
<?php $isAdmin = ($currentRole === 'admin'); ?>
<?php if ($isAdmin): ?>    
    <div class="page-header-actions">
        <a class="btn btn-primary" href="/menuitems/create">Create Menu Item</a>
    </div>
<?php endif; ?>

<?php if (!empty($status)): ?>
    <div class="alert alert-success">
        <?php if ($status === 'created'): ?>
            Menu item created successfully.
        <?php elseif ($status === 'updated'): ?>
            Menu item updated successfully.
        <?php elseif ($status === 'deleted'): ?>
            Menu item deleted successfully.
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<?php if (empty($items)): ?>
    <div class="dashboard-card">
        <div class="card-title">No menu items found</div>
        <div class="card-value card-value-sm">Create your first menu item to populate this section.</div>
    </div>
<?php else: ?>
    <div class="menu-grid">
        <?php foreach ($items as $item): ?>
            <article class="menu-card">
                <?php if (empty($item['url'])): ?>
                    <div class="menu-card-image">
                        <div class="menu-card-image-placeholder">Image</div>
                    </div>
                <?php else: ?>
                    <div class="menu-card-image">
                        <img src="<?= empty($item['url']) ? '' : htmlspecialchars((string)$item['url']) ?>" />
                    </div>                    
                <?php endif ?>
                <div class="menu-card-content">
                    <h3><?= htmlspecialchars((string)($item['display_name'] ?? '')) ?></h3>
                    <p><?= nl2br(htmlspecialchars((string)($item['description'] ?? ''))) ?></p>
                    <div class="menu-card-price">$<?= number_format((float)($item['price'] ?? 0), 2) ?></div>
                    <div class="menu-card-meta">Stock: <?= htmlspecialchars((string)($item['stock'] ?? '0')) ?></div>
                </div>
                <?php if ($isAdmin): ?>
                    <div class="menu-card-actions">
                        <a class="btn btn-secondary" href="/menuitems/edit?id=<?= urlencode((string)($item['id'] ?? '')) ?>">Edit</a>
                        <form method="post" action="/menuitems/delete" onsubmit="return confirm('Delete this menu item?');">
                            <input type="hidden" name="id" value="<?= htmlspecialchars((string)($item['id'] ?? '')) ?>">
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
