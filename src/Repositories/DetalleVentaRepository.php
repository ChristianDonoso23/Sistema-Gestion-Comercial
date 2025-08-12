<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\DetalleVenta;
use PDO;

class DetalleVentaRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): DetalleVenta
    {
        return new DetalleVenta(
            (int)$row['idVenta'],
            (int)$row['lineNumber'],
            (int)$row['idProducto'],
            (int)$row['cantidad'],
            (float)$row['precioUnitario'],
            (float)$row['subtotal']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_detalle_venta_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return array_map(fn($row) => $this->hydrate($row), $rows);
        } catch (\Exception $e) {
            error_log("Error en findAll DetalleVentaRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?DetalleVenta
    {
        throw new \LogicException("Use findByCompositeId para DetalleVenta");
    }

    public function findByCompositeId(int $idVenta, int $lineNumber): ?DetalleVenta
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_detalle_venta(:idVenta, :lineNumber)");
            $stmt->execute(['idVenta' => $idVenta, 'lineNumber' => $lineNumber]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findByCompositeId DetalleVentaRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof DetalleVenta) {
            throw new \InvalidArgumentException('Expected instance of DetalleVenta');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_create_detalle_venta(:idVenta, :lineNumber, :idProducto, :cantidad, :precioUnitario, :subtotal)"
            );

            $params = [
                'idVenta' => $entity->getIdVenta(),
                'lineNumber' => $entity->getLineNumber(),
                'idProducto' => $entity->getIdProducto(),
                'cantidad' => $entity->getCantidad(),
                'precioUnitario' => $entity->getPrecioUnitario(),
                'subtotal' => $entity->getSubtotal(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en create DetalleVentaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof DetalleVenta) {
            throw new \InvalidArgumentException('Expected instance of DetalleVenta');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_update_detalle_venta(:idVenta, :lineNumber, :idProducto, :cantidad, :precioUnitario, :subtotal)"
            );

            $params = [
                'idVenta' => $entity->getIdVenta(),
                'lineNumber' => $entity->getLineNumber(),
                'idProducto' => $entity->getIdProducto(),
                'cantidad' => $entity->getCantidad(),
                'precioUnitario' => $entity->getPrecioUnitario(),
                'subtotal' => $entity->getSubtotal(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update DetalleVentaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        throw new \LogicException("Use deleteCompositeId para DetalleVenta");
    }

    public function deleteCompositeId(int $idVenta, int $lineNumber): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_detalle_venta(:idVenta, :lineNumber)");
            $ok = $stmt->execute(['idVenta' => $idVenta, 'lineNumber' => $lineNumber]);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en deleteCompositeId DetalleVentaRepository: " . $e->getMessage());
            return false;
        }
    }
}
