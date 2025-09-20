<?php
namespace App\Controllers;

use App\Models\Company;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CompanyController
{
    private $companyModel;

    public function __construct()
    {
        $this->companyModel = new Company();
    }

    public function getAllCompanies(Request $request, Response $response)
    {
        try {
            $companies = $this->companyModel->getAllCompanies();
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $companies
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

    public function getCompanyById(Request $request, Response $response, array $args)
    {
        try {
            $id = (int)$args['id'];
            $company = $this->companyModel->getCompanyById($id);
            
            if (!$company) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Company not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $company
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

    public function createCompany(Request $request, Response $response)
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            
            if (!isset($data['company_name']) || empty(trim($data['company_name']))) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Company name is required'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $company = $this->companyModel->createCompany($data);
            
            if ($company) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'Company created successfully',
                    'data' => $company
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to create company'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateCompany(Request $request, Response $response, array $args)
    {
        try {
            $id = (int)$args['id'];
            $data = json_decode($request->getBody()->getContents(), true);
            
            if (!isset($data['company_name']) || empty(trim($data['company_name']))) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Company name is required'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $updated = $this->companyModel->updateCompany($id, $data);
            
            if ($updated) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'Company updated successfully'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Company not found or no changes made'
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

    public function deleteCompany(Request $request, Response $response, array $args)
    {
        try {
            $id = (int)$args['id'];
            $deleted = $this->companyModel->deleteCompany($id);
            
            if ($deleted) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'Company deleted successfully'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Company not found'
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
