<?php

/** @var \Slim\Flash\Messages $flash */
/** @var array $errors */
?>
<?php foreach ($flash->getMessages() as $type => $messages) : ?>
    <?php foreach ($messages as $message) : ?>
        <div class="container alert alert-<?= $type ?: 'warning' ?>" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>
<?php foreach ($errors as $field => $fieldErrors) : ?>
    <?php foreach ($fieldErrors as $error) : ?>
        <div class="container alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
<?php endforeach; ?>
