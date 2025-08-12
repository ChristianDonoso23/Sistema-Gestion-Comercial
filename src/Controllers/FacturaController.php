<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\FacturaRepository;
use App\Entities\Factura;

class FacturaController
{
    private FacturaRepository $facturaRepository;

    public function __construct()
    {
        $this->facturaRepository = new FacturaRepository();
    }

    private function facturaToArray(Factura $factura): array
    {
        return [
            'id' => $factura->getId(),
            'idVenta' => $factura->getIdVenta(),
            'numero' => $factura->getNumero(),
            'claveAcceso' => $factura->getClaveAcceso(),
            'fechaEmision' => $factura->getFechaEmision()->format('Y-m-d'),
            'estado' => $factura->getEstado(),
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
                        $factura = $this->facturaRepository->findById((int)$_GET['id']);
                        echo json_encode($factura ? $this->facturaToArray($factura) : null);
                    } else {
                        $facturas = $this->facturaRepository->findAll();
                        $list = array_map(fn(Factura $f) => $this->facturaToArray($f), $facturas);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $fechaEmision = isset($payload['fechaEmision']) ? new \DateTime($payload['fechaEmision']) : new \DateTime();

                    $factura = new Factura(
                        null,
                        (int)($payload['idVenta'] ?? 0),
                        (int)($payload['numero'] ?? 0),
                        $payload['claveAcceso'] ?? '',
                        $fechaEmision,
                        $payload['estado'] ?? 'borrador'
                    );

                    $success = $this->facturaRepository->create($factura);
                    echo json_encode(['success' => $success, 'id' => $factura->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingFactura = $this->facturaRepository->findById((int)$payload['id']);
                    if (!$existingFactura) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Factura no encontrada']);
                        return;
                    }

                    $existingFactura->setIdVenta((int)($payload['idVenta'] ?? $existingFactura->getIdVenta()));
                    $existingFactura->setNumero((int)($payload['numero'] ?? $existingFactura->getNumero()));
                    $existingFactura->setClaveAcceso($payload['claveAcceso'] ?? $existingFactura->getClaveAcceso());
                    if (isset($payload['fechaEmision'])) {
                        $existingFactura->setFechaEmision(new \DateTime($payload['fechaEmision']));
                    }
                    $existingFactura->setEstado($payload['estado'] ?? $existingFactura->getEstado());

                    $success = $this->facturaRepository->update($existingFactura);
                    echo json_encode(['success' => $success, 'id' => $existingFactura->getId()]);
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

                    $success = $this->facturaRepository->delete($id);
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
