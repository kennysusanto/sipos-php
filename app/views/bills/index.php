<h1 class="page-title">Bills</h1>
<div class="page-subtitle">Manage billing records and open bill details.</div>
<div class="page-header-actions">
    <a class="btn btn-primary" href="/bills/create">Create Bill</a>
</div>

<?php if (!empty($status)): ?>
    <div class="alert alert-success">
        <?php if ($status === 'created'): ?>
            Bill created successfully.
        <?php elseif ($status === 'updated'): ?>
            Bill updated successfully.
        <?php elseif ($status === 'deleted'): ?>
            Bill deleted successfully.
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<?php if (empty($bills)): ?>
    <div class="dashboard-card">
        <div class="card-title">No bills found</div>
        <div class="card-value card-value-sm">Create a bill to start adding bill items.</div>
    </div>
<?php else: ?>
    <div class="dashboard-card">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Table ID</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bills as $bill): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($bill['id'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($bill['table_id'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($bill['created_at'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($bill['updated_at'] ?? '')) ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-secondary" href="/bills/detail?bill_id=<?= urlencode((string)($bill['id'] ?? '')) ?>">Detail</a>
                                <a class="btn btn-secondary" href="/bills/edit?id=<?= urlencode((string)($bill['id'] ?? '')) ?>">Edit</a>
                                <form method="post" action="/bills/delete" onsubmit="return confirm('Delete this bill?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)($bill['id'] ?? '')) ?>">
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
