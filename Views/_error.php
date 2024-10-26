<?php
/** @var $exception \Exception */
?>

<div class="container mt-5">
    <div class="alert alert-danger">
        <h3>
            <?php 
            if ($exception instanceof \App\Core\Exception\ForbiddenException) {
                echo 'Access Denied';
            } else {
                echo get_class($exception);
            }
            ?>
        </h3>
        <p><?= $exception->getMessage() ?></p>
    </div>
    <a href="/" class="btn btn-primary">Go Back Home</a>
</div>
