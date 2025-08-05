<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Cliente;
use App\Entities\PersonaNatural;
use App\Entities\PersonaJuridica;
use PDO;

class ClienteRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Cliente
    {
        // Verificar si es PersonaNatural (tiene datos de persona natural)
        if (!empty($row['nombre']) || !empty($row['apellido']) || !empty($row['cedula'])) {
            return new PersonaNatural(
                (int)($row['cliente_id'] ?? 0),
                $row['email'] ?? '',
                $row['telefono'] ?? '',
                $row['direccion'] ?? '',
                $row['nombre'] ?? '',
                $row['apellido'] ?? '',
                $row['cedula'] ?? ''
            );
        }

        // Verificar si es PersonaJuridica (tiene datos de persona jurídica)
        if (!empty($row['razonSocial']) || !empty($row['ruc']) || !empty($row['representanteLegal'])) {
            return new PersonaJuridica(
                (int)($row['cliente_id'] ?? 0),
                $row['email'] ?? '',
                $row['telefono'] ?? '',
                $row['direccion'] ?? '',
                $row['razonSocial'] ?? '',
                $row['ruc'] ?? '',
                $row['representanteLegal'] ?? ''
            );
        }

        // Fallback - no debería llegar aquí con la estructura actual
        throw new \Exception('No se pudo determinar el tipo de cliente');
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_cliente_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $clientes = [];
            foreach ($rows as $row) {
                $clientes[] = $this->hydrate($row);
            }
            return $clientes;
        } catch (\Exception $e) {
            error_log("Error en findAll: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?Cliente
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_cliente(:id)");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Cliente) {
            throw new \InvalidArgumentException('Expected instance of Cliente');
        }

        try {
            if ($entity instanceof PersonaNatural) {
                $stmt = $this->db->prepare("CALL sp_persona_natural_create(:email, :telefono, :direccion, :nombre, :apellido, :cedula)");
                $params = [
                    'email' => $entity->getEmail(),
                    'telefono' => $entity->getTelefono(),
                    'direccion' => $entity->getDireccion(),
                    'nombre' => $entity->getNombre(),
                    'apellido' => $entity->getApellido(),
                    'cedula' => $entity->getCedula(),
                ];
            } elseif ($entity instanceof PersonaJuridica) {
                $stmt = $this->db->prepare("CALL sp_persona_juridica_create(:email, :telefono, :direccion, :razonSocial, :ruc, :representanteLegal)");
                $params = [
                    'email' => $entity->getEmail(),
                    'telefono' => $entity->getTelefono(),
                    'direccion' => $entity->getDireccion(),
                    'razonSocial' => $entity->getRazonSocial(),
                    'ruc' => $entity->getRuc(),
                    'representanteLegal' => $entity->getRepresentanteLegal(),
                ];
            } else {
                throw new \InvalidArgumentException('Cliente debe ser PersonaNatural o PersonaJuridica');
            }

            $ok = $stmt->execute($params);

            if ($ok) {
                // Capturar el ID que retorna el SP
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                if ($result && isset($result['cliente_id'])) {
                    // Usar reflexión para asignar el ID al objeto
                    $reflection = new \ReflectionClass($entity);
                    $prop = $reflection->getProperty('id');
                    $prop->setAccessible(true);
                    $prop->setValue($entity, (int)$result['cliente_id']);
                    
                    return true;
                } else {
                    error_log("Warning: No se pudo obtener el ID del cliente creado");
                    return false;
                }
            }

            $stmt->closeCursor();
            return false;

        } catch (\Exception $e) {
            error_log("Error en create: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Cliente) {
            throw new \InvalidArgumentException('Expected instance of Cliente');
        }

        try {
            if ($entity instanceof PersonaNatural) {
                $stmt = $this->db->prepare("CALL sp_persona_natural_update(:id, :email, :telefono, :direccion, :nombre, :apellido, :cedula)");
                $params = [
                    'id' => $entity->getId(),
                    'email' => $entity->getEmail(),
                    'telefono' => $entity->getTelefono(),
                    'direccion' => $entity->getDireccion(),
                    'nombre' => $entity->getNombre(),
                    'apellido' => $entity->getApellido(),
                    'cedula' => $entity->getCedula(),
                ];
            } elseif ($entity instanceof PersonaJuridica) {
                $stmt = $this->db->prepare("CALL sp_persona_juridica_update(:id, :email, :telefono, :direccion, :razonSocial, :ruc, :representanteLegal)");
                $params = [
                    'id' => $entity->getId(),
                    'email' => $entity->getEmail(),
                    'telefono' => $entity->getTelefono(),
                    'direccion' => $entity->getDireccion(),
                    'razonSocial' => $entity->getRazonSocial(),
                    'ruc' => $entity->getRuc(),
                    'representanteLegal' => $entity->getRepresentanteLegal(),
                ];
            } else {
                throw new \InvalidArgumentException('Cliente debe ser PersonaNatural o PersonaJuridica');
            }

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
            
        } catch (\Exception $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_cliente(:id)");
            $ok = $stmt->execute(['id' => $id]);
            $stmt->closeCursor();

            return $ok;
            
        } catch (\Exception $e) {
            error_log("Error en delete: " . $e->getMessage());
            return false;
        }
    }
}