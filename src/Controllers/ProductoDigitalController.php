<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProductoDigitalRepository;
use App\Entities\ProductoDigital;

class ProductoDigitalController
{
    private ProductoDigitalRepository $productoDigitalRepository;

    public function __construct()
    {
        $this->productoDigitalRepository = new ProductoDigitalRepository();
    }

    private function productoDigitalToArray(ProductoDigital $producto): array
    {
        return [
            'id' => $producto->getId(),
            'nombre' => $producto->getNombre(),
            'descripcion' => $producto->getDescripcion(),
            'precioUnitario' => $producto->getPrecioUnitario(),
            'stock' => $producto->getStock(),
            'idCategoria' => $producto->getIdCategoria(),
            'urlDescarga' => $producto->getUrlDescarga(),
            'licencia' => $producto->getLicencia(),
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
                        $producto = $this->productoDigitalRepository->findById((int)$_GET['id']);
                        echo json_encode($producto ? $this->productoDigitalToArray($producto) : null);
                    } else {
                        $productos = $this->productoDigitalRepository->findAll();
                        $list = array_map(fn(ProductoDigital $p) => $this->productoDigitalToArray($p), $productos);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $producto = new ProductoDigital(
                        null,
                        $payload['nombre'] ?? '',
                        $payload['descripcion'] ?? '',
                        $payload['precioUnitario'] ?? 0.0,
                        $payload['stock'] ?? 0,
                        $payload['idCategoria'] ?? null,
                        $payload['urlDescarga'] ?? '',
                        $payload['licencia'] ?? ''
                    );

                    $success = $this->productoDigitalRepository->create($producto);
                    echo json_encode(['success' => $success, 'id' => $producto->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingProducto = $this->productoDigitalRepository->findById((int)$payload['id']);
                    if (!$existingProducto) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Producto digital no encontrado']);
                        return;
                    }

                    $existingProducto->setNombre($payload['nombre'] ?? $existingProducto->getNombre());
                    $existingProducto->setDescripcion($payload['descripcion'] ?? $existingProducto->getDescripcion());
                    $existingProducto->setPrecioUnitario($payload['precioUnitario'] ?? $existingProducto->getPrecioUnitario());
                    $existingProducto->setStock($payload['stock'] ?? $existingProducto->getStock());
                    $existingProducto->setIdCategoria($payload['idCategoria'] ?? $existingProducto->getIdCategoria());
                    $existingProducto->setUrlDescarga($payload['urlDescarga'] ?? $existingProducto->getUrlDescarga());
                    $existingProducto->setLicencia($payload['licencia'] ?? $existingProducto->getLicencia());

                    $success = $this->productoDigitalRepository->update($existingProducto);
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

                    $success = $this->productoDigitalRepository->delete($id);
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
