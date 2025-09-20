<?php
use Slim\Factory\AppFactory;
use App\Controllers\UserController;
use App\Middleware\CorsMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

// Autoload classes
spl_autoload_register(function ($className) {
    $className = str_replace('App\\', '', $className);
    $className = str_replace('\\', '/', $className);
    $file = __DIR__ . '/../src/' . $className . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Create Slim app
$app = AppFactory::create();

// Add middleware
$app->add(new CorsMiddleware());
$app->addRoutingMiddleware();

// Handle CORS preflight requests
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

// Error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Routes
$app->group('/api', function ($group) {
    // User routes
    $group->get('/users', [UserController::class, 'getAllUsers']);
    $group->get('/users/{id:[0-9]+}', [UserController::class, 'getUserById']);
    $group->post('/users', [UserController::class, 'createUser']);
    $group->put('/users/{id:[0-9]+}', [UserController::class, 'updateUser']);
    $group->delete('/users/{id:[0-9]+}', [UserController::class, 'deleteUser']);
});

// Default route
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'message' => 'Welcome to Slim Framework REST API',
        'version' => '1.0.0',
        'endpoints' => [
            'GET /api/users' => 'Get all users',
            'GET /api/users/{id}' => 'Get user by ID',
            'POST /api/users' => 'Create new user',
            'PUT /api/users/{id}' => 'Update user',
            'DELETE /api/users/{id}' => 'Delete user'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();