<?php if (isset($flash)) : ?>
    <?php foreach ($flash as $type => $messages) :  ?>
        <?php foreach ($messages as $message) : ?>
            <div class="container alert alert-<?= $type ?: 'warning' ?>" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
