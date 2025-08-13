<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\ProductoDigital;
use PDO;

class ProductoDigitalRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): ProductoDigital
    {
        return new ProductoDigital(
            (int)$row['producto_id'],
            $row['nombre'],
            $row['descripcion'],
            (float)$row['precioUnitario'],
            (int)$row['stock'],
            (int)$row['idCategoria'],
            $row['urlDescarga'],
            $row['licencia']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_producto_digital_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $productos = [];
            foreach ($rows as $row) {
                $productos[] = $this->hydrate($row);
            }
            return $productos;
        } catch (\Exception $e) {
            error_log("Error en findAll ProductoDigitalRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?ProductoDigital
    {
        try {
            $stmt = $this->db->prepare("CALL sp_producto_digital_find(:p_id)");
            $stmt->execute(['p_id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById ProductoDigitalRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof ProductoDigital) {
            throw new \InvalidArgumentException('Expected instance of ProductoDigital');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_producto_digital_create(:p_nombre, :p_descripcion, :p_precioUnitario, :p_stock, :p_idCategoria, :p_urlDescarga, :p_licencia)"
            );

            $params = [
                'p_nombre' => $entity->getNombre(),
                'p_descripcion' => $entity->getDescripcion(),
                'p_precioUnitario' => $entity->getPrecioUnitario(),
                'p_stock' => $entity->getStock(),
                'p_idCategoria' => $entity->getIdCategoria(),
                'p_urlDescarga' => $entity->getUrlDescarga(),
                'p_licencia' => $entity->getLicencia(),
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
            error_log("Error en create ProductoDigitalRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof ProductoDigital) {
            throw new \InvalidArgumentException('Expected instance of ProductoDigital');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_producto_digital_update(:p_id, :p_nombre, :p_descripcion, :p_precioUnitario, :p_stock, :p_idCategoria, :p_urlDescarga, :p_licencia)"
            );

            $params = [
                'p_id' => $entity->getId(),
                'p_nombre' => $entity->getNombre(),
                'p_descripcion' => $entity->getDescripcion(),
                'p_precioUnitario' => $entity->getPrecioUnitario(),
                'p_stock' => $entity->getStock(),
                'p_idCategoria' => $entity->getIdCategoria(),
                'p_urlDescarga' => $entity->getUrlDescarga(),
                'p_licencia' => $entity->getLicencia(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update ProductoDigitalRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_producto_digital_delete(:p_id)");
            $ok = $stmt->execute(['p_id' => $id]);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete ProductoDigitalRepository: " . $e->getMessage());
            return false;
        }
    }
}
