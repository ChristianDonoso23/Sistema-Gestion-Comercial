<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\PersonaNatural;
use PDO;

class PersonaNaturalRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): PersonaNatural
    {
        return new PersonaNatural(
            (int)$row['cliente_id'],
            $row['email'],
            $row['telefono'],
            $row['direccion'],
            $row['nombre'],
            $row['apellido'],
            $row['cedula']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_persona_natural_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $personasNaturales = [];
            foreach ($rows as $row) {
                $personasNaturales[] = $this->hydrate($row);
            }
            return $personasNaturales;
        } catch (\Exception $e) {
            error_log("Error en findAll PersonaNaturalRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?PersonaNatural
    {
        try {
            $stmt = $this->db->prepare("CALL sp_persona_natural_find(:p_id)");
            $stmt->execute(['p_id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById PersonaNaturalRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof PersonaNatural) {
            throw new \InvalidArgumentException('Expected instance of PersonaNatural');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_persona_natural_create(:p_email, :p_telefono, :p_direccion, :p_nombre, :p_apellido, :p_cedula)"
            );

            $params = [
                'p_email' => $entity->getEmail(),
                'p_telefono' => $entity->getTelefono(),
                'p_direccion' => $entity->getDireccion(),
                'p_nombre' => $entity->getNombre(),
                'p_apellido' => $entity->getApellido(),
                'p_cedula' => $entity->getCedula(),
            ];

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($result && isset($result['cliente_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['cliente_id']);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error en create PersonaNaturalRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof PersonaNatural) {
            throw new \InvalidArgumentException('Expected instance of PersonaNatural');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_persona_natural_update(:p_id, :p_email, :p_telefono, :p_direccion, :p_nombre, :p_apellido, :p_cedula)"
            );

            $params = [
                'p_id' => $entity->getId(),
                'p_email' => $entity->getEmail(),
                'p_telefono' => $entity->getTelefono(),
                'p_direccion' => $entity->getDireccion(),
                'p_nombre' => $entity->getNombre(),
                'p_apellido' => $entity->getApellido(),
                'p_cedula' => $entity->getCedula(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update PersonaNaturalRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_persona_natural_delete(:p_id)");
            $ok = $stmt->execute(['p_id' => $id]);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete PersonaNaturalRepository: " . $e->getMessage());
            return false;
        }
    }
}
