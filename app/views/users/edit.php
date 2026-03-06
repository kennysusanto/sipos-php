<h1 class="page-title">Edit User</h1>
<div class="page-subtitle">Update user identity, credentials, and role.</div>
<div class="page-header-actions">
    <a class="btn btn-secondary" href="/users">Back to Users</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<div class="dashboard-card form-card">
    <form method="post" action="/users/update" class="app-form">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($user['id'] ?? '')) ?>">

        <label for="tenant_id">Tenant</label>
        <select id="tenant_id" name="tenant_id" required>
            <?php $selectedTenantId = (string)($user['tenant_id'] ?? ''); ?>
            <?php foreach (($tenants ?? []) as $tenant): ?>
                <?php $tenantId = (string)($tenant['id'] ?? ''); ?>
                <option value="<?= htmlspecialchars($tenantId) ?>" <?= $selectedTenantId === $tenantId ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)($tenant['display_name'] ?? '')) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required value="<?= htmlspecialchars((string)($user['username'] ?? '')) ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars((string)($user['email'] ?? '')) ?>">

        <label for="password">New Password (leave empty to keep current)</label>
        <input type="password" id="password" name="password">

        <label for="role">Role</label>
        <?php $selectedRole = $user['role'] ?? 'user'; ?>
        <select id="role" name="role" required>
            <option value="user" <?= $selectedRole === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Update User</button>
            <a class="btn btn-secondary" href="/users">Cancel</a>
        </div>
    </form>
</div>
