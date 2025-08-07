<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ClienteRepository;
use App\Entities\PersonaNatural;
use App\Entities\PersonaJuridica;
use App\Entities\Cliente;

class ClienteController
{
    private ClienteRepository $clienteRepository;

    public function __construct()
    {
        $this->clienteRepository = new ClienteRepository();
    }

    private function clienteToArray(Cliente $cliente): array
    {
        if ($cliente instanceof PersonaNatural) {
            return [
                'id' => $cliente->getId(),
                'tipo' => 'PersonaNatural',
                'email' => $cliente->getEmail(),
                'telefono' => $cliente->getTelefono(),
                'direccion' => $cliente->getDireccion(),
                'nombre' => $cliente->getNombre(),
                'apellido' => $cliente->getApellido(),
                'cedula' => $cliente->getCedula(),
            ];
        }

        if ($cliente instanceof PersonaJuridica) {
            return [
                'id' => $cliente->getId(),
                'tipo' => 'PersonaJuridica',
                'email' => $cliente->getEmail(),
                'telefono' => $cliente->getTelefono(),
                'direccion' => $cliente->getDireccion(),
                'razonSocial' => $cliente->getRazonSocial(),
                'ruc' => $cliente->getRuc(),
                'representanteLegal' => $cliente->getRepresentanteLegal(),
            ];
        }

        return [
            'id' => $cliente->getId(),
            'tipo' => 'Cliente',
            'email' => $cliente->getEmail(),
            'telefono' => $cliente->getTelefono(),
            'direccion' => $cliente->getDireccion(),
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
                        $cliente = $this->clienteRepository->findById((int)$_GET['id']);
                        echo json_encode($cliente ? $this->clienteToArray($cliente) : null);
                    } else {
                        $clientes = $this->clienteRepository->findAll();
                        $list = array_map(fn(Cliente $c) => $this->clienteToArray($c), $clientes);
                        echo json_encode($list);
                    }
                    break;

                case 'POST':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload)) {
                        throw new \InvalidArgumentException('Payload JSON inválido');
                    }

                    $cliente = $this->createClienteFromPayload($payload);
                    $success = $this->clienteRepository->create($cliente);
                    echo json_encode(['success' => $success, 'id' => $cliente->getId()]);
                    break;

                case 'PUT':
                    $payload = json_decode(file_get_contents('php://input'), true);
                    if (!is_array($payload) || !isset($payload['id'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID y datos para actualización requeridos']);
                        return;
                    }

                    $clienteExistente = $this->clienteRepository->findById((int)$payload['id']);
                    if (!$clienteExistente) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Cliente no encontrado']);
                        return;
                    }

                    $clienteActualizado = $this->updateClienteFromPayload($clienteExistente, $payload);
                    $success = $this->clienteRepository->update($clienteActualizado);
                    echo json_encode(['success' => $success, 'id' => $clienteActualizado->getId()]);
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

                    $success = $this->clienteRepository->delete((int)$id);
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

    private function createClienteFromPayload(array $payload): Cliente
    {
        if (!isset($payload['tipo'])) {
            throw new \InvalidArgumentException('Tipo de cliente no especificado');
        }

        return match ($payload['tipo']) {
            'PersonaNatural' => new PersonaNatural(
                0,
                $payload['email'] ?? '',
                $payload['telefono'] ?? '',
                $payload['direccion'] ?? '',
                $payload['nombre'] ?? '',
                $payload['apellido'] ?? '',
                $payload['cedula'] ?? ''
            ),
            'PersonaJuridica' => new PersonaJuridica(
                0,
                $payload['email'] ?? '',
                $payload['telefono'] ?? '',
                $payload['direccion'] ?? '',
                $payload['razonSocial'] ?? '',
                $payload['ruc'] ?? '',
                $payload['representanteLegal'] ?? ''
            ),
            default => throw new \InvalidArgumentException('Tipo de cliente inválido'),
        };
    }

    private function updateClienteFromPayload(Cliente $cliente, array $payload): Cliente
    {
        $cliente->setEmail($payload['email'] ?? $cliente->getEmail());
        $cliente->setTelefono($payload['telefono'] ?? $cliente->getTelefono());
        $cliente->setDireccion($payload['direccion'] ?? $cliente->getDireccion());

        if ($cliente instanceof PersonaNatural) {
            $cliente->setNombre($payload['nombre'] ?? $cliente->getNombre());
            $cliente->setApellido($payload['apellido'] ?? $cliente->getApellido());
            $cliente->setCedula($payload['cedula'] ?? $cliente->getCedula());
        } elseif ($cliente instanceof PersonaJuridica) {
            $cliente->setRazonSocial($payload['razonSocial'] ?? $cliente->getRazonSocial());
            $cliente->setRuc($payload['ruc'] ?? $cliente->getRuc());
            $cliente->setRepresentanteLegal($payload['representanteLegal'] ?? $cliente->getRepresentanteLegal());
        }

        return $cliente;
    }
}
