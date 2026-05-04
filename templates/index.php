<?php
/** @var \Slim\Routing\RouteParser $router */
?>
<h1 class="display-3">Анализатор страниц</h1>
<p class="fs-5 fw-light">
    Бесплатно проверяйте сайты на SEO-пригодность
</p>
<form action="<?= $router->urlFor('urls.store') ?>" method="POST">
    <div class="d-flex justify-content-between align-items-center">
        <input type="url" name="url" class="form-control me-2" placeholder="https://www.example.com" value="<?= htmlspecialchars($urlValue ?? '') ?>" aria-label="url" />
        <input type="submit" class="btn btn-primary" value="Проверить" />
    </div>
</form>
