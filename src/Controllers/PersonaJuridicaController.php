<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PersonaJuridicaRepository;
use App\Entities\PersonaJuridica;

class PersonaJuridicaController
{
    private PersonaJuridicaRepository $personaJuridicaRepository;

    public function __construct()
    {
        $this->personaJuridicaRepository = new PersonaJuridicaRepository();
    }

    private function personaJuridicaToArray(PersonaJuridica $persona): array
    {
        return [
            'id' => $persona->getId(),
            'email' => $persona->getEmail(),
            'telefono' => $persona->getTelefono(),
            'direccion' => $persona->getDireccion(),
            'razonSocial' => $persona->getRazonSocial(),
            'ruc' => $persona->getRuc(),
            'representanteLegal' => $persona->getRepresentanteLegal(),
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
                        $persona = $this->personaJuridicaRepository->findById((int)$_GET['id']);
                        echo json_encode($persona ? $this->personaJuridicaToArray($persona) : null);
                    } else {
                        $personas = $this->personaJuridicaRepository->findAll();
                        $list = array_map(fn(PersonaJuridica $p) => $this->personaJuridicaToArray($p), $personas);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $persona = new PersonaJuridica(
                        null,
                        $payload['email'] ?? '',
                        $payload['telefono'] ?? '',
                        $payload['direccion'] ?? '',
                        $payload['razonSocial'] ?? '',
                        $payload['ruc'] ?? '',
                        $payload['representanteLegal'] ?? ''
                    );

                    $success = $this->personaJuridicaRepository->create($persona);
                    echo json_encode(['success' => $success, 'id' => $persona->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $existingPersona = $this->personaJuridicaRepository->findById((int)$payload['id']);
                    if (!$existingPersona) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Persona Jurídica no encontrada']);
                        return;
                    }

                    $existingPersona->setEmail($payload['email'] ?? $existingPersona->getEmail());
                    $existingPersona->setTelefono($payload['telefono'] ?? $existingPersona->getTelefono());
                    $existingPersona->setDireccion($payload['direccion'] ?? $existingPersona->getDireccion());
                    $existingPersona->setRazonSocial($payload['razonSocial'] ?? $existingPersona->getRazonSocial());
                    $existingPersona->setRuc($payload['ruc'] ?? $existingPersona->getRuc());
                    $existingPersona->setRepresentanteLegal($payload['representanteLegal'] ?? $existingPersona->getRepresentanteLegal());

                    $success = $this->personaJuridicaRepository->update($existingPersona);
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

                    $success = $this->personaJuridicaRepository->delete($id);
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
