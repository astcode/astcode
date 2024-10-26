<?php

/** @var $model \App\Models\User */

use App\Core\Form\Form;

/** @var $this \App\Core\View */
$this->title = 'Register';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Create an Account</h2>
                </div>
                <div class="card-body">
                    <?php $form = Form::begin('', 'post'); ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'firstname')->placeholder('Enter your First Name...'); ?>
                            <?php if ($model->hasError('firstname')): ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo $model->getFirstError('firstname') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'lastname')->placeholder('Enter your Last Name...'); ?>
                            <?php if ($model->hasError('lastname')): ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo $model->getFirstError('lastname') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'username')->placeholder('Choose a Username...'); ?>
                            <?php if ($model->hasError('username')): ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo $model->getFirstError('username') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'email')->fieldType('email')->placeholder('Enter your Email...'); ?>
                            <?php if ($model->hasError('email')): ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo $model->getFirstError('email') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'password')->passwordField()->placeholder('Choose a Password...'); ?>
                            <?php if ($model->hasError('password')): ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo $model->getFirstError('password') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'confirmPassword')->passwordField()->placeholder('Confirm Password...'); ?>
                            <?php if ($model->hasError('confirmPassword')): ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo $model->getFirstError('confirmPassword') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <?php echo $form->checkboxField($model, 'agreeTerms', 'I agree to the Terms and Conditions'); ?>
                        <?php if ($model->hasError('agreeTerms')): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo $model->getFirstError('agreeTerms') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                    <?php echo Form::end(); ?>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="/login">Login here</a></p>
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
