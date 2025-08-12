<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\RolPermisoRepository;
use App\Entities\RolPermiso;

class RolPermisoController
{
    private RolPermisoRepository $rolPermisoRepository;

    public function __construct()
    {
        $this->rolPermisoRepository = new RolPermisoRepository();
    }

    private function rolPermisoToArray(RolPermiso $rp): array
    {
        return [
            'idRol' => $rp->getIdRol(),
            'idPermiso' => $rp->getIdPermiso(),
        ];
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            switch ($method) {
                case 'GET':
                    if (isset($_GET['idRol']) && isset($_GET['idPermiso'])) {
                        $rp = $this->rolPermisoRepository->findByCompositeId((int)$_GET['idRol'], (int)$_GET['idPermiso']);
                        echo json_encode($rp ? $this->rolPermisoToArray($rp) : null);
                    } else {
                        $rps = $this->rolPermisoRepository->findAll();
                        $list = array_map(fn(RolPermiso $rp) => $this->rolPermisoToArray($rp), $rps);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['idRol'], $payload['idPermiso'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Campos idRol y idPermiso son requeridos']);
                        return;
                    }

                    $rp = new RolPermiso((int)$payload['idRol'], (int)$payload['idPermiso']);
                    $success = $this->rolPermisoRepository->create($rp);
                    echo json_encode(['success' => $success]);
                    break;

                case 'DELETE':
                    $idRol = $_GET['idRol'] ?? null;
                    $idPermiso = $_GET['idPermiso'] ?? null;

                    if ($idRol === null || $idPermiso === null) {
                        $payload = json_decode(file_get_contents('php://input'), true);
                        $idRol = $idRol ?? $payload['idRol'] ?? null;
                        $idPermiso = $idPermiso ?? $payload['idPermiso'] ?? null;
                    }

                    if ($idRol === null || $idPermiso === null) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Campos idRol y idPermiso son requeridos para eliminar']);
                        return;
                    }

                    $success = $this->rolPermisoRepository->deleteCompositeId((int)$idRol, (int)$idPermiso);
                    echo json_encode(['success' => $success]);
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
