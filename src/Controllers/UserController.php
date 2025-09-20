<?php
namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function getAllUsers(Request $request, Response $response)
    {
        try {
            $users = $this->userModel->getAllUsers();
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $users
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getUserById(Request $request, Response $response, array $args)
    {
        try {
            $id = (int)$args['id'];
            $user = $this->userModel->getUserById($id);
            
            if (!$user) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'User not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $user
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function createUser(Request $request, Response $response)
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            
            if (!isset($data['name']) || !isset($data['email'])) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Name and email are required'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $user = $this->userModel->createUser($data);
            
            if ($user) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'User created successfully',
                    'data' => $user
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            }
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateUser(Request $request, Response $response, array $args)
    {
        try {
            $id = (int)$args['id'];
            $data = json_decode($request->getBody()->getContents(), true);
            
            if (!isset($data['name']) || !isset($data['email'])) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Name and email are required'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $updated = $this->userModel->updateUser($id, $data);
            
            if ($updated) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'User updated successfully'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'User not found or no changes made'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteUser(Request $request, Response $response, array $args)
    {
        try {
            $id = (int)$args['id'];
            $deleted = $this->userModel->deleteUser($id);
            
            if ($deleted) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'User deleted successfully'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'User not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}