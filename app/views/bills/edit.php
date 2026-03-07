<h1 class="page-title">Edit Bill</h1>
<div class="page-subtitle">Update optional table assignment for this bill.</div>
<div class="page-header-actions">
    <a class="btn btn-secondary" href="/bills">Back to Bills</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<div class="dashboard-card form-card">
    <form method="post" action="/bills/update" class="app-form">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($bill['id'] ?? '')) ?>">

        <label for="table_id">Table ID (optional)</label>
        <input type="number" id="table_id" name="table_id" min="1" step="1" value="<?= htmlspecialchars((string)($bill['table_id'] ?? '')) ?>">

        <label for="note">Note (optional)</label>
        <input type="text" id="note" name="note" maxlength="255" value="<?= htmlspecialchars((string)($bill['note'] ?? '')) ?>">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update Bill</button>
            <a class="btn btn-secondary" href="/bills">Cancel</a>
        </div>
    </form>
</div>
