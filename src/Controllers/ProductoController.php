<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProductoRepository;
use App\Entities\ProductoFisico;
use App\Entities\ProductoDigital;
use App\Entities\Producto;

class ProductoController
{
    private ProductoRepository $productoRepository;

    public function __construct()
    {
        $this->productoRepository = new ProductoRepository();
    }

    private function productoToArray(Producto $producto): array
    {
        if ($producto instanceof ProductoFisico) {
            return [
                'id' => $producto->getId(),
                'tipo' => 'ProductoFisico',
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

        if ($producto instanceof ProductoDigital) {
            return [
                'id' => $producto->getId(),
                'tipo' => 'ProductoDigital',
                'nombre' => $producto->getNombre(),
                'descripcion' => $producto->getDescripcion(),
                'precioUnitario' => $producto->getPrecioUnitario(),
                'stock' => $producto->getStock(),
                'idCategoria' => $producto->getIdCategoria(),
                'urlDescarga' => $producto->getUrlDescarga(),
                'licencia' => $producto->getLicencia(),
            ];
        }

        return [
            'id' => $producto->getId(),
            'tipo' => 'Producto',
            'nombre' => $producto->getNombre(),
            'descripcion' => $producto->getDescripcion(),
            'precioUnitario' => $producto->getPrecioUnitario(),
            'stock' => $producto->getStock(),
            'idCategoria' => $producto->getIdCategoria(),
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
                        $producto = $this->productoRepository->findById((int)$_GET['id']);
                        echo json_encode($producto ? $this->productoToArray($producto) : null);
                    } else {
                        $productos = $this->productoRepository->findAll();
                        $list = array_map(fn(Producto $p) => $this->productoToArray($p), $productos);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $producto = $this->createProductoFromPayload($payload);

                    $success = $this->productoRepository->create($producto);
                    echo json_encode(['success' => $success, 'id' => $producto->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingProducto = $this->productoRepository->findById((int)$payload['id']);
                    if (!$existingProducto) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Producto no encontrado']);
                        return;
                    }

                    $producto = $this->updateProductoFromPayload($existingProducto, $payload);

                    $success = $this->productoRepository->update($producto);
                    echo json_encode(['success' => $success, 'id' => $producto->getId()]);
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

                    $success = $this->productoRepository->delete($id);
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

    private function createProductoFromPayload(array $payload): Producto
    {
        if (!isset($payload['tipo'])) {
            throw new \InvalidArgumentException('Tipo de producto no especificado');
        }

        return match ($payload['tipo']) {
            'ProductoFisico' => new ProductoFisico(
                0,
                $payload['nombre'] ?? '',
                $payload['descripcion'] ?? '',
                (float)($payload['precioUnitario'] ?? 0),
                (int)($payload['stock'] ?? 0),
                (int)($payload['idCategoria'] ?? 0),
                (float)($payload['peso'] ?? 0),
                (float)($payload['alto'] ?? 0),
                (float)($payload['ancho'] ?? 0),
                (float)($payload['profundidad'] ?? 0)
            ),
            'ProductoDigital' => new ProductoDigital(
                0,
                $payload['nombre'] ?? '',
                $payload['descripcion'] ?? '',
                (float)($payload['precioUnitario'] ?? 0),
                (int)($payload['stock'] ?? 0),
                (int)($payload['idCategoria'] ?? 0),
                $payload['urlDescarga'] ?? '',
                $payload['licencia'] ?? ''
            ),
            default => throw new \InvalidArgumentException('Tipo de producto inválido'),
        };
    }

    private function updateProductoFromPayload(Producto $producto, array $payload): Producto
    {
        $producto->setNombre($payload['nombre'] ?? $producto->getNombre());
        $producto->setDescripcion($payload['descripcion'] ?? $producto->getDescripcion());
        $producto->setPrecioUnitario(isset($payload['precioUnitario']) ? (float)$payload['precioUnitario'] : $producto->getPrecioUnitario());
        $producto->setStock(isset($payload['stock']) ? (int)$payload['stock'] : $producto->getStock());
        $producto->setIdCategoria(isset($payload['idCategoria']) ? (int)$payload['idCategoria'] : $producto->getIdCategoria());

        if ($producto instanceof ProductoFisico) {
            $producto->setPeso(isset($payload['peso']) ? (float)$payload['peso'] : $producto->getPeso());
            $producto->setAlto(isset($payload['alto']) ? (float)$payload['alto'] : $producto->getAlto());
            $producto->setAncho(isset($payload['ancho']) ? (float)$payload['ancho'] : $producto->getAncho());
            $producto->setProfundidad(isset($payload['profundidad']) ? (float)$payload['profundidad'] : $producto->getProfundidad());
        } elseif ($producto instanceof ProductoDigital) {
            $producto->setUrlDescarga($payload['urlDescarga'] ?? $producto->getUrlDescarga());
            $producto->setLicencia($payload['licencia'] ?? $producto->getLicencia());
        }

        return $producto;
    }
}
