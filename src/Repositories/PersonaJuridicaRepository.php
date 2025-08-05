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
        $stmt = $this->db->query("CALL sp_persona_juridica_list()");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->hydrate($row);
        }
        return $out;
    }

    public function findById(int $id): ?object
    {
        $stmt = $this->db->prepare("CALL sp_persona_juridica_find(:id)");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof PersonaJuridica) {
            throw new \InvalidArgumentException('Expected instance of PersonaJuridica');
        }
        $stmt = $this->db->prepare("CALL sp_persona_juridica_create(:email, :telefono, :direccion, :razonSocial, :ruc, :representanteLegal)");
        $ok = $stmt->execute([
            'email' => $entity->getEmail(),
            'telefono' => $entity->getTelefono(),
            'direccion' => $entity->getDireccion(),
            'razonSocial' => $entity->getRazonSocial(),
            'ruc' => $entity->getRuc(),
            'representanteLegal' => $entity->getRepresentanteLegal(),
        ]);
        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();
        return $ok;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof PersonaJuridica) {
            throw new \InvalidArgumentException('Expected instance of PersonaJuridica');
        }
        $stmt = $this->db->prepare("CALL sp_persona_juridica_update(:id, :email, :telefono, :direccion, :razonSocial, :ruc, :representanteLegal)");
        $ok = $stmt->execute([
            'id' => $entity->getId(),
            'email' => $entity->getEmail(),
            'telefono' => $entity->getTelefono(),
            'direccion' => $entity->getDireccion(),
            'razonSocial' => $entity->getRazonSocial(),
            'ruc' => $entity->getRuc(),
            'representanteLegal' => $entity->getRepresentanteLegal(),
        ]);
        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_persona_juridica_delete(:id)");
        $ok = $stmt->execute(['id' => $id]);
        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();
        return $ok;
    }
}
