<?php
namespace App\Controllers;

use App\Models\AuthUser;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    private $authUser;

    public function __construct()
    {
        $this->authUser = new AuthUser();
    }

    public function register(Request $request, Response $response)
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            $companyId = isset($data['company_id']) ? (int)$data['company_id'] : 0;
            $email = isset($data['email']) ? trim($data['email']) : '';
            $password = isset($data['password']) ? (string)$data['password'] : '';
            $firstName = isset($data['first_name']) ? trim($data['first_name']) : '';
            $lastName = isset($data['last_name']) ? trim($data['last_name']) : '';

            if ($companyId <= 0 || $email === '' || $password === '') {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'company_id, email and password are required'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Invalid email format'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
            }

            if ($this->authUser->emailExists($email)) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Email already registered'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $created = $this->authUser->register([
                'company_id' => $companyId,
                'email' => $email,
                'password_hash' => $passwordHash,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => $created
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function login(Request $request, Response $response)
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            $email = isset($data['email']) ? trim($data['email']) : '';
            $password = isset($data['password']) ? (string)$data['password'] : '';

            if ($email === '' || $password === '') {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'email and password are required'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Invalid email format'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
            }

            $user = $this->authUser->getUserByEmail($email);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }

            unset($user['password_hash']);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Login successful',
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
}


