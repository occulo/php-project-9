<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DI\ContainerBuilder;
use Slim\Routing\RouteContext;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Slim\Flash\Messages;
use Slim\Exception\HttpNotFoundException;
use Valitron\Validator;
use GuzzleHttp\Client;
use Hexlet\Code\Database;
use Hexlet\Code\Repository\UrlRepository;
use Hexlet\Code\Repository\CheckRepository;
use Hexlet\Code\HtmlParser;
use Hexlet\Code\UrlNormalizer;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Database
$dbUrl = getenv('DATABASE_URL');
if (!is_string($dbUrl)) {
    throw new \Exception("DATABASE_URL is not set");
}
$pdo = Database::connect($dbUrl);

// Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    Messages::class => fn() => new Messages($_SESSION),
    UrlRepository::class => fn() => new UrlRepository($pdo),
    CheckRepository::class => fn() => new CheckRepository($pdo)
]);
$container = $containerBuilder->build();
AppFactory::setContainer($container);

// App
$app = AppFactory::create();

// Router
$routeParser = $app->getRouteCollector()->getRouteParser();

// Renderer
$renderer = new PhpRenderer(__DIR__ . '/../templates', [
    'router' => $routeParser,
    'flash'  => $container->get(Messages::class)
]);
$renderer->setLayout('layouts/layout.php');

// Error Handler
$errorHandler = function (Request $request, Throwable $exception) use ($app, $renderer) {
    if ($exception instanceof HttpNotFoundException) {
        $response = $app->getResponseFactory()->createResponse(404);
        return $renderer->render($response, 'errors/404.php', ['title' => 'Страница не найдена']);
    }
    $response = $app->getResponseFactory()->createResponse(500);
    return $renderer->render($response, 'errors/500.php', ['title' => 'Ошибка сервера']);
};
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Routes
$app->get('/', function (Request $request, Response $response, $args) use ($renderer) {
    return $renderer->render($response, 'index.php', [
        'title' => 'Анализатор страниц'
    ]);
})->setName('home');

$app->get('/urls', function (Request $request, Response $response, $args) use ($renderer) {
    $urlRepo = $this->get(UrlRepository::class);
    $checkRepo = $this->get(CheckRepository::class);

    $urls = $urlRepo->getAll();
    $checks = $checkRepo->getAllLatest();
    $sortedUrls = array_map(function ($url) use ($checks) {
        $check = $checks[$url['id']] ?? [];
        return array_merge($url, $check);
    }, $urls);

    return $renderer->render($response, 'urls/index.php', [
        'title' => 'Анализатор страниц - Сайты',
        'urls' => $sortedUrls
    ]);
})->setName('urls');

$app->post('/urls', function (Request $request, Response $response, $args) use ($renderer) {
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    $urlRepo = $this->get(UrlRepository::class);
    $data = $request->getParsedBody();
    $data = is_array($data) ? $data : [];

    $validator = new Validator($data);
    $validator->rule('required', 'url')->message('URL не должен быть пустым');
    $validator->rule('lengthMax', 'url', 255)->message('URL превышает 255 символов');
    $validator->rule('url', 'url')->message('Некорректный URL');

    $url = trim($data['url'] ?? '');

    if (!$validator->validate()) {
        return $renderer->render($response, 'index.php', [
            'title' => 'Анализатор страниц',
            'urlValue' => $url,
            'errors' => $validator->errors()
        ])->withStatus(422);
    }

    $normalizedUrl = UrlNormalizer::normalize($url);

    $flash = $this->get(Messages::class);
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
})->setName('urls.store');

$app->get('/urls/{url_id:[0-9]+}', function (Request $request, Response $response, $args) use ($renderer) {
    $urlRepo = $this->get(UrlRepository::class);
    $checkRepo = $this->get(CheckRepository::class);
    $url = $urlRepo->getById($args['url_id']);
    if ($url === null) {
        throw new HttpNotFoundException($request);
    }
    $checks = $checkRepo->getByUrlId($args['url_id']);
    return $renderer->render($response, 'urls/show.php', [
        'title' => 'Анализатор страниц - Детали',
        'url' => $url,
        'checks' => $checks
    ]);
})->setName('url');

$app->post('/urls/{url_id:[0-9]+}/checks', function (Request $request, Response $response, $args) {
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    $client = new Client();
    $urlRepo = $this->get(UrlRepository::class);
    $checkRepo = $this->get(CheckRepository::class);

    $url = $urlRepo->getById($args['url_id']);
    if ($url === null) {
        throw new HttpNotFoundException($request);
    }

    $flash = $this->get(Messages::class);
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

    $htmlParser = new HtmlParser($html);
    $h1 = $htmlParser->getElement('h1');
    $title = $htmlParser->getElement('title');
    $description = $htmlParser->getMetaByName('description');

    $checkRepo->insert($args['url_id'], $status, $h1, $title, $description);

    $flash->addMessage('success', 'Страница успешно проверена');
    return $response->withStatus(302)->withHeader(
        'Location',
        $routeParser->urlFor('url', ['url_id' => (string) $args['url_id']])
    );
})->setName('url.check.store');

$app->run();
