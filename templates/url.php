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
