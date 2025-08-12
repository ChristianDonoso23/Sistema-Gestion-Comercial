<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Usuario;
use PDO;

class UsuarioRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Usuario
    {
        return new Usuario(
            (int)$row['id'],
            $row['username'],
            $row['passwordHash'],
            $row['estado']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_usuario_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $usuarios = [];
            foreach ($rows as $row) {
                $usuarios[] = $this->hydrate($row);
            }
            return $usuarios;
        } catch (\Exception $e) {
            error_log("Error en findAll UsuarioRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?Usuario
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_usuario(:id)");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById UsuarioRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Usuario) {
            throw new \InvalidArgumentException('Expected instance of Usuario');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_create_usuario(:username, :passwordHash, :estado)"
            );

            $params = [
                'username' => $entity->getUsername(),
                'passwordHash' => $entity->getPasswordHash(),
                'estado' => $entity->getEstado(),
            ];

            return $stmt->execute($params);
        } catch (\Exception $e) {
            error_log("Error en create UsuarioRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Usuario) {
            throw new \InvalidArgumentException('Expected instance of Usuario');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_update_usuario(:id, :username, :passwordHash, :estado)"
            );

            $params = [
                'id' => $entity->getId(),
                'username' => $entity->getUsername(),
                'passwordHash' => $entity->getPasswordHash(),
                'estado' => $entity->getEstado(),
            ];

            return $stmt->execute($params);
        } catch (\Exception $e) {
            error_log("Error en update UsuarioRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_usuario(:id)");
            $ok = $stmt->execute(['id' => $id]);
            $stmt->closeCursor();
            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete UsuarioRepository: " . $e->getMessage());
            return false;
        }
    }
}
