<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Factura;
use PDO;

class FacturaRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): Factura
    {
        return new Factura(
            (int)$row['id'],
            (int)$row['idVenta'],
            (int)$row['numero'],
            $row['claveAcceso'],
            new \DateTime($row['fechaEmision']),
            $row['estado']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_factura_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $facturas = [];
            foreach ($rows as $row) {
                $facturas[] = $this->hydrate($row);
            }
            return $facturas;
        } catch (\Exception $e) {
            error_log("Error en findAll FacturaRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?Factura
    {
        try {
            $stmt = $this->db->prepare("CALL sp_find_factura(:id)");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById FacturaRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Factura) {
            throw new \InvalidArgumentException('Expected instance of Factura');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_create_factura(:idVenta, :numero, :claveAcceso, :fechaEmision, :estado)"
            );

            $params = [
                'idVenta' => $entity->getIdVenta(),
                'numero' => $entity->getNumero(),
                'claveAcceso' => $entity->getClaveAcceso(),
                'fechaEmision' => $entity->getFechaEmision()->format('Y-m-d'),
                'estado' => $entity->getEstado(),
            ];

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($result && isset($result['factura_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['factura_id']);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error en create FacturaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Factura) {
            throw new \InvalidArgumentException('Expected instance of Factura');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_update_factura(:id, :idVenta, :numero, :claveAcceso, :fechaEmision, :estado)"
            );

            $params = [
                'id' => $entity->getId(),
                'idVenta' => $entity->getIdVenta(),
                'numero' => $entity->getNumero(),
                'claveAcceso' => $entity->getClaveAcceso(),
                'fechaEmision' => $entity->getFechaEmision()->format('Y-m-d'),
                'estado' => $entity->getEstado(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update FacturaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_delete_factura(:id)");
            $ok = $stmt->execute(['id' => $id]);
            $stmt->closeCursor();
            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete FacturaRepository: " . $e->getMessage());
            return false;
        }
    }
}
