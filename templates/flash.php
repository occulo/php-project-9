<?php if (isset($flash)): ?>
    <?php $data = $flash->getMessages(); ?>
    <?php foreach ($data as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <div class="container alert alert-<?= $type ?>" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
