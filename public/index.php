<?php
use Slim\Factory\AppFactory;
use App\Controllers\UserController;
use App\Controllers\CompanyController;
use App\Controllers\AuthController;
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

// Add routing middleware first
$app->addRoutingMiddleware();

// Add CORS middleware
$app->add(new CorsMiddleware());

// Error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Default route - define this first
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'message' => 'Welcome to Slim Framework REST API',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api/register' => 'Register new user',
            'GET /api/companies' => 'Get all companies',
            'GET /api/companies/{id}' => 'Get company by ID',
            'POST /api/companies' => 'Create new company',
            'PUT /api/companies/{id}' => 'Update company',
            'DELETE /api/companies/{id}' => 'Delete company',
            'GET /api/users' => 'Get all users',
            'GET /api/users/{id}' => 'Get user by ID',
            'POST /api/users' => 'Create new user',
            'PUT /api/users/{id}' => 'Update user',
            'DELETE /api/users/{id}' => 'Delete user'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Route for the actual file path (when accessing via direct URL)
$app->get('/project-management/public/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'message' => 'Welcome to Slim Framework REST API',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api/register' => 'Register new user',
            'GET /api/companies' => 'Get all companies',
            'GET /api/companies/{id}' => 'Get company by ID',
            'POST /api/companies' => 'Create new company',
            'PUT /api/companies/{id}' => 'Update company',
            'DELETE /api/companies/{id}' => 'Delete company',
            'GET /api/users' => 'Get all users',
            'GET /api/users/{id}' => 'Get user by ID',
            'POST /api/users' => 'Create new user',
            'PUT /api/users/{id}' => 'Update user',
            'DELETE /api/users/{id}' => 'Delete user'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// API routes with full path (when accessing via direct URL)
$app->group('/project-management/public/api', function ($group) {
    // Auth routes
    $group->post('/register', [AuthController::class, 'register']);
    // Company routes
    $group->get('/companies', [CompanyController::class, 'getAllCompanies']);
    $group->get('/companies/{id:[0-9]+}', [CompanyController::class, 'getCompanyById']);
    $group->post('/companies', [CompanyController::class, 'createCompany']);
    $group->put('/companies/{id:[0-9]+}', [CompanyController::class, 'updateCompany']);
    $group->delete('/companies/{id:[0-9]+}', [CompanyController::class, 'deleteCompany']);
    
    // User routes
    $group->get('/users', [UserController::class, 'getAllUsers']);
    $group->get('/users/{id:[0-9]+}', [UserController::class, 'getUserById']);
    $group->post('/users', [UserController::class, 'createUser']);
    $group->put('/users/{id:[0-9]+}', [UserController::class, 'updateUser']);
    $group->delete('/users/{id:[0-9]+}', [UserController::class, 'deleteUser']);
});


// Debug route to test if routing is working
$app->get('/test', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(['message' => 'Test route is working!']));
    return $response->withHeader('Content-Type', 'application/json');
});

// Handle CORS preflight requests for API routes
$app->options('/api/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

// Routes
$app->group('/api', function ($group) {
    // Auth routes
    $group->post('/register', [AuthController::class, 'register']);
    // Company routes
    $group->get('/companies', [CompanyController::class, 'getAllCompanies']);
    $group->get('/companies/{id:[0-9]+}', [CompanyController::class, 'getCompanyById']);
    $group->post('/companies', [CompanyController::class, 'createCompany']);
    $group->put('/companies/{id:[0-9]+}', [CompanyController::class, 'updateCompany']);
    $group->delete('/companies/{id:[0-9]+}', [CompanyController::class, 'deleteCompany']);
    
    // User routes
    $group->get('/users', [UserController::class, 'getAllUsers']);
    $group->get('/users/{id:[0-9]+}', [UserController::class, 'getUserById']);
    $group->post('/users', [UserController::class, 'createUser']);
    $group->put('/users/{id:[0-9]+}', [UserController::class, 'updateUser']);
    $group->delete('/users/{id:[0-9]+}', [UserController::class, 'deleteUser']);
});


$app->run();