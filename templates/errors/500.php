<?php
/** @var \Slim\Routing\RouteParser $router */
?>

<div class="d-flex justify-content-center align-items-center">
    <div class="text-center">
        <h1 class="display-3 fw-bold">500</h1>
        <h2 class="fs-2">Внутренняя ошибка сервера</h2>
        <p class="fs-5 fw-light mt-3 mb-4">Что-то пошло не так. Попробуйте позже.</p>
        <a href="<?= $router->urlFor('home') ?>" class="btn btn-primary">
            На Главную
        </a>
    </div>
</div>
