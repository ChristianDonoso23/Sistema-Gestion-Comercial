<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PermisoRepository;
use App\Entities\Permiso;

class PermisoController
{
    private PermisoRepository $permisoRepository;

    public function __construct()
    {
        $this->permisoRepository = new PermisoRepository();
    }

    private function permisoToArray(Permiso $permiso): array
    {
        return [
            'id' => $permiso->getId(),
            'codigo' => $permiso->getCodigo(),
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
                        $permiso = $this->permisoRepository->findById((int)$_GET['id']);
                        echo json_encode($permiso ? $this->permisoToArray($permiso) : null);
                    } else {
                        $permisos = $this->permisoRepository->findAll();
                        $list = array_map(fn(Permiso $p) => $this->permisoToArray($p), $permisos);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || empty($payload['codigo'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Campo "codigo" requerido']);
                        return;
                    }

                    $permiso = new Permiso(null, $payload['codigo']);

                    $success = $this->permisoRepository->create($permiso);
                    echo json_encode(['success' => $success, 'id' => $permiso->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'], $payload['codigo'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y codigo requeridos para actualizar']);
                        return;
                    }

                    $permiso = $this->permisoRepository->findById((int)$payload['id']);
                    if (!$permiso) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Permiso no encontrado']);
                        return;
                    }

                    $permiso->setCodigo($payload['codigo']);
                    $success = $this->permisoRepository->update($permiso);
                    echo json_encode(['success' => $success, 'id' => $permiso->getId()]);
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

                    $success = $this->permisoRepository->delete($id);
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
