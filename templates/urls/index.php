<?php

/** @var \Slim\Routing\RouteParser $router */
/** @var array $urls */
?>
<h1 class="fw-normal">Сайты</h1>
<div class="table-responsive-sm mt-3">
<table class="table table-bordered table-hover" data-test="urls">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Имя</th>
      <th class="text-nowrap" scope="col">Последняя проверка</th>
      <th class="text-nowrap" scope="col">Код ответа</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($urls as $url) : ?>
    <tr>
      <th scope="row"><?= htmlspecialchars((string) $url['id']) ?></th>
      <td>
          <a href="<?= $router->urlFor('url', ['url_id' => (string) $url['id']]) ?>"
              aria-label="<?= htmlspecialchars($url['name']) ?>">
            <?= htmlspecialchars($url['name']) ?>
          </a>
      </td>
      <td>
          <?php if (isset($url['last_checked_at'])) : ?>
                <?= htmlspecialchars(date('d.m.Y H:i', strtotime($url['last_checked_at']))) ?>
          <?php else : ?>
                <span class="text-muted">---</span>
          <?php endif ?>
      </td>
      <td>
          <?php if (isset($url['status_code'])) : ?>
                <?= htmlspecialchars((string) $url['status_code']) ?>
          <?php else : ?>
                <span class="text-muted">---</span>
          <?php endif ?>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
</div>
