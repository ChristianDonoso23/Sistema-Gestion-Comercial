<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProductoFisicoRepository;
use App\Entities\ProductoFisico;

class ProductoFisicoController
{
    private ProductoFisicoRepository $productoFisicoRepository;

    public function __construct()
    {
        $this->productoFisicoRepository = new ProductoFisicoRepository();
    }

    private function productoFisicoToArray(ProductoFisico $producto): array
    {
        return [
            'id' => $producto->getId(),
            'nombre' => $producto->getNombre(),
            'descripcion' => $producto->getDescripcion(),
            'precioUnitario' => $producto->getPrecioUnitario(),
            'stock' => $producto->getStock(),
            'idCategoria' => $producto->getIdCategoria(),
            'peso' => $producto->getPeso(),
            'alto' => $producto->getAlto(),
            'ancho' => $producto->getAncho(),
            'profundidad' => $producto->getProfundidad(),
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
                        $producto = $this->productoFisicoRepository->findById((int)$_GET['id']);
                        echo json_encode($producto ? $this->productoFisicoToArray($producto) : null);
                    } else {
                        $productos = $this->productoFisicoRepository->findAll();
                        $list = array_map(fn(ProductoFisico $p) => $this->productoFisicoToArray($p), $productos);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $producto = new ProductoFisico(
                        null,
                        $payload['nombre'] ?? '',
                        $payload['descripcion'] ?? '',
                        $payload['precioUnitario'] ?? 0.0,
                        $payload['stock'] ?? 0,
                        $payload['idCategoria'] ?? null,
                        $payload['peso'] ?? 0.0,
                        $payload['alto'] ?? 0.0,
                        $payload['ancho'] ?? 0.0,
                        $payload['profundidad'] ?? 0.0
                    );

                    $success = $this->productoFisicoRepository->create($producto);
                    echo json_encode(['success' => $success, 'id' => $producto->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingProducto = $this->productoFisicoRepository->findById((int)$payload['id']);
                    if (!$existingProducto) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Producto físico no encontrado']);
                        return;
                    }

                    $existingProducto->setNombre($payload['nombre'] ?? $existingProducto->getNombre());
                    $existingProducto->setDescripcion($payload['descripcion'] ?? $existingProducto->getDescripcion());
                    $existingProducto->setPrecioUnitario($payload['precioUnitario'] ?? $existingProducto->getPrecioUnitario());
                    $existingProducto->setStock($payload['stock'] ?? $existingProducto->getStock());
                    $existingProducto->setIdCategoria($payload['idCategoria'] ?? $existingProducto->getIdCategoria());
                    $existingProducto->setPeso($payload['peso'] ?? $existingProducto->getPeso());
                    $existingProducto->setAlto($payload['alto'] ?? $existingProducto->getAlto());
                    $existingProducto->setAncho($payload['ancho'] ?? $existingProducto->getAncho());
                    $existingProducto->setProfundidad($payload['profundidad'] ?? $existingProducto->getProfundidad());

                    $success = $this->productoFisicoRepository->update($existingProducto);
                    echo json_encode(['success' => $success, 'id' => $existingProducto->getId()]);
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

                    $success = $this->productoFisicoRepository->delete($id);
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
