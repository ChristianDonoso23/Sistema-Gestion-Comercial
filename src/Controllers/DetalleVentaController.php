<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\DetalleVentaRepository;
use App\Repositories\ProductoRepository;
use App\Entities\DetalleVenta;

class DetalleVentaController
{
    private DetalleVentaRepository $detalleVentaRepository;
    private ProductoRepository $productoRepository;

    public function __construct()
    {
        $this->detalleVentaRepository = new DetalleVentaRepository();
        $this->productoRepository = new ProductoRepository();
    }

    private function detalleVentaToArray(DetalleVenta $detalle): array
    {
        return [
            'idVenta' => $detalle->getIdVenta(),
            'lineNumber' => $detalle->getLineNumber(),
            'idProducto' => $detalle->getIdProducto(),
            'cantidad' => $detalle->getCantidad(),
            'precioUnitario' => $detalle->getPrecioUnitario(),
            'subtotal' => $detalle->getSubtotal(),
        ];
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            switch ($method) {
                case 'GET':
                    if (isset($_GET['idVenta']) && isset($_GET['lineNumber'])) {
                        $detalle = $this->detalleVentaRepository->findByCompositeId((int)$_GET['idVenta'], (int)$_GET['lineNumber']);
                        echo json_encode($detalle ? $this->detalleVentaToArray($detalle) : null);
                    } else {
                        $detalles = $this->detalleVentaRepository->findAll();
                        $list = array_map(fn(DetalleVenta $d) => $this->detalleVentaToArray($d), $detalles);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $producto = $this->productoRepository->findById((int)($payload['idProducto'] ?? 0));
                    if (!$producto) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Producto no existe']);
                        return;
                    }

                    $cantidad = (int)($payload['cantidad'] ?? 0);
                    $precioUnitario = $producto->getPrecioUnitario();
                    $subtotal = $precioUnitario * $cantidad;

                    $detalle = new DetalleVenta(
                        (int)($payload['idVenta'] ?? 0),
                        (int)($payload['lineNumber'] ?? 0),
                        (int)($payload['idProducto'] ?? 0),
                        $cantidad,
                        $precioUnitario,
                        $subtotal
                    );

                    $success = $this->detalleVentaRepository->create($detalle);
                    echo json_encode(['success' => $success]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['idVenta'], $payload['lineNumber'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID compuestos requeridos para actualizar']);
                        return;
                    }

                    $existingDetalle = $this->detalleVentaRepository->findByCompositeId((int)$payload['idVenta'], (int)$payload['lineNumber']);
                    if (!$existingDetalle) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Detalle de venta no encontrado']);
                        return;
                    }

                    $newIdProducto = isset($payload['idProducto']) ? (int)$payload['idProducto'] : $existingDetalle->getIdProducto();
                    $newCantidad = isset($payload['cantidad']) ? (int)$payload['cantidad'] : $existingDetalle->getCantidad();

                    $producto = $this->productoRepository->findById($newIdProducto);
                    if (!$producto) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Producto no existe']);
                        return;
                    }

                    $precioUnitario = $producto->getPrecioUnitario();
                    $subtotal = $precioUnitario * $newCantidad;

                    $existingDetalle->setIdProducto($newIdProducto);
                    $existingDetalle->setCantidad($newCantidad);
                    $existingDetalle->setPrecioUnitario($precioUnitario);
                    $existingDetalle->setSubtotal($subtotal);

                    $success = $this->detalleVentaRepository->update($existingDetalle);
                    echo json_encode(['success' => $success]);
                    break;

                case 'DELETE':
                    $idVenta = $_GET['idVenta'] ?? null;
                    $lineNumber = $_GET['lineNumber'] ?? null;

                    if ($idVenta === null || $lineNumber === null) {
                        $payload = json_decode(file_get_contents('php://input'), true);
                        $idVenta = $idVenta ?? $payload['idVenta'] ?? null;
                        $lineNumber = $lineNumber ?? $payload['lineNumber'] ?? null;
                    }

                    if ($idVenta === null || $lineNumber === null) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID compuestos requeridos para eliminar']);
                        return;
                    }

                    $success = $this->detalleVentaRepository->deleteCompositeId((int)$idVenta, (int)$lineNumber);
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
