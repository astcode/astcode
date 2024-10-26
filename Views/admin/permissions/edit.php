<?php
/** @var $this \App\Core\View */
/** @var $model \App\Models\Permission */
use App\Core\Application;
$this->title = 'Edit Permission';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Edit Permission: <?= htmlspecialchars($model->name) ?></h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" 
                       value="<?= Application::$app->session->get('csrf_token') ?>">
                
                <div class="mb-3">
                    <label class="form-label">Permission Name</label>
                    <input type="text" name="name" 
                           value="<?= htmlspecialchars($model->name) ?>"
                           class="form-control <?= $model->hasError('name') ? 'is-invalid' : '' ?>"
                           placeholder="e.g., users.create">
                    <div class="invalid-feedback">
                        <?= $model->getFirstError('name') ?>
                    </div>
                    <small class="form-text text-muted">
                        Use format: resource.action (e.g., users.create, posts.edit)
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                            class="form-control <?= $model->hasError('description') ? 'is-invalid' : '' ?>"
                            rows="3"
                            placeholder="Describe what this permission allows"><?= htmlspecialchars($model->description) ?></textarea>
                    <div class="invalid-feedback">
                        <?= $model->getFirstError('description') ?>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Permission</button>
                    <a href="/permissions" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
