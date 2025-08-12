<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Rol;
use PDO;

class RolRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Rol
    {
        return new Rol(
            (int)$row['id'],
            $row['nombre']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_rol_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return array_map(fn($row) => $this->hydrate($row), $rows);
        } catch (\Exception $e) {
            error_log("Error en findAll RolRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?Rol
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_rol(:id)");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById RolRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Rol) {
            throw new \InvalidArgumentException('Expected instance of Rol');
        }

        try {
            $stmt = $this->db->prepare("CALL sp_create_rol(:nombre)");
            $stmt->execute(['nombre' => $entity->getNombre()]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($result && isset($result['rol_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['rol_id']);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log("Error en create RolRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Rol) {
            throw new \InvalidArgumentException('Expected instance of Rol');
        }

        try {
            $stmt = $this->db->prepare("CALL sp_update_rol(:id, :nombre)");

            $params = [
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update RolRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_rol(:id)");
            $ok = $stmt->execute(['id' => $id]);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete RolRepository: " . $e->getMessage());
            return false;
        }
    }
}
