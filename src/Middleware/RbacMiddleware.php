<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use PDO;
use App\Config\Database;

class RbacMiddleware implements MiddlewareInterface
{
    private $connection;
    private $rolePermissions;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
        
        // Define role permissions for different endpoints
        $this->rolePermissions = [
            // Admin can do everything
            'admin' => ['*'],
            
            // Owner can do everything except manage other companies
            'owner' => [
                'GET:*', 'POST:*', 'PUT:*', 'DELETE:*',
                '!GET:/companies', '!POST:/companies', '!PUT:/companies', '!DELETE:/companies'
            ],
            
            // Project Manager permissions
            'project_manager' => [
                'GET:/projects', 'POST:/projects', 'PUT:/projects', 'DELETE:/projects',
                'GET:/epics', 'POST:/epics', 'PUT:/epics', 'DELETE:/epics',
                'GET:/stories', 'POST:/stories', 'PUT:/stories', 'DELETE:/stories',
                'GET:/tasks', 'POST:/tasks', 'PUT:/tasks', 'DELETE:/tasks',
                'GET:/teams', 'GET:/users'
            ],
            
            // Team Leader permissions
            'team_leader' => [
                'GET:/projects', 
                'GET:/epics', 'POST:/epics', 'PUT:/epics',
                'GET:/stories', 'POST:/stories', 'PUT:/stories', 'DELETE:/stories',
                'GET:/tasks', 'POST:/tasks', 'PUT:/tasks', 'DELETE:/tasks',
                'GET:/teams', 'GET:/users'
            ],
            
            // Developer permissions
            'developer' => [
                'GET:/projects', 'GET:/epics', 'GET:/stories',
                'GET:/tasks', 'PUT:/tasks',
                'GET:/teams', 'GET:/users'
            ],
            
            // QA permissions
            'qa' => [
                'GET:/projects', 'GET:/epics', 'GET:/stories',
                'GET:/tasks', 'PUT:/tasks',
                'GET:/teams', 'GET:/users'
            ],
            
            // Business Analyst permissions
            'business_analyst' => [
                'GET:/projects', 'GET:/epics', 
                'GET:/stories', 'POST:/stories', 'PUT:/stories',
                'GET:/tasks',
                'GET:/teams', 'GET:/users'
            ],
            
            // Resource Manager permissions
            'resource_manager' => [
                'GET:/projects', 'GET:/epics', 'GET:/stories', 'GET:/tasks',
                'GET:/teams', 'PUT:/teams',
                'GET:/users', 'PUT:/users'
            ],
            
            // Viewer permissions (read-only)
            'viewer' => [
                'GET:/projects', 'GET:/epics', 'GET:/stories', 'GET:/tasks',
                'GET:/teams', 'GET:/users'
            ]
        ];
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        // Get user ID from request (assuming it's set in a previous middleware)
        $userId = $request->getAttribute('user_id');
        
        if (!$userId) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Unauthorized: User not authenticated'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        
        // Get the current request method and path
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        
        // Get user roles from database
        $userRoles = $this->getUserRoles($userId);
        
        // Check if user has permission for this endpoint
        if (!$this->hasPermission($userRoles, $method, $path)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Forbidden: Insufficient permissions'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }
        
        // User has permission, proceed with the request
        return $handler->handle($request);
    }
    
    private function getUserRoles(int $userId): array
    {
        try {
            $query = "SELECT role FROM User_Team_Roles WHERE user_id = :user_id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $roles = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $roles[] = $row['role'];
            }
            
            return $roles;
        } catch (\PDOException $e) {
            // Log error
            error_log('Error fetching user roles: ' . $e->getMessage());
            return [];
        }
    }
    
    private function hasPermission(array $userRoles, string $method, string $path): bool
    {
        // If user has no roles, deny access
        if (empty($userRoles)) {
            return false;
        }
        
        $methodPath = $method . ':' . $path;
        
        foreach ($userRoles as $role) {
            // Skip if role doesn't exist in permissions
            if (!isset($this->rolePermissions[$role])) {
                continue;
            }
            
            $permissions = $this->rolePermissions[$role];
            
            // Check for wildcard permission
            if (in_array('*', $permissions)) {
                return true;
            }
            
            // Check for exact permission match
            if (in_array($methodPath, $permissions)) {
                return true;
            }
            
            // Check for method wildcard
            if (in_array($method . ':*', $permissions)) {
                return true;
            }
            
            // Check for path wildcard (e.g., GET:/projects/*)
            foreach ($permissions as $permission) {
                if (substr($permission, -1) === '*' && 
                    strpos($methodPath, substr($permission, 0, -1)) === 0) {
                    return true;
                }
            }
            
            // Check for negated permissions (starting with !)
            foreach ($permissions as $permission) {
                if (substr($permission, 0, 1) === '!' && 
                    substr($permission, 1) === $methodPath) {
                    return false;
                }
            }
        }
        
        return false;
    }
}