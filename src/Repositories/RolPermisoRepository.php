<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\RolPermiso;
use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use PDO;

class RolPermisoRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): RolPermiso
    {
        return new RolPermiso(
            (int)$row['idRol'],
            (int)$row['idPermiso']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_rolpermiso_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return array_map(fn($row) => $this->hydrate($row), $rows);
        } catch (\Exception $e) {
            error_log("Error en findAll RolPermisoRepository: " . $e->getMessage());
            return [];
        }
    }

    // No implementamos findById porque es compuesta
    public function findByCompositeId(int $idRol, int $idPermiso): ?RolPermiso
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_rolpermiso(:idRol, :idPermiso)");
            $stmt->execute(['idRol' => $idRol, 'idPermiso' => $idPermiso]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findByCompositeId RolPermisoRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof RolPermiso) {
            throw new \InvalidArgumentException('Expected instance of RolPermiso');
        }

        try {
            $stmt = $this->db->prepare("CALL sp_create_rolpermiso(:idRol, :idPermiso)");
            return $stmt->execute([
                'idRol' => $entity->getIdRol(),
                'idPermiso' => $entity->getIdPermiso(),
            ]);
        } catch (\Exception $e) {
            error_log("Error en create RolPermisoRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        // No tiene sentido actualizar la relación, eliminar y crear es mejor
        throw new \LogicException("No implementado: use delete + create para actualizar RolPermiso");
    }

    public function delete(int $id): bool
    {
        throw new \LogicException("Use deleteCompositeId para RolPermiso");
    }

    public function deleteCompositeId(int $idRol, int $idPermiso): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_rolpermiso(:idRol, :idPermiso)");
            return $stmt->execute(['idRol' => $idRol, 'idPermiso' => $idPermiso]);
        } catch (\Exception $e) {
            error_log("Error en deleteCompositeId RolPermisoRepository: " . $e->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?RolPermiso
    {
        throw new \LogicException("No implementado: use findByCompositeId para RolPermiso");
    }
}
