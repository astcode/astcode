<?php
/** @var $this \App\Core\View */
/** @var $apiKeys array */
use App\Core\Application;

$this->title = 'API Keys';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Your API Keys</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKeyModal">
                        Create New API Key
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($apiKeys)): ?>
                        <p class="text-muted">You haven't created any API keys yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Key</th>
                                        <th>Created</th>
                                        <th>Last Used</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($apiKeys as $key): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($key['name']) ?></td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="<?= htmlspecialchars($key['key']) ?>" readonly>
                                                    <button class="btn btn-outline-secondary copy-btn" type="button" data-key="<?= htmlspecialchars($key['key']) ?>">
                                                        Copy
                                                    </button>
                                                </div>
                                            </td>
                                            <td><?= $key['created_at'] ?></td>
                                            <td><?= $key['last_used_at'] ?? 'Never' ?></td>
                                            <td>
                                                <?php if ($key['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="POST" action="/api/v1/keys/delete" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Application::$app->session->get('csrf_token'); ?>">
                                                    <input type="hidden" name="id" value="<?= $key['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this API key?')">
                                                        Delete
                                                    </button>
                                                </form>
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
    </div>
</div>

<!-- Create API Key Modal -->
<div class="modal fade" id="createKeyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New API Key</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createKeyForm" method="POST" action="/api/v1/keys">
                    <input type="hidden" name="csrf_token" value="<?php echo Application::$app->session->get('csrf_token'); ?>">
                    <div class="mb-3">
                        <label for="keyName" class="form-label">Key Name</label>
                        <input type="text" class="form-control" id="keyName" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="createKeyForm" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.copy-btn').forEach(button => {
    button.addEventListener('click', function() {
        const key = this.dataset.key;
        navigator.clipboard.writeText(key).then(() => {
            const originalText = this.textContent;
            this.textContent = 'Copied!';
            setTimeout(() => {
                this.textContent = originalText;
            }, 2000);
        });
    });
});
</script>
