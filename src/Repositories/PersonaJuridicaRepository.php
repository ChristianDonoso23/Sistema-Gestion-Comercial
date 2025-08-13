<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\PersonaJuridica;
use PDO;

class PersonaJuridicaRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): PersonaJuridica
    {
        return new PersonaJuridica(
            (int)$row['cliente_id'],
            $row['email'],
            $row['telefono'],
            $row['direccion'],
            $row['razonSocial'],
            $row['ruc'],
            $row['representanteLegal']
        );
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("CALL sp_persona_juridica_list()");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $personasJuridicas = [];
            foreach ($rows as $row) {
                $personasJuridicas[] = $this->hydrate($row);
            }
            return $personasJuridicas;
        } catch (\Exception $e) {
            error_log("Error en findAll PersonaJuridicaRepository: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?PersonaJuridica
    {
        try {
            $stmt = $this->db->prepare("CALL sp_persona_juridica_find(:p_id)");
            $stmt->execute(['p_id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $row ? $this->hydrate($row) : null;
        } catch (\Exception $e) {
            error_log("Error en findById PersonaJuridicaRepository: " . $e->getMessage());
            return null;
        }
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof PersonaJuridica) {
            throw new \InvalidArgumentException('Expected instance of PersonaJuridica');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_persona_juridica_create(:p_email, :p_telefono, :p_direccion, :p_razonSocial, :p_ruc, :p_representanteLegal)"
            );

            $params = [
                'p_email' => $entity->getEmail(),
                'p_telefono' => $entity->getTelefono(),
                'p_direccion' => $entity->getDireccion(),
                'p_razonSocial' => $entity->getRazonSocial(),
                'p_ruc' => $entity->getRuc(),
                'p_representanteLegal' => $entity->getRepresentanteLegal(),
            ];

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($result && isset($result['cliente_id'])) {
                $reflection = new \ReflectionClass($entity);
                $prop = $reflection->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, (int)$result['cliente_id']);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error en create PersonaJuridicaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof PersonaJuridica) {
            throw new \InvalidArgumentException('Expected instance of PersonaJuridica');
        }

        try {
            $stmt = $this->db->prepare(
                "CALL sp_persona_juridica_update(:p_id, :p_email, :p_telefono, :p_direccion, :p_razonSocial, :p_ruc, :p_representanteLegal)"
            );

            $params = [
                'p_id' => $entity->getId(),
                'p_email' => $entity->getEmail(),
                'p_telefono' => $entity->getTelefono(),
                'p_direccion' => $entity->getDireccion(),
                'p_razonSocial' => $entity->getRazonSocial(),
                'p_ruc' => $entity->getRuc(),
                'p_representanteLegal' => $entity->getRepresentanteLegal(),
            ];

            $ok = $stmt->execute($params);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en update PersonaJuridicaRepository: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("CALL sp_persona_juridica_delete(:p_id)");
            $ok = $stmt->execute(['p_id' => $id]);
            $stmt->closeCursor();

            return $ok;
        } catch (\Exception $e) {
            error_log("Error en delete PersonaJuridicaRepository: " . $e->getMessage());
            return false;
        }
    }
}
