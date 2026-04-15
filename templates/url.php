<h1><?= $url['name'] ?></h1>
<table data-test="url">
  <tbody>
    <tr>
      <th>ID</th>
      <td><?= $url['id'] ?></td>
    </tr>
    <tr>
      <th>Имя</th>
      <td><a href="<?= $url['name'] ?>" aria-label="<?= $url['name'] ?>"><?= $url['name'] ?></a></td>
    </tr>
    <tr>
      <th>Дата создания</th>
      <td><?= $url['created_at'] ?></td>
    </tr>
  </tbody>
</table>
<form method="post" action="/urls/<?= $url['id'] ?>/checks">
  <input type="submit" value="Запустить проверку">
</form>
<table data-test="checks">
  <thead>
    <tr>
      <th>ID</th>
      <th>Код ответа</th>
      <th>h1</th>
      <th>title</th>
      <th>description</th>
      <th>Дата создания</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($checks as $check): ?>
    <tr>
      <td><?= $check['id'] ?></td>
      <td><?= $check['status_code'] ?></td>
      <td><?= $check['h1'] ?></td>
      <td><?= $check['title'] ?></td>
      <td><?= $check['description'] ?></td>
      <td><?= $check['created_at'] ?></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>


