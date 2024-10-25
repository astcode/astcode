<?php

/** @var $model \App\Models\SurveyForm */

use App\Core\Form\Form;

$this->title = 'Favorite Movie Survey';
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Favorite Movie Survey</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php $form = Form::begin('/survey', 'post'); ?>

            <!-- Full Name -->
            <?php
                echo $form->field($model, 'name', 'text', [
                    'placeholder' => 'Enter your full name',
                    'class' => 'form-control',
                ]);
                if ($model->hasError('name')) {
                    echo sprintf('<div class="invalid-feedback d-block">%s</div>', htmlspecialchars($model->getFirstError('name'), ENT_QUOTES, 'UTF-8'));
                }
            ?>

            <!-- Email Address -->
            <?php
                echo $form->field($model, 'email', 'email', [
                    'placeholder' => 'Enter your email address',
                    'class' => 'form-control',
                ]);
                if ($model->hasError('email')) {
                    echo sprintf('<div class="invalid-feedback d-block">%s</div>', htmlspecialchars($model->getFirstError('email'), ENT_QUOTES, 'UTF-8'));
                }
            ?>

            <!-- Favorite Genre -->
            <div class="mb-3">
                <label for="genre" class="form-label">Favorite Genre</label>
                <select name="genre" id="genre" class="form-select">
                    <option value="">Select your favorite genre</option>
                    <option value="action" <?php echo ($model->genre === 'action') ? 'selected' : ''; ?>>Action</option>
                    <option value="comedy" <?php echo ($model->genre === 'comedy') ? 'selected' : ''; ?>>Comedy</option>
                    <option value="drama" <?php echo ($model->genre === 'drama') ? 'selected' : ''; ?>>Drama</option>
                    <option value="horror" <?php echo ($model->genre === 'horror') ? 'selected' : ''; ?>>Horror</option>
                    <option value="sci-fi" <?php echo ($model->genre === 'sci-fi') ? 'selected' : ''; ?>>Sci-Fi</option>
                    <option value="romance" <?php echo ($model->genre === 'romance') ? 'selected' : ''; ?>>Romance</option>
                    <option value="documentary" <?php echo ($model->genre === 'documentary') ? 'selected' : ''; ?>>Documentary</option>
                    <option value="other" <?php echo ($model->genre === 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
                <?php if ($model->hasError('genre')): ?>
                    <div class="invalid-feedback d-block">
                        <?php echo htmlspecialchars($model->getFirstError('genre'), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Preferred Streaming Service -->
            <div class="mb-3">
                <label class="form-label">Preferred Streaming Service</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="streaming" id="streaming_netflix" value="netflix" <?php echo ($model->streaming === 'netflix') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="streaming_netflix">Netflix</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="streaming" id="streaming_amazon" value="amazon_prime" <?php echo ($model->streaming === 'amazon_prime') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="streaming_amazon">Amazon Prime</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="streaming" id="streaming_hulu" value="hulu" <?php echo ($model->streaming === 'hulu') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="streaming_hulu">Hulu</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="streaming" id="streaming_disney" value="disney_plus" <?php echo ($model->streaming === 'disney_plus') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="streaming_disney">Disney+</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="streaming" id="streaming_hbo" value="hbo_max" <?php echo ($model->streaming === 'hbo_max') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="streaming_hbo">HBO Max</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="streaming" id="streaming_other" value="other" <?php echo ($model->streaming === 'other') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="streaming_other">Other</label>
                </div>
                <?php if ($model->hasError('streaming')): ?>
                    <div class="invalid-feedback d-block">
                        <?php echo htmlspecialchars($model->getFirstError('streaming'), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Favorite Actors -->
            <div class="mb-3">
                <label class="form-label">Favorite Actors</label>
                <?php
                    echo $form->checkboxField($model, 'actors', 'Leonardo DiCaprio', 'leonardo_dicaprio', true);
                    echo $form->checkboxField($model, 'actors', 'Scarlett Johansson', 'scarlett_johansson', true);
                    echo $form->checkboxField($model, 'actors', 'Denzel Washington', 'denzel_washington', true);
                    echo $form->checkboxField($model, 'actors', 'Meryl Streep', 'meryl_streep', true);
                    echo $form->checkboxField($model, 'actors', 'Other', 'other', true);
                ?>
                <?php if ($model->hasError('actors')): ?>
                    <div class="invalid-feedback d-block">
                        <?php echo htmlspecialchars($model->getFirstError('actors'), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Additional Comments -->
            <?php
                echo $form->textAreaField($model, 'comments', [
                    'placeholder' => 'Share your thoughts or suggestions',
                    'class' => 'form-control',
                    'rows' => 5,
                ]);
                if ($model->hasError('comments')) {
                    echo sprintf('<div class="invalid-feedback d-block">%s</div>', htmlspecialchars($model->getFirstError('comments'), ENT_QUOTES, 'UTF-8'));
                }
            ?>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100 mt-3">Submit Survey</button>

            <?php Form::end(); ?>
        </div>
    </div>
</div>
