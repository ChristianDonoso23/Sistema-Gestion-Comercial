<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\PersonaNatural;
use App\Entities\PersonaJuridica;
use App\Repositories\ClienteRepository;

class ClienteController
{
    private ClienteRepository $clienteRepository;

    public function __construct()
    {
        $this->clienteRepository = new ClienteRepository();
    }

    private function clienteToArray($cliente): array
    {
        // Detecta el tipo de cliente y arma el array apropiado
        if ($cliente instanceof PersonaNatural) {
            return [
                'id' => $cliente->getId(),
                'email' => $cliente->getEmail(),
                'telefono' => $cliente->getTelefono(),
                'direccion' => $cliente->getDireccion(),
                'tipo' => 'natural',
                'nombre' => $cliente->getNombre(),
                'apellido' => $cliente->getApellido(),
                'cedula' => $cliente->getCedula(),
            ];
        } elseif ($cliente instanceof PersonaJuridica) {
            return [
                'id' => $cliente->getId(),
                'email' => $cliente->getEmail(),
                'telefono' => $cliente->getTelefono(),
                'direccion' => $cliente->getDireccion(),
                'tipo' => 'juridica',
                'razonSocial' => $cliente->getRazonSocial(),
                'ruc' => $cliente->getRuc(),
                'representanteLegal' => $cliente->getRepresentanteLegal(),
            ];
        }
        return [];
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $cliente = $this->clienteRepository->findById((int)$_GET['id']);
                echo json_encode($cliente ? $this->clienteToArray($cliente) : null);
            } else {
                $clientes = $this->clienteRepository->findAll();
                $list = array_map(fn($c) => $this->clienteToArray($c), $clientes);
                echo json_encode($list);
            }
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        if ($method === 'POST') {
            if (!isset($payload['tipo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Tipo de cliente requerido (natural o juridica)']);
                return;
            }

            // Crear instancia según tipo
            if ($payload['tipo'] === 'natural') {
                $cliente = new PersonaNatural(
                    null,
                    $payload['email'],
                    $payload['telefono'],
                    $payload['direccion'],
                    $payload['nombre'],
                    $payload['apellido'],
                    $payload['cedula']
                );
            } elseif ($payload['tipo'] === 'juridica') {
                $cliente = new PersonaJuridica(
                    null,
                    $payload['email'],
                    $payload['telefono'],
                    $payload['direccion'],
                    $payload['razonSocial'],
                    $payload['ruc'],
                    $payload['representanteLegal']
                );
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Tipo de cliente inválido']);
                return;
            }

            $success = $this->clienteRepository->create($cliente);
            echo json_encode(['success' => $success]);
            return;
        }

        if ($method === 'PUT') {
            if (!isset($payload['id'], $payload['tipo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID y tipo de cliente requeridos para actualizar']);
                return;
            }

            // Buscar cliente existente
            $clienteExistente = $this->clienteRepository->findById((int)$payload['id']);
            if (!$clienteExistente) {
                http_response_code(404);
                echo json_encode(['error' => 'Cliente no encontrado']);
                return;
            }

            // Actualizar según tipo
            if ($payload['tipo'] === 'natural' && $clienteExistente instanceof PersonaNatural) {
                $clienteExistente->setEmail($payload['email']);
                $clienteExistente->setTelefono($payload['telefono']);
                $clienteExistente->setDireccion($payload['direccion']);
                $clienteExistente->setNombre($payload['nombre']);
                $clienteExistente->setApellido($payload['apellido']);
                $clienteExistente->setCedula($payload['cedula']);
            } elseif ($payload['tipo'] === 'juridica' && $clienteExistente instanceof PersonaJuridica) {
                $clienteExistente->setEmail($payload['email']);
                $clienteExistente->setTelefono($payload['telefono']);
                $clienteExistente->setDireccion($payload['direccion']);
                $clienteExistente->setRazonSocial($payload['razonSocial']);
                $clienteExistente->setRuc($payload['ruc']);
                $clienteExistente->setRepresentanteLegal($payload['representanteLegal']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Tipo de cliente no coincide con el registro existente']);
                return;
            }

            $success = $this->clienteRepository->update($clienteExistente);
            echo json_encode(['success' => $success]);
            return;
        }

        if ($method === 'DELETE') {
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID requerido para eliminar']);
                return;
            }

            $success = $this->clienteRepository->delete((int)$_GET['id']);
            echo json_encode(['success' => $success]);
            return;
        }

        http_response_code(405);
        echo json_encode(['error' => 'Método HTTP no soportado']);
    }
}
