<?php
/** @var \Slim\Routing\RouteParser $router */
/** @var array $url */
/** @var array $checks */
?>
<h1 class="fw-normal">Сайт: <?= htmlspecialchars($url['name']) ?></h1>
<div class="table-responsive my-3">
  <table class="table table-bordered table-hover text-nowrap" data-test="url">
    <tbody>
      <tr>
        <th scope="row">ID</th>
        <td><?= $url['id'] ?></td>
      </tr>
      <tr>
        <th scope="row">Имя</th>
        <td>
          <a href="<?= htmlspecialchars($url['name']) ?>" aria-label="<?= htmlspecialchars($url['name']) ?>"><?= htmlspecialchars($url['name']) ?></a>
        </td>
      </tr>
      <tr>
        <th scope="row">Дата создания</th>
        <td><?= date('d.m.Y H:i', strtotime($url['created_at'])); ?></td>
      </tr>
    </tbody>
  </table>
</div>
<div class="d-flex justify-content-between align-items-center">
  <h2 class="fw-normal">Проверки</h2>
  <form method="post" action="<?= $router->urlFor('url.check.store', ['url_id' => (string) $url['id']]) ?>">
    <input type="submit" class="btn btn-primary" value="Запустить проверку">
  </form>
</div>
<div class="table-responsive mt-3">
  <table class="table table-bordered table-hover mt-2" data-test="checks">
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th class="text-nowrap" scope="col">Код ответа</th>
        <th scope="col">h1</th>
        <th scope="col">title</th>
        <th scope="col">description</th>
        <th class="text-nowrap" scope="col">Дата создания</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($checks as $check) : ?>
      <tr>
        <th scope="row"><?= $check['id'] ?></th>
        <td><?= $check['status_code'] ?></td>
        <td><?= htmlspecialchars(truncate($check['h1'])) ?></td>
        <td><?= htmlspecialchars(truncate($check['title'])) ?></td>
        <td><?= htmlspecialchars(truncate($check['description'])) ?></td>
        <td><?= date('d.m.Y H:i', strtotime($check['created_at'])) ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>
