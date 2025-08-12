<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\RolRepository;
use App\Entities\Rol;

class RolController
{
    private RolRepository $rolRepository;

    public function __construct()
    {
        $this->rolRepository = new RolRepository();
    }

    private function rolToArray(Rol $rol): array
    {
        return [
            'id' => $rol->getId(),
            'nombre' => $rol->getNombre(),
        ];
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            switch ($method) {
                case 'GET':
                    if (isset($_GET['id'])) {
                        $rol = $this->rolRepository->findById((int)$_GET['id']);
                        echo json_encode($rol ? $this->rolToArray($rol) : null);
                    } else {
                        $roles = $this->rolRepository->findAll();
                        $list = array_map(fn(Rol $r) => $this->rolToArray($r), $roles);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || empty($payload['nombre'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Campo "nombre" requerido']);
                        return;
                    }

                    $rol = new Rol(null, $payload['nombre']);

                    $success = $this->rolRepository->create($rol);
                    echo json_encode(['success' => $success, 'id' => $rol->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'], $payload['nombre'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y nombre requeridos para actualizar']);
                        return;
                    }

                    $rol = $this->rolRepository->findById((int)$payload['id']);
                    if (!$rol) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Rol no encontrado']);
                        return;
                    }

                    $rol->setNombre($payload['nombre']);
                    $success = $this->rolRepository->update($rol);
                    echo json_encode(['success' => $success, 'id' => $rol->getId()]);
                    break;

                case 'DELETE':
                    $id = null;
                    if (isset($_GET['id'])) {
                        $id = (int)$_GET['id'];
                    } else {
                        $payload = json_decode(file_get_contents('php://input'), true);
                        $id = $payload['id'] ?? null;
                    }

                    if (!$id) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID requerido para eliminar']);
                        return;
                    }

                    $success = $this->rolRepository->delete($id);
                    echo json_encode(['success' => $success, 'id' => $id]);
                    break;

                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Método HTTP no soportado']);
                    break;
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }
}
