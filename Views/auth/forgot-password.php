<?php
/** @var $model \App\Models\PasswordResetForm */
/** @var $this \App\Core\View */

use App\Core\Form\Form;

$this->title = 'Forgot Password';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Forgot Password</h2>
                </div>
                <div class="card-body">
                    <?php $form = Form::begin('', 'post'); ?>
                    <div class="mb-3">
                        <?php echo $form->field($model, 'email')->fieldType('email')->placeholder('Enter your Email Address...'); ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                    </div>
                    <?php echo Form::end(); ?>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>Remember your password? <a href="/login">Login here</a></p>
            </div>
        </div>
    </div>
</div>
