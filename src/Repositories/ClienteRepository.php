<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Cliente;
use App\Entities\PersonaNatural;
use App\Entities\PersonaJuridica;
use PDO;
use DateTime;

class ClienteRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Cliente
{
    // Si es PersonaNatural
    if (isset($row['nombre']) && $row['nombre'] !== null) {
        return new PersonaNatural(
            isset($row['id']) ? (int)$row['id'] : null,
            $row['email'],
            $row['telefono'],
            $row['direccion'],
            $row['nombre'],
            $row['apellido'],
            $row['cedula']
        );
    }

    // Si es PersonaJuridica
    if (isset($row['razonSocial']) && $row['razonSocial'] !== null) {
        return new PersonaJuridica(
            isset($row['id']) ? (int)$row['id'] : null,
            $row['email'],
            $row['telefono'],
            $row['direccion'],
            $row['razonSocial'],
            $row['ruc'],
            $row['representanteLegal']
        );
    }

    // Si sólo Cliente base (aunque Cliente es abstracta, por si acaso)
    return new Cliente(
        isset($row['id']) ? (int)$row['id'] : null,
        $row['email'],
        $row['telefono'],
        $row['direccion']
    );
}


    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_cliente_list()");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $clientes = [];
        foreach ($rows as $row) {
            $clientes[] = $this->hydrate($row);
        }
        return $clientes;
    }

    public function findById(int $id): ?Cliente
    {
        $stmt = $this->db->prepare("CALL sp_find_cliente(:id)");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Cliente) {
            throw new \InvalidArgumentException('Expected instance of Cliente');
        }

        // Detectamos si es PersonaNatural o PersonaJuridica para llamar al procedimiento correcto
        if ($entity instanceof PersonaNatural) {
            $stmt = $this->db->prepare("CALL sp_create_persona_natural(:email, :telefono, :direccion, :nombre, :apellido, :cedula)");
            $params = [
                'email' => $entity->getEmail(),
                'telefono' => $entity->getTelefono(),
                'direccion' => $entity->getDireccion(),
                'nombre' => $entity->getNombre(),
                'apellido' => $entity->getApellido(),
                'cedula' => $entity->getCedula(),
            ];
        } elseif ($entity instanceof PersonaJuridica) {
            $stmt = $this->db->prepare("CALL sp_create_persona_juridica(:email, :telefono, :direccion, :razonSocial, :ruc, :representanteLegal)");
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

        // Opcional: capturar el nuevo ID devuelto por el procedure si quieres asignarlo
        if ($ok) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (isset($result['cliente_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['cliente_id']);
            }
            return true;
        }

        return false;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Cliente) {
            throw new \InvalidArgumentException('Expected instance of Cliente');
        }

        if ($entity instanceof PersonaNatural) {
            $stmt = $this->db->prepare("CALL sp_update_persona_natural(:id, :email, :telefono, :direccion, :nombre, :apellido, :cedula)");
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
            $stmt = $this->db->prepare("CALL sp_update_persona_juridica(:id, :email, :telefono, :direccion, :razonSocial, :ruc, :representanteLegal)");
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
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_cliente(:id)");
        $ok = $stmt->execute(['id' => $id]);
        $stmt->closeCursor();

        return $ok;
    }
}
