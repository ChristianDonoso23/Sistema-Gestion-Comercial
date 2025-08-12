<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UsuarioRepository;
use App\Entities\Usuario;

class UsuarioController
{
    private UsuarioRepository $usuarioRepository;

    public function __construct()
    {
        $this->usuarioRepository = new UsuarioRepository();
    }

    private function usuarioToArray(Usuario $usuario): array
    {
        return [
            'id' => $usuario->getId(),
            'username' => $usuario->getUsername(),
            'passwordHash' => $usuario->getPasswordHash(),
            'estado' => $usuario->getEstado(),
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
                        $usuario = $this->usuarioRepository->findById((int)$_GET['id']);
                        echo json_encode($usuario ? $this->usuarioToArray($usuario) : null);
                    } else {
                        $usuarios = $this->usuarioRepository->findAll();
                        $list = array_map(fn(Usuario $u) => $this->usuarioToArray($u), $usuarios);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    // Aquí podrías aplicar hash a la contraseña si es necesario
                    $passwordHash = $payload['passwordHash'] ?? '';
                    $estado = $payload['estado'] ?? 'activo';

                    $usuario = new Usuario(
                        null,
                        $payload['username'] ?? '',
                        $passwordHash,
                        $estado
                    );

                    $success = $this->usuarioRepository->create($usuario);
                    echo json_encode(['success' => $success]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingUsuario = $this->usuarioRepository->findById((int)$payload['id']);
                    if (!$existingUsuario) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Usuario no encontrado']);
                        return;
                    }

                    $existingUsuario->setUsername($payload['username'] ?? $existingUsuario->getUsername());
                    $existingUsuario->setPasswordHash($payload['passwordHash'] ?? $existingUsuario->getPasswordHash());
                    $existingUsuario->setEstado($payload['estado'] ?? $existingUsuario->getEstado());

                    $success = $this->usuarioRepository->update($existingUsuario);
                    echo json_encode(['success' => $success]);
                    break;

                case 'DELETE':
                    $id = $_GET['id'] ?? null;
                    if ($id === null) {
                        $payload = json_decode(file_get_contents('php://input'), true);
                        $id = $payload['id'] ?? null;
                    }

                    if ($id === null) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID requerido para eliminar']);
                        return;
                    }

                    $success = $this->usuarioRepository->delete((int)$id);
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
