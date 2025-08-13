<?php declare(strict_types=1);

namespace App\Entities;

class Usuario
{
    private int $id;
    private string $username;
    private string $passwordHash;
    private string $estado;

    public function __construct(
        ?int $id = null,
        string $username,
        string $passwordHash,
        string $estado
    )
    {
        $this->id = $id ?? 0;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->estado = $estado;
    }

    /* Getters */
    public function getId(): int                {return $this->id; }
    public function getUsername(): string       {return $this->username; }
    public function getPasswordHash(): string   {return $this->passwordHash; }
    public function getEstado(): string         {return $this->estado; }
    /* Setters */
    public function setId(int $id): void                          { $this->id = $id; }
    public function setUsername(string $username): void           { $this->username = $username; }
    public function setPasswordHash(string $passwordHash): void   { $this->passwordHash = $passwordHash; }
    public function setEstado(string $estado): void               { $this->estado = $estado; }
}