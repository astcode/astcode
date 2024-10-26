<?php
/** @var $this \App\Core\View */
/** @var $roles array */
/** @var $permissions array */
use App\Core\Application;

$this->title = 'Role Management';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Role Management</h3>
            <a href="/roles/create" class="btn btn-primary">Create New Role</a>
        </div>
        <div class="card-body">
            <?php if (empty($roles)): ?>
                <p class="text-muted">No roles found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role): ?>
                                <tr>
                                    <td><?= htmlspecialchars($role->name) ?></td>
                                    <td><?= htmlspecialchars($role->description) ?></td>
                                    <td>
                                        <?php 
                                        $permissions = $role->getPermissions();
                                        foreach ($permissions as $permission): ?>
                                            <span class="badge bg-info me-1">
                                                <?= htmlspecialchars($permission['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/roles/edit/<?= $role->id ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <?php if (!in_array($role->name, ['super_admin', 'admin', 'user'])): ?>
                                                <form method="POST" action="/roles/delete/<?= $role->id ?>" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= Application::$app->session->get('csrf_token') ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
