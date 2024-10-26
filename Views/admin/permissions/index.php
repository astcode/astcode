<?php
/** @var $this \App\Core\View */
/** @var $permissions array */
use App\Core\Application;

$this->title = 'Permission Management';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Permissions</h3>
            <a href="/permissions/create" class="btn btn-primary">Create New Permission</a>
        </div>
        <div class="card-body">
            <?php if (empty($permissions)): ?>
                <p class="text-muted">No permissions found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permissions as $permission): ?>
                                <tr>
                                    <td><?= htmlspecialchars($permission->name) ?></td>
                                    <td><?= htmlspecialchars($permission->description) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/permissions/edit/<?= $permission->id ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <form method="POST" action="/permissions/delete/<?= $permission->id ?>" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= Application::$app->session->get('csrf_token') ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this permission?')">Delete</button>
                                            </form>
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
