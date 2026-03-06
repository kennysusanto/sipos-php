<h1 class="page-title">Bill #<?= htmlspecialchars((string)($bill['id'] ?? '')) ?> Detail</h1>
<div class="page-subtitle">List of bill items for this bill only.</div>
<div class="page-header-actions">
    <a class="btn btn-primary" href="/billitems/create?bill_id=<?= urlencode((string)($bill['id'] ?? '')) ?>">Add Bill Item</a>
    <a class="btn btn-secondary" href="/bills">Back to Bills</a>
</div>

<?php if (!empty($status)): ?>
    <div class="alert alert-success">
        <?php if ($status === 'created'): ?>
            Bill item added successfully.
        <?php elseif ($status === 'updated'): ?>
            Bill item updated successfully.
        <?php elseif ($status === 'deleted'): ?>
            Bill item deleted successfully.
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<?php if (empty($items)): ?>
    <div class="dashboard-card">
        <div class="card-title">No bill items found</div>
        <div class="card-value card-value-sm">Add an item to this bill.</div>
    </div>
<?php else: ?>
    <div class="dashboard-card">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Menu Item</th>
                    <th>Quantity</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($item['id'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($item['menuitem_display_name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($item['quantity'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($item['created_at'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($item['updated_at'] ?? '')) ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-secondary" href="/billitems/edit?id=<?= urlencode((string)($item['id'] ?? '')) ?>">Edit</a>
                                <form method="post" action="/billitems/delete" onsubmit="return confirm('Delete this bill item?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)($item['id'] ?? '')) ?>">
                                    <input type="hidden" name="bill_id" value="<?= htmlspecialchars((string)($bill['id'] ?? '')) ?>">
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
