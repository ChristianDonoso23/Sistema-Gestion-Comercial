<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CategoriaRepository;
use App\Entities\Categoria;

class CategoriaController
{
    private CategoriaRepository $categoriaRepository;

    public function __construct()
    {
        $this->categoriaRepository = new CategoriaRepository();
    }

    private function categoriaToArray(Categoria $categoria): array
    {
        return [
            'id' => $categoria->getId(),
            'nombre' => $categoria->getNombre(),
            'descripcion' => $categoria->getDescripcion(),
            'estado' => $categoria->getEstado(),
            'idPadre' => $categoria->getIdPadre(),
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
                        $categoria = $this->categoriaRepository->findById((int)$_GET['id']);
                        echo json_encode($categoria ? $this->categoriaToArray($categoria) : null);
                    } else {
                        $categorias = $this->categoriaRepository->findAll();
                        $list = array_map(fn(Categoria $c) => $this->categoriaToArray($c), $categorias);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $categoria = new Categoria(
                        null,
                        $payload['nombre'] ?? '',
                        $payload['descripcion'] ?? '',
                        $payload['estado'] ?? 'activo',
                        $payload['idPadre'] ?? null
                    );

                    $success = $this->categoriaRepository->create($categoria);
                    echo json_encode(['success' => $success, 'id' => $categoria->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingCategoria = $this->categoriaRepository->findById((int)$payload['id']);
                    if (!$existingCategoria) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Categoría no encontrada']);
                        return;
                    }

                    $existingCategoria->setNombre($payload['nombre'] ?? $existingCategoria->getNombre());
                    $existingCategoria->setDescripcion($payload['descripcion'] ?? $existingCategoria->getDescripcion());
                    $existingCategoria->setEstado($payload['estado'] ?? $existingCategoria->getEstado());
                    $existingCategoria->setIdPadre($payload['idPadre'] ?? $existingCategoria->getIdPadre());

                    $success = $this->categoriaRepository->update($existingCategoria);
                    echo json_encode(['success' => $success, 'id' => $existingCategoria->getId()]);
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

                    $success = $this->categoriaRepository->delete($id);
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
