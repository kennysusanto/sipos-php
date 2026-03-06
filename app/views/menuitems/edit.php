<h1 class="page-title">Edit Menu Item</h1>
<div class="page-subtitle">Update menu item details and inventory.</div>
<div class="page-header-actions">
    <a class="btn btn-secondary" href="/menuitems">Back to Menu Items</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<div class="dashboard-card form-card">
    <form method="post" action="/menuitems/update" class="app-form">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($item['id'] ?? '')) ?>">
        <input type="hidden" name="tenant_id" value="<?= htmlspecialchars((string)($_SESSION['tenant_id'] ?? ($item['tenant_id'] ?? ''))) ?>">

        <label for="display_name">Display Name</label>
        <input type="text" id="display_name" name="display_name" required value="<?= htmlspecialchars((string)($item['display_name'] ?? '')) ?>">

        <label for="name">Name (internal)</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars((string)($item['name'] ?? '')) ?>">

        <label for="url">URL</label>
        <input type="url" id="url" name="url" value="<?= htmlspecialchars((string)($item['url'] ?? '')) ?>" placeholder="https://example.com/item-image-or-page">

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" required><?= htmlspecialchars((string)($item['description'] ?? '')) ?></textarea>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars((string)($item['price'] ?? '0')) ?>">

        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" min="0" step="1" required value="<?= htmlspecialchars((string)($item['stock'] ?? '0')) ?>">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update Menu Item</button>
            <a class="btn btn-secondary" href="/menuitems">Cancel</a>
        </div>
    </form>
</div>
