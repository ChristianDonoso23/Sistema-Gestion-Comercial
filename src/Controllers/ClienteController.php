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

    // Convierte objeto Cliente a array para JSON
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

        // Caso base, sólo cliente genérico (si fuera necesario)
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

                    $cliente = null;

                    if (isset($payload['tipo'])) {
                        if ($payload['tipo'] === 'PersonaNatural') {
                            $cliente = new PersonaNatural(
                                0,
                                $payload['email'] ?? '',
                                $payload['telefono'] ?? '',
                                $payload['direccion'] ?? '',
                                $payload['nombre'] ?? '',
                                $payload['apellido'] ?? '',
                                $payload['cedula'] ?? ''
                            );
                        } elseif ($payload['tipo'] === 'PersonaJuridica') {
                            $cliente = new PersonaJuridica(
                                0,
                                $payload['email'] ?? '',
                                $payload['telefono'] ?? '',
                                $payload['direccion'] ?? '',
                                $payload['razonSocial'] ?? '',
                                $payload['ruc'] ?? '',
                                $payload['representanteLegal'] ?? ''
                            );
                        } else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Tipo de cliente inválido']);
                            return;
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Tipo de cliente no especificado']);
                        return;
                    }

                    $success = $this->clienteRepository->create($cliente);
                    echo json_encode(['success' => $success, 'id' => $cliente->getId()]);
                    break;

                // Puedes agregar PUT y DELETE más adelante
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
