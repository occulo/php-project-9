<?php
/** @var array $urls */
?>
<h1>Сайты</h1>
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
      <th scope="row"><?= $url['id'] ?></th>
      <td><a href="/urls/<?= $url['id'] ?>" aria-label="<?= $url['name'] ?>"><?= $url['name'] ?></a></td>
      <td>
        <?php if ($url['last_checked_at']) : ?>
            <?= date('d.m.Y H:i', strtotime($url['last_checked_at'])) ?>
        <?php else : ?>
          <span class="text-muted">---</span>
        <?php endif ?>
      </td>
      <td>
        <?php if ($url['status_code']) : ?>
            <?= $url['status_code'] ?>
        <?php else : ?>
          <span class="text-muted">---</span>
        <?php endif ?>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
</div>
