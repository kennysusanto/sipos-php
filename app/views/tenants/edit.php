<h1 class="page-title">Edit Tenant</h1>
<div class="page-subtitle">Update tenant naming details.</div>
<div class="page-header-actions">
    <a class="btn btn-secondary" href="/tenants">Back to Tenants</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<div class="dashboard-card form-card">
    <form method="post" action="/tenants/update" class="app-form">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($tenant['id'] ?? '')) ?>">

        <label for="name">Name (internal)</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars((string)($tenant['name'] ?? '')) ?>">

        <label for="display_name">Display Name</label>
        <input type="text" id="display_name" name="display_name" required value="<?= htmlspecialchars((string)($tenant['display_name'] ?? '')) ?>">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update Tenant</button>
            <a class="btn btn-secondary" href="/tenants">Cancel</a>
        </div>
    </form>
</div>
