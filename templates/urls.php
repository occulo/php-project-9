<table data-test="urls">
  <thead>
    <tr>
      <th>ID</th>
      <th>Имя</th>
      <th>Дата создания</th>
      <th>Код ответа</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($urls as $url): ?>
    <tr>
      <td><?= $url['id'] ?></td>
      <td><a href="/urls/<?= $url['id'] ?>" aria-label="<?= $url['name'] ?>"><?= $url['name'] ?></a></td>
      <td><?= $url['created_at'] ?></td>
      <td><?= $url['status_code'] ?></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
