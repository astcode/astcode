<?php
/** User Aaron Thomas */
/** @var \App\Core\Controller $this */
/** @var \Exception $exception */


$this->layout = 'error';
?>



<h1>Error Page</h1>

<h3><?= $exception->getCode() ?> - <?= $exception->getMessage() ?></h3>
