<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\PersonaNatural;
use PDO;

class PersonaNaturalRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function hydrate(array $row): PersonaNatural
    {
        return new PersonaNatural(
            (int)$row['cliente_id'],
            $row['email'],
            $row['telefono'],
            $row['direccion'],
            $row['nombre'],
            $row['apellido'],
            $row['cedula']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_persona_natural_list()");
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
        $stmt = $this->db->prepare("CALL sp_persona_natural_find(:id)");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof PersonaNatural) {
            throw new \InvalidArgumentException('Expected instance of PersonaNatural');
        }
        $stmt = $this->db->prepare("CALL sp_persona_natural_create(:email, :telefono, :direccion, :nombre, :apellido, :cedula)");
        $ok = $stmt->execute([
            'email' => $entity->getEmail(),
            'telefono' => $entity->getTelefono(),
            'direccion' => $entity->getDireccion(),
            'nombre' => $entity->getNombre(),
            'apellido' => $entity->getApellido(),
            'cedula' => $entity->getCedula(),
        ]);
        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();
        return $ok;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof PersonaNatural) {
            throw new \InvalidArgumentException('Expected instance of PersonaNatural');
        }
        $stmt = $this->db->prepare("CALL sp_persona_natural_update(:id, :email, :telefono, :direccion, :nombre, :apellido, :cedula)");
        $ok = $stmt->execute([
            'id' => $entity->getId(),
            'email' => $entity->getEmail(),
            'telefono' => $entity->getTelefono(),
            'direccion' => $entity->getDireccion(),
            'nombre' => $entity->getNombre(),
            'apellido' => $entity->getApellido(),
            'cedula' => $entity->getCedula(),
        ]);
        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_persona_natural_delete(:id)");
        $ok = $stmt->execute(['id' => $id]);
        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();
        return $ok;
    }
}
