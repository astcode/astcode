<?php
/** @var $this \App\Core\View */
/** @var $model \App\Models\Role */
/** @var $permissions array */
use App\Core\Application;
$this->title = 'Create Role';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Create New Role</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" 
                       value="<?= Application::$app->session->get('csrf_token') ?>">
                
                <div class="mb-3">
                    <label class="form-label">Role Name</label>
                    <input type="text" name="name" 
                           value="<?= htmlspecialchars($model->name) ?>"
                           class="form-control <?= $model->hasError('name') ? 'is-invalid' : '' ?>">
                    <div class="invalid-feedback">
                        <?= $model->getFirstError('name') ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                            class="form-control <?= $model->hasError('description') ? 'is-invalid' : '' ?>"
                            rows="3"><?= htmlspecialchars($model->description) ?></textarea>
                    <div class="invalid-feedback">
                        <?= $model->getFirstError('description') ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <?php foreach ($permissions as $group => $groupPermissions): ?>
                        <div class="card mb-2">
                            <div class="card-header">
                                <h5 class="mb-0"><?= ucfirst($group) ?></h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($groupPermissions as $permission): ?>
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="<?= $permission->id ?>"
                                               class="form-check-input"
                                               id="perm_<?= $permission->id ?>">
                                        <label class="form-check-label" 
                                               for="perm_<?= $permission->id ?>">
                                            <?= htmlspecialchars($permission->description) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Create Role</button>
                    <a href="/roles" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
