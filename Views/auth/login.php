<?php

/** @var $model \App\Models\LoginForm */

use App\Core\Form\Form;

/** @var $this \App\Core\View */
$this->title = 'Login';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Login</h2>
                </div>
                <div class="card-body">
                    <?php $form = Form::begin('', 'post'); ?>
                    <div class="mb-3">
                        <?php echo $form->field($model, 'email')->fieldType('email')->placeholder('Enter your Email Address...'); ?>
                        <?php if ($model->hasError('email')): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo $model->getFirstError('email') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <?php echo $form->field($model, 'password')->passwordField()->placeholder('Enter your Password...'); ?>
                        <?php if ($model->hasError('password')): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo $model->getFirstError('password') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 form-check">
                        <?php echo $form->checkboxField($model, 'rememberMe', 'Remember Me'); ?>
                    </div>
                    <div class="mb-3">
                        <a href="/forgot-password">Forgot Password?</a>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                    <?php echo Form::end(); ?>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="/register">Register here</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 15px;
    }
    .card-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .btn-primary {
        border-radius: 25px;
        padding: 10px 20px;
    }
    .form-control {
        border-radius: 25px;
    }
    .form-check-label {
        font-size: 0.9rem;
    }
</style>
