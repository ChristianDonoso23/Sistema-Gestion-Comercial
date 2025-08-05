<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Producto;
use App\Entities\ProductoFisico;
use App\Entities\ProductoDigital;
use PDO;
use ReflectionClass;

class ProductoRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Producto
    {
        // Si tiene campos de ProductoFisico, crea objeto ProductoFisico
        if (!empty($row['peso']) || !empty($row['alto']) || !empty($row['ancho']) || !empty($row['profundidad'])) {
            return new ProductoFisico(
                (int)($row['id'] ?? 0),
                $row['nombre'] ?? '',
                $row['descripcion'] ?? '',
                (float)$row['precioUnitario'],
                (int)($row['stock'] ?? 0),
                (int)($row['idCategoria'] ?? 0),
                (float)($row['peso'] ?? 0),
                (float)($row['alto'] ?? 0),
                (float)($row['ancho'] ?? 0),
                (float)($row['profundidad'] ?? 0)
            );
        }

        // Si tiene campos de ProductoDigital, crea objeto ProductoDigital
        if (!empty($row['urlDescarga']) || !empty($row['licencia'])) {
            return new ProductoDigital(
                (int)($row['id'] ?? 0),
                $row['nombre'] ?? '',
                $row['descripcion'] ?? '',
                (float)$row['precioUnitario'],
                (int)($row['stock'] ?? 0),
                (int)($row['idCategoria'] ?? 0),
                $row['urlDescarga'] ?? '',
                $row['licencia'] ?? ''
            );
        }

        // Por defecto, objeto Producto base (abstracta, por si acaso)
        throw new \RuntimeException('No se pudo hidratar producto: tipo desconocido');
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_producto_list()");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $productos = [];
        foreach ($rows as $row) {
            $productos[] = $this->hydrate($row);
        }
        return $productos;
    }

    public function findById(int $id): ?Producto
    {
        $stmt = $this->db->prepare("CALL sp_find_producto(:id)");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Producto) {
            throw new \InvalidArgumentException('Se esperaba instancia de Producto');
        }

        if ($entity instanceof ProductoFisico) {
            $stmt = $this->db->prepare("CALL sp_create_producto_fisico(:nombre, :descripcion, :precioUnitario, :stock, :idCategoria, :peso, :alto, :ancho, :profundidad)");
            $params = [
                'nombre' => $entity->getNombre(),
                'descripcion' => $entity->getDescripcion(),
                'precioUnitario' => $entity->getPrecioUnitario(),
                'stock' => $entity->getStock(),
                'idCategoria' => $entity->getIdCategoria(),
                'peso' => $entity->getPeso(),
                'alto' => $entity->getAlto(),
                'ancho' => $entity->getAncho(),
                'profundidad' => $entity->getProfundidad(),
            ];
        } elseif ($entity instanceof ProductoDigital) {
            $stmt = $this->db->prepare("CALL sp_create_producto_digital(:nombre, :descripcion, :precioUnitario, :stock, :idCategoria, :urlDescarga, :licencia)");
            $params = [
                'nombre' => $entity->getNombre(),
                'descripcion' => $entity->getDescripcion(),
                'precioUnitario' => $entity->getPrecioUnitario(),
                'stock' => $entity->getStock(),
                'idCategoria' => $entity->getIdCategoria(),
                'urlDescarga' => $entity->getUrlDescarga(),
                'licencia' => $entity->getLicencia(),
            ];
        } else {
            throw new \InvalidArgumentException('Producto debe ser ProductoFisico o ProductoDigital');
        }

        $ok = $stmt->execute($params);

        if ($ok) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (isset($result['producto_id'])) {
                $reflection = new ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['producto_id']);
            }
            return true;
        }

        return false;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Producto) {
            throw new \InvalidArgumentException('Se esperaba instancia de Producto');
        }

        if ($entity instanceof ProductoFisico) {
            $stmt = $this->db->prepare("CALL sp_update_producto_fisico(:id, :nombre, :descripcion, :precioUnitario, :stock, :idCategoria, :peso, :alto, :ancho, :profundidad)");
            $params = [
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'descripcion' => $entity->getDescripcion(),
                'precioUnitario' => $entity->getPrecioUnitario(),
                'stock' => $entity->getStock(),
                'idCategoria' => $entity->getIdCategoria(),
                'peso' => $entity->getPeso(),
                'alto' => $entity->getAlto(),
                'ancho' => $entity->getAncho(),
                'profundidad' => $entity->getProfundidad(),
            ];
        } elseif ($entity instanceof ProductoDigital) {
            $stmt = $this->db->prepare("CALL sp_update_producto_digital(:id, :nombre, :descripcion, :precioUnitario, :stock, :idCategoria, :urlDescarga, :licencia)");
            $params = [
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'descripcion' => $entity->getDescripcion(),
                'precioUnitario' => $entity->getPrecioUnitario(),
                'stock' => $entity->getStock(),
                'idCategoria' => $entity->getIdCategoria(),
                'urlDescarga' => $entity->getUrlDescarga(),
                'licencia' => $entity->getLicencia(),
            ];
        } else {
            throw new \InvalidArgumentException('Producto debe ser ProductoFisico o ProductoDigital');
        }

        $ok = $stmt->execute($params);
        $stmt->closeCursor();

        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_producto(:id)");
        $ok = $stmt->execute(['id' => $id]);
        $stmt->closeCursor();

        return $ok;
    }
}
