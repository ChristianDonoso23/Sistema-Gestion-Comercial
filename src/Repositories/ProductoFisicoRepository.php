<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\ProductoFisico;
use PDO;

class ProductoFisicoRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): ProductoFisico
    {
        return new ProductoFisico(
            (int)$row['producto_id'],
            $row['nombre'],
            $row['descripcion'],
            (float)$row['precioUnitario'],
            (int)$row['stock'],
            (int)$row['idCategoria'],
            (float)$row['peso'],
            (float)$row['alto'],
            (float)$row['ancho'],
            (float)$row['profundidad']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_producto_fisico_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $productos = [];
            foreach ($rows as $row) {
                $productos[] = $this->hydrate($row);
            }
            return $productos;
        } catch (\Exception $e) {
            error_log("Error en findAll ProductoFisicoRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?ProductoFisico
    {
        try {
            $stmt = $this->db->prepare("CALL sp_producto_fisico_find(:p_id)");
            $stmt->execute(['p_id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById ProductoFisicoRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof ProductoFisico) {
            throw new \InvalidArgumentException('Expected instance of ProductoFisico');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_producto_fisico_create(:p_nombre, :p_descripcion, :p_precioUnitario, :p_stock, :p_idCategoria, :p_peso, :p_alto, :p_ancho, :p_profundidad)"
            );

            $params = [
                'p_nombre' => $entity->getNombre(),
                'p_descripcion' => $entity->getDescripcion(),
                'p_precioUnitario' => $entity->getPrecioUnitario(),
                'p_stock' => $entity->getStock(),
                'p_idCategoria' => $entity->getIdCategoria(),
                'p_peso' => $entity->getPeso(),
                'p_alto' => $entity->getAlto(),
                'p_ancho' => $entity->getAncho(),
                'p_profundidad' => $entity->getProfundidad(),
            ];

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($result && isset($result['producto_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['producto_id']);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error en create ProductoFisicoRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof ProductoFisico) {
            throw new \InvalidArgumentException('Expected instance of ProductoFisico');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_producto_fisico_update(:p_id, :p_nombre, :p_descripcion, :p_precioUnitario, :p_stock, :p_idCategoria, :p_peso, :p_alto, :p_ancho, :p_profundidad)"
            );

            $params = [
                'p_id' => $entity->getId(),
                'p_nombre' => $entity->getNombre(),
                'p_descripcion' => $entity->getDescripcion(),
                'p_precioUnitario' => $entity->getPrecioUnitario(),
                'p_stock' => $entity->getStock(),
                'p_idCategoria' => $entity->getIdCategoria(),
                'p_peso' => $entity->getPeso(),
                'p_alto' => $entity->getAlto(),
                'p_ancho' => $entity->getAncho(),
                'p_profundidad' => $entity->getProfundidad(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update ProductoFisicoRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_producto_fisico_delete(:p_id)");
            $ok = $stmt->execute(['p_id' => $id]);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete ProductoFisicoRepository: " . $e->getMessage());
            return false;
        }
    }
}
