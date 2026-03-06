<h1 class="page-title">Create User</h1>
<div class="page-subtitle">Add a new account and assign a role.</div>
<div class="page-header-actions">
    <a class="btn btn-secondary" href="/users">Back to Users</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars((string)$error) ?></div>
<?php endif; ?>

<div class="dashboard-card form-card">
    <form method="post" action="/users/store" class="app-form">
        <label for="tenant_id">Tenant</label>
        <select id="tenant_id" name="tenant_id" required>
            <option value="">Select tenant</option>
            <?php $selectedTenantId = (string)($old['tenant_id'] ?? ''); ?>
            <?php foreach (($tenants ?? []) as $tenant): ?>
                <?php $tenantId = (string)($tenant['id'] ?? ''); ?>
                <option value="<?= htmlspecialchars($tenantId) ?>" <?= $selectedTenantId === $tenantId ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)($tenant['display_name'] ?? '')) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required value="<?= htmlspecialchars((string)($old['username'] ?? '')) ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars((string)($old['email'] ?? '')) ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <?php $selectedRole = $old['role'] ?? 'user'; ?>
            <option value="user" <?= $selectedRole === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Create User</button>
            <a class="btn btn-secondary" href="/users">Cancel</a>
        </div>
    </form>
</div>
