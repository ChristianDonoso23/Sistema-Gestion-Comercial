<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Categoria;
use PDO;

class CategoriaRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Categoria
    {
        return new Categoria(
            (int)$row['id'],
            $row['nombre'],
            $row['descripcion'],
            $row['estado'],
            isset($row['idPadre']) ? (int)$row['idPadre'] : null
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_categoria_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $categorias = [];
            foreach ($rows as $row) {
                $categorias[] = $this->hydrate($row);
            }
            return $categorias;
        } catch (\Exception $e) {
            error_log("Error en findAll CategoriaRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?Categoria
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_categoria(:id)");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById CategoriaRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Categoria) {
            throw new \InvalidArgumentException('Expected instance of Categoria');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_create_categoria(:nombre, :descripcion, :estado, :idPadre)"
            );

            $params = [
                'nombre' => $entity->getNombre(),
                'descripcion' => $entity->getDescripcion(),
                'estado' => $entity->getEstado(),
                'idPadre' => $entity->getIdPadre(),
            ];

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($result && isset($result['categoria_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['categoria_id']);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error en create CategoriaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Categoria) {
            throw new \InvalidArgumentException('Expected instance of Categoria');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_update_categoria(:id, :nombre, :descripcion, :estado, :idPadre)"
            );

            $params = [
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'descripcion' => $entity->getDescripcion(),
                'estado' => $entity->getEstado(),
                'idPadre' => $entity->getIdPadre(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update CategoriaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_categoria(:id)");
            $ok = $stmt->execute(['id' => $id]);
            $stmt->closeCursor();
            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete CategoriaRepository: " . $e->getMessage());
            return false;
        }
    }
}
