<h1 class="page-title">Create Tenant</h1>
<div class="page-subtitle">Create a new tenant for grouping users.</div>
<div class="page-header-actions">
    <a class="btn btn-secondary" href="/tenants">Back to Tenants</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<div class="dashboard-card form-card">
    <form method="post" action="/tenants/store" class="app-form">
        <label for="name">Name (internal)</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars((string)($old['name'] ?? '')) ?>">

        <label for="display_name">Display Name</label>
        <input type="text" id="display_name" name="display_name" required value="<?= htmlspecialchars((string)($old['display_name'] ?? '')) ?>">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Create Tenant</button>
            <a class="btn btn-secondary" href="/tenants">Cancel</a>
        </div>
    </form>
</div>
