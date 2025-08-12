<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Venta;
use PDO;

class VentaRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Venta
    {
        // Manejar el estado vacío del ENUM
        $estado = trim($row['estado'] ?? '');
        if (empty($estado)) {
            $estado = 'borrador'; // Valor por defecto
        }
        
        return new Venta(
            (int)$row['id'],
            new \DateTime($row['fecha']),
            (int)$row['idCliente'],
            (float)$row['total'],
            $estado
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_venta_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $ventas = [];
            foreach ($rows as $row) {
                $ventas[] = $this->hydrate($row);
            }
            return $ventas;
        } catch (\Exception $e) {
            error_log("Error en findAll VentaRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?Venta
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_venta(?)");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById VentaRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Venta) {
            throw new \InvalidArgumentException('Expected instance of Venta');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_create_venta(?, ?, ?, ?)"
            );

            // Asegurar que el estado no esté vacío
            $estado = trim($entity->getEstado());
            if (empty($estado)) {
                $estado = 'borrador';
            }

            $params = [
                $entity->getFecha()->format('Y-m-d'),
                $entity->getIdCliente(),
                $entity->getTotal(),
                $estado
            ];

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($result && isset($result['venta_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['venta_id']);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error en create VentaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Venta) {
            throw new \InvalidArgumentException('Expected instance of Venta');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_update_venta(?, ?, ?, ?, ?)"
            );

            // Asegurar que el estado no esté vacío
            $estado = trim($entity->getEstado());
            if (empty($estado)) {
                $estado = 'borrador';
            }

            $params = [
                $entity->getId(),
                $entity->getFecha()->format('Y-m-d'),
                $entity->getTotal(),
                $entity->getIdCliente(),
                $estado
            ];

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result && ($result['affected_rows'] > 0);
        } catch (\Exception $e) {
            error_log("Error en update VentaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_venta(?)");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result && ($result['affected_rows'] > 0);
        } catch (\Exception $e) {
            error_log("Error en delete VentaRepository: " . $e->getMessage());
            return false;
        }
    }
}