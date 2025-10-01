<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $auth = $request->getHeaderLine('Authorization');
        
        if (empty($auth)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Authentication required'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        
        // Extract the token
        $token = str_replace('Bearer ', '', $auth);
        
        try {
            // For simplicity, we're using a basic token validation
            // In a production environment, use JWT or other secure token method
            $userData = $this->validateToken($token);
            
            // Add user data to request attributes
            $request = $request->withAttribute('user_id', $userData['user_id']);
            $request = $request->withAttribute('company_id', $userData['company_id']);
            
            // Continue with the request
            return $handler->handle($request);
            
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Invalid or expired token'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
    
    private function validateToken(string $token): array
    {
        // This is a placeholder for token validation
        // In a real application, you would validate JWT or other token type
        
        // For demonstration purposes, we'll decode a simple base64 token
        // Format: base64(user_id:company_id)
        $decoded = base64_decode($token);
        
        if (!$decoded || strpos($decoded, ':') === false) {
            throw new \Exception('Invalid token format');
        }
        
        list($userId, $companyId) = explode(':', $decoded);
        
        if (!is_numeric($userId) || !is_numeric($companyId)) {
            throw new \Exception('Invalid token data');
        }
        
        return [
            'user_id' => (int)$userId,
            'company_id' => (int)$companyId
        ];
    }
}