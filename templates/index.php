<h1 class="display-3">Анализатор страниц</h1>
<p class="lead">
    Бесплатно проверяйте сайты на SEO-пригодность
</p>
<form action="/urls" method="POST">
    <div class="d-flex">
        <input type="url" name="url" class="form-control me-2" placeholder="https://www.example.com" value="<?= htmlspecialchars($urlValue ?? '') ?>" aria-label="url" />
        <input type="submit" class="btn btn-primary" value="Проверить" />
    </div>
</form>
