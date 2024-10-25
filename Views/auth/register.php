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
                        </div>
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'lastname')->placeholder('Enter your Last Name...'); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'username')->placeholder('Choose a Username...'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'email')->fieldType('email')->placeholder('Enter your Email Address...'); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'password')->passwordField()->placeholder('Choose a Password...'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'confirmPassword')->passwordField()->placeholder('Confirm your Password...'); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check">
                                <?php echo $form->checkboxField($model, 'agreeTerms', 'I agree to the Terms and Conditions'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                        </div>
                    </div>
                    <?php echo Form::end(); ?>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="/login">Log in</a></p>
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
