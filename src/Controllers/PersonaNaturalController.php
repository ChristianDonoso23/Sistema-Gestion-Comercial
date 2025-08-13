<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PersonaNaturalRepository;
use App\Entities\PersonaNatural;

class PersonaNaturalController
{
    private PersonaNaturalRepository $personaNaturalRepository;

    public function __construct()
    {
        $this->personaNaturalRepository = new PersonaNaturalRepository();
    }

    private function personaNaturalToArray(PersonaNatural $persona): array
    {
        return [
            'id' => $persona->getId(),
            'email' => $persona->getEmail(),
            'telefono' => $persona->getTelefono(),
            'direccion' => $persona->getDireccion(),
            'nombre' => $persona->getNombre(),
            'apellido' => $persona->getApellido(),
            'cedula' => $persona->getCedula(),
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
                        $persona = $this->personaNaturalRepository->findById((int)$_GET['id']);
                        echo json_encode($persona ? $this->personaNaturalToArray($persona) : null);
                    } else {
                        $personas = $this->personaNaturalRepository->findAll();
                        $list = array_map(fn(PersonaNatural $p) => $this->personaNaturalToArray($p), $personas);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $persona = new PersonaNatural(
                        null,
                        $payload['email'] ?? '',
                        $payload['telefono'] ?? '',
                        $payload['direccion'] ?? '',
                        $payload['nombre'] ?? '',
                        $payload['apellido'] ?? '',
                        $payload['cedula'] ?? ''
                    );

                    $success = $this->personaNaturalRepository->create($persona);
                    echo json_encode(['success' => $success, 'id' => $persona->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingPersona = $this->personaNaturalRepository->findById((int)$payload['id']);
                    if (!$existingPersona) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Persona natural no encontrada']);
                        return;
                    }

                    $existingPersona->setEmail($payload['email'] ?? $existingPersona->getEmail());
                    $existingPersona->setTelefono($payload['telefono'] ?? $existingPersona->getTelefono());
                    $existingPersona->setDireccion($payload['direccion'] ?? $existingPersona->getDireccion());
                    $existingPersona->setNombre($payload['nombre'] ?? $existingPersona->getNombre());
                    $existingPersona->setApellido($payload['apellido'] ?? $existingPersona->getApellido());
                    $existingPersona->setCedula($payload['cedula'] ?? $existingPersona->getCedula());

                    $success = $this->personaNaturalRepository->update($existingPersona);
                    echo json_encode(['success' => $success, 'id' => $existingPersona->getId()]);
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

                    $success = $this->personaNaturalRepository->delete($id);
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
