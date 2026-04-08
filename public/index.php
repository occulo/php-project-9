<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DI\ContainerBuilder;
use Slim\Routing\RouteContext;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Slim\Flash\Messages;
use Valitron\Validator;
use Hexlet\Code\Database;
use Hexlet\Code\UrlRepository;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// Database
$pdo = Database::connect(getenv('DATABASE_URL'));
$repo = new UrlRepository($pdo);

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

// Routes
$app->get('/', function (Request $request, Response $response, $args) use ($renderer) {
    $flash = $this->get(Messages::class);
    return $renderer->render($response, 'index.php', [
        'title' => 'Анализатор страниц',
        'flash' => $flash->getMessages()
    ]);
});

$app->get('/urls', function (Request $request, Response $response, $args) use ($renderer, $repo) {
    $flash = $this->get(Messages::class);
    $urls = $repo->getAll();
    return $renderer->render($response, 'urls.php', [
        'title' => 'Анализатор страниц - Сайты',
        'urls' => $urls,
        'flash' => $flash->getMessages()
    ]);
});

$app->post('/urls', function (Request $request, Response $response, $args) use ($renderer, $repo) {
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    $flash = $this->get(Messages::class);
    $data = $request->getParsedBody();

    $validator = new Validator($data);
    $validator->rule('required', 'url')->message('URL не должен быть пустым');
    $validator->rule('lengthMax', 'url', 255)->message('URL превышает 255 символов');
    $validator->rule('url', 'url')->message('Некорректный URL');

    if (!$validator->validate()) {
        $validatorErrors = array_merge(...array_values($validator->errors()));
        return $renderer->render($response, 'index.php', [
            'title' => 'Анализатор страниц',
            'urlValue' => $data['url'],
            'flash' => ['danger' => $validatorErrors]
        ])->withStatus(422);
    }

    $url = trim($data['url']);
    $parsedUrl = parse_url($url);
    $normalizedUrl = sprintf("%s://%s", $parsedUrl['scheme'], $parsedUrl['host']);

    if ($existing = $repo->getByName($normalizedUrl)) {
        $flash->addMessage('warning', 'Страница уже существует');
        return $response->withStatus(302)->withHeader(
            'Location',
            $routeParser->urlFor('url', ['id' => $existing['id']])
        );
    }

    $id = $repo->insert($normalizedUrl);
    $flash->addMessage('success', 'Страница успешно добавлена');
    return $response->withStatus(302)->withHeader('Location', $routeParser->urlFor('url', ['id' => $id]));
});

$app->get('/urls/{id}', function (Request $request, Response $response, $args) use ($renderer, $repo) {
    $flash = $this->get(Messages::class);
    $url = $repo->getById($args['id']);
    return $renderer->render($response, 'url.php', [
        'title' => 'Анализатор страниц - Детали',
        'url' => $url,
        'flash' => $flash->getMessages()
    ]);
})->setName('url');

$app->run();
