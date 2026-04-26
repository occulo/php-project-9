<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DI\ContainerBuilder;
use Slim\Routing\RouteContext;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Slim\Flash\Messages;
use Valitron\Validator;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Hexlet\Code\Database;
use Hexlet\Code\UrlRepository;
use Hexlet\Code\CheckRepository;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Database
$dbUrl = getenv('DATABASE_URL');
if (!is_string($dbUrl)) {
    throw new \Exception("DATABASE_URL is not set");
}
$pdo = Database::connect($dbUrl);
$urlRepo = new UrlRepository($pdo);
$checkRepo = new CheckRepository($pdo);

// Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    Messages::class => function () {
        return new Messages($_SESSION);
    }
]);
AppFactory::setContainer($containerBuilder->build());

// App
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

// Renderer
$renderer = new PhpRenderer(__DIR__ . '/../templates');
$renderer->setLayout('layout.php');

// Guzzle
$client = new Client();

// Routes
$app->get('/', function (Request $request, Response $response, $args) use ($renderer) {
    $flash = $this->get(Messages::class);
    return $renderer->render($response, 'index.php', [
        'title' => 'Анализатор страниц',
        'flash' => $flash->getMessages()
    ]);
});

$app->get('/urls', function (Request $request, Response $response, $args) use ($renderer, $urlRepo) {
    $flash = $this->get(Messages::class);
    $urls = $urlRepo->getAll();
    return $renderer->render($response, 'urls.php', [
        'title' => 'Анализатор страниц - Сайты',
        'urls' => $urls,
        'flash' => $flash->getMessages()
    ]);
});

$app->post('/urls', function (Request $request, Response $response, $args) use ($renderer, $urlRepo) {
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    $flash = $this->get(Messages::class);
    $data = $request->getParsedBody();
    $data = is_array($data) ? $data : [];

    $validator = new Validator($data);
    $validator->rule('required', 'url')->message('URL не должен быть пустым');
    $validator->rule('lengthMax', 'url', 255)->message('URL превышает 255 символов');
    $validator->rule('url', 'url')->message('Некорректный URL');

    $url = trim($data['url'] ?? '');

    if (!$validator->validate()) {
        $errors = $validator->errors();
        if (!is_array($errors)) {
            $errors = [];
        }
        $validatorErrors = array_merge(...array_values($errors));
        return $renderer->render($response, 'index.php', [
            'title' => 'Анализатор страниц',
            'urlValue' => $url,
            'flash' => ['danger' => $validatorErrors]
        ])->withStatus(422);
    }

    $parsedUrl = parse_url($url);
    if (!isset($parsedUrl['scheme'], $parsedUrl['host'])) {
        throw new \Exception("URL is invalid");
    }
    $normalizedUrl = sprintf("%s://%s", $parsedUrl['scheme'], $parsedUrl['host']);

    if ($existing = $urlRepo->getByName($normalizedUrl)) {
        $flash->addMessage('warning', 'Страница уже существует');
        return $response->withStatus(302)->withHeader(
            'Location',
            $routeParser->urlFor('url', ['url_id' => (string) $existing['id']])
        );
    }

    $id = $urlRepo->insert($normalizedUrl);
    $flash->addMessage('success', 'Страница успешно добавлена');
    return $response->withStatus(302)->withHeader('Location', $routeParser->urlFor('url', ['url_id' => (string) $id]));
});

$app->get('/urls/{url_id:[0-9]+}', function (
    Request $request,
    Response $response,
    array $args
) use (
    $renderer,
    $urlRepo,
    $checkRepo
) {
    $flash = $this->get(Messages::class);
    $url = $urlRepo->getById($args['url_id']);
    if ($url === null) {
        throw new \Exception("URL not found");
    }
    $checks = $checkRepo->getByUrlId($args['url_id']);
    return $renderer->render($response, 'url.php', [
        'title' => 'Анализатор страниц - Детали',
        'url' => $url,
        'checks' => $checks,
        'flash' => $flash->getMessages()
    ]);
})->setName('url');

$app->post('/urls/{url_id:[0-9]+}/checks', function (
    Request $request,
    Response $response,
    $args
) use (
    $client,
    $urlRepo,
    $checkRepo
) {
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    $flash = $this->get(Messages::class);

    $url = $urlRepo->getById($args['url_id']);
    if ($url === null) {
        throw new \Exception("URL not found");
    }

    try {
        $check = $client->get($url['name']);
        $status = $check->getStatusCode();
        $html = $check->getBody()->getContents();
    } catch (\Throwable $th) {
        $flash->addMessage('danger', 'Произошла ошибка при проверке, не удалось подключиться');
        return $response->withStatus(302)->withHeader(
            'Location',
            $routeParser->urlFor('url', ['url_id' => (string) $args['url_id']])
        );
    }

    $crawler = new Crawler($html);
    $h1 = ($node = $crawler->filter('h1'))->count() ? $node->text() : null;
    $title = ($node = $crawler->filter('title'))->count() ? $node->text() : null;
    $description = ($node = $crawler->filter('meta[name="description"]'))->count() ? $node->attr('content') : null;

    $checkRepo->insert($args['url_id'], $status, $h1, $title, $description);

    $flash->addMessage('success', 'Страница успешно проверена');
    return $response->withStatus(302)->withHeader(
        'Location',
        $routeParser->urlFor('url', ['url_id' => (string) $args['url_id']])
    );
});

$app->run();
