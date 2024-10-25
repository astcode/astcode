<?php
/** @var $model \App\Models\PasswordResetForm */
/** @var $token string */
/** @var $this \App\Core\View */

use App\Core\Form\Form;

$this->title = 'Reset Password';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Reset Password</h2>
                </div>
                <div class="card-body">
                    <?php $form = Form::begin('', 'post'); ?>
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <div class="mb-3">
                        <?php echo $form->field($model, 'password')->passwordField()->placeholder('Enter new password...'); ?>
                    </div>
                    <div class="mb-3">
                        <?php echo $form->field($model, 'confirmPassword')->passwordField()->placeholder('Confirm new password...'); ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                    </div>
                    <?php echo Form::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
