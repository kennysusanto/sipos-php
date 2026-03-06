<h1 class="page-title">Add Bill Item</h1>
<div class="page-subtitle">Bill #<?= htmlspecialchars((string)($bill['id'] ?? '')) ?>.</div>
<div class="page-header-actions">
    <a class="btn btn-secondary" href="/bills/detail?bill_id=<?= urlencode((string)($bill['id'] ?? '')) ?>">Back to Bill Detail</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<div class="dashboard-card form-card">
    <form method="post" action="/billitems/store" class="app-form">
        <input type="hidden" name="bill_id" value="<?= htmlspecialchars((string)($bill['id'] ?? '')) ?>">

        <label for="menuitem_id">Menu Item</label>
        <select id="menuitem_id" name="menuitem_id" required>
            <option value="">Select menu item</option>
            <?php $selectedMenuitemId = (string)($old['menuitem_id'] ?? ''); ?>
            <?php foreach (($menuItems ?? []) as $menuItem): ?>
                <?php $menuItemId = (string)($menuItem['id'] ?? ''); ?>
                <option value="<?= htmlspecialchars($menuItemId) ?>" <?= $selectedMenuitemId === $menuItemId ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)($menuItem['display_name'] ?? '')) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" min="1" step="1" required value="<?= htmlspecialchars((string)($old['quantity'] ?? '1')) ?>">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Add Item</button>
            <a class="btn btn-secondary" href="/bills/detail?bill_id=<?= urlencode((string)($bill['id'] ?? '')) ?>">Cancel</a>
        </div>
    </form>
</div>
