<h1>Сайт: <?= $url['name'] ?></h1>
<div class="table-responsive my-3">
  <table class="table table-bordered table-hover text-nowrap" data-test="url">
    <tbody>
      <tr>
        <th scope="row">ID</th>
        <td><?= $url['id'] ?></td>
      </tr>
      <tr>
        <th scope="row">Имя</th>
        <td><a href="<?= $url['name'] ?>" aria-label="<?= $url['name'] ?>"><?= $url['name'] ?></a></td>
      </tr>
      <tr>
        <th scope="row">Дата создания</th>
        <td><?= date('d.m.Y H:i', strtotime($url['created_at'])); ?></td>
      </tr>
    </tbody>
  </table>
</div>
<div class="d-flex justify-content-between align-items-center">
  <h3>Проверки</h3>
  <form method="post" action="/urls/<?= $url['id'] ?>/checks">
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
        <td><?= truncate($check['h1']) ?></td>
        <td><?= truncate($check['title']) ?></td>
        <td><?= truncate($check['description']) ?></td>
        <td><?= date('d.m.Y H:i', strtotime($check['created_at'])) ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>
