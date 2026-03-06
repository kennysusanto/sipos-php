<h1 class="page-title">Create Bill</h1>
<div class="page-subtitle">Create a bill with optional table number.</div>
<div class="page-header-actions">
    <a class="btn btn-secondary" href="/bills">Back to Bills</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<div class="dashboard-card form-card">
    <form method="post" action="/bills/store" class="app-form">
        <label for="table_id">Table ID (optional)</label>
        <input type="number" id="table_id" name="table_id" min="1" step="1" value="<?= htmlspecialchars((string)($old['table_id'] ?? '')) ?>">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Create Bill</button>
            <a class="btn btn-secondary" href="/bills">Cancel</a>
        </div>
    </form>
</div>
