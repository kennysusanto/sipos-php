<h1 class="page-title">Tenants</h1>
<div class="page-subtitle">Manage tenant organizations for user isolation.</div>
<div class="page-header-actions">
    <a class="btn btn-primary" href="/tenants/create">Create Tenant</a>
</div>

<?php if (!empty($status)): ?>
    <div class="alert alert-success">
        <?php if ($status === 'created'): ?>
            Tenant created successfully.
        <?php elseif ($status === 'updated'): ?>
            Tenant updated successfully.
        <?php elseif ($status === 'deleted'): ?>
            Tenant deleted successfully.
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<?php if (empty($tenants)): ?>
    <div class="dashboard-card">
        <div class="card-title">No tenants found</div>
        <div class="card-value card-value-sm">Create your first tenant to assign users.</div>
    </div>
<?php else: ?>
    <div class="dashboard-card">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Display Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $tenant): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($tenant['id'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($tenant['name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($tenant['display_name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($tenant['created_at'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($tenant['updated_at'] ?? '')) ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-secondary" href="/tenants/edit?id=<?= urlencode((string)($tenant['id'] ?? '')) ?>">Edit</a>
                                <form method="post" action="/tenants/delete" onsubmit="return confirm('Delete this tenant?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)($tenant['id'] ?? '')) ?>">
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
