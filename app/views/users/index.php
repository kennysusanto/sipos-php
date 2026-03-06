<h1 class="page-title">System Users</h1>
<div class="page-subtitle">Manage administrator and staff access accounts.</div>
<div class="page-header-actions">
    <a class="btn btn-primary" href="/users/create">Create User</a>
</div>

<?php if (!empty($status)): ?>
    <div class="alert alert-success">
        <?php if ($status === 'created'): ?>
            User created successfully.
        <?php elseif ($status === 'updated'): ?>
            User updated successfully.
        <?php elseif ($status === 'deleted'): ?>
            User deleted successfully.
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<?php if (empty($users)): ?>
    <div class="dashboard-card">
        <div class="card-title">No users found</div>
        <div class="card-value card-value-sm">Check DB connection and the user table records.</div>
    </div>
<?php else: ?>
    <div class="dashboard-card">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tenant</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($user['id'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($user['tenant_display_name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($user['username'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($user['email'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($user['role'] ?? '')) ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-secondary" href="/users/edit?id=<?= urlencode((string)($user['id'] ?? '')) ?>">Edit</a>
                                <form method="post" action="/users/delete" onsubmit="return confirm('Delete this user?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)($user['id'] ?? '')) ?>">
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
