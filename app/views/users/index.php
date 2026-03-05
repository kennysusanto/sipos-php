<h1>System Users</h1>
<?php if (empty($users)): ?>
    <div class="dashboard-card">
        <div class="card-title">No users found</div>
        <div class="card-value" style="font-size: 1rem;">Check DB connection and the `user` table records.</div>
    </div>
<?php else: ?>
    <div class="dashboard-card">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($user['id'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($user['username'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($user['role'] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
