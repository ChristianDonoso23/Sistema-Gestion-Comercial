<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Permiso;
use PDO;

class PermisoRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Permiso
    {
        return new Permiso(
            (int)$row['id'],
            $row['codigo']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_permiso_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return array_map(fn($row) => $this->hydrate($row), $rows);
        } catch (\Exception $e) {
            error_log("Error en findAll PermisoRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?Permiso
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_permiso(:id)");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById PermisoRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Permiso) {
            throw new \InvalidArgumentException('Expected instance of Permiso');
        }

        try {
            $stmt = $this->db->prepare("CALL sp_create_permiso(:codigo)");
            $stmt->execute(['codigo' => $entity->getCodigo()]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($result && isset($result['permiso_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['permiso_id']);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log("Error en create PermisoRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Permiso) {
            throw new \InvalidArgumentException('Expected instance of Permiso');
        }

        try {
            $stmt = $this->db->prepare("CALL sp_update_permiso(:id, :codigo)");

            $params = [
                'id' => $entity->getId(),
                'codigo' => $entity->getCodigo(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update PermisoRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_permiso(:id)");
            $ok = $stmt->execute(['id' => $id]);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete PermisoRepository: " . $e->getMessage());
            return false;
        }
    }
}
