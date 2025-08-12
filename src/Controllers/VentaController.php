<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\VentaRepository;
use App\Entities\Venta;

class VentaController
{
    private VentaRepository $ventaRepository;

    public function __construct()
    {
        $this->ventaRepository = new VentaRepository();
    }

    private function ventaToArray(Venta $venta): array
    {
        return [
            'id' => $venta->getId(),
            'fecha' => $venta->getFecha()->format('Y-m-d'),
            'idCliente' => $venta->getIdCliente(),
            'total' => $venta->getTotal(),
            'estado' => $venta->getEstado(),
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
                        $venta = $this->ventaRepository->findById((int)$_GET['id']);
                        echo json_encode($venta ? $this->ventaToArray($venta) : null);
                    } else {
                        $ventas = $this->ventaRepository->findAll();
                        $list = array_map(fn(Venta $v) => $this->ventaToArray($v), $ventas);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    // Validar y limpiar el estado
                    $estado = trim($payload['estado'] ?? 'borrador');
                    if (empty($estado)) {
                        $estado = 'borrador';
                    }

                    $venta = new Venta(
                        null,
                        new \DateTime($payload['fecha'] ?? 'now'),
                        (int)($payload['idCliente'] ?? 0),
                        (float)($payload['total'] ?? 0),
                        $estado
                    );

                    $success = $this->ventaRepository->create($venta);
                    echo json_encode(['success' => $success, 'id' => $venta->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingVenta = $this->ventaRepository->findById((int)$payload['id']);
                    if (!$existingVenta) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Venta no encontrada']);
                        return;
                    }

                    // Validar y limpiar el estado si viene en el payload
                    if (isset($payload['estado'])) {
                        $estado = trim($payload['estado']);
                        if (empty($estado)) {
                            $estado = 'borrador';
                        }
                    } else {
                        $estado = $existingVenta->getEstado();
                    }

                    $existingVenta->setFecha(new \DateTime($payload['fecha'] ?? $existingVenta->getFecha()->format('Y-m-d')));
                    $existingVenta->setIdCliente((int)($payload['idCliente'] ?? $existingVenta->getIdCliente()));
                    $existingVenta->setTotal((float)($payload['total'] ?? $existingVenta->getTotal()));
                    $existingVenta->setEstado($estado);

                    $success = $this->ventaRepository->update($existingVenta);
                    echo json_encode(['success' => $success, 'id' => $existingVenta->getId()]);
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

                    $success = $this->ventaRepository->delete($id);
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