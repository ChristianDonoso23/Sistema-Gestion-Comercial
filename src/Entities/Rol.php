<?php

declare(strict_types=1);

namespace App\Entities;

class Rol
{
    private int $id;
    private string $nombre;

    public function __construct(
        ?int $id = null,
        string $nombre
    )
    {
        $this->id = $id ?? 0;
        $this->nombre = $nombre;
    }
    /* Getters */
    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    /* Setters */
    public function setId(int $id): void { $this->id = $id; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
}
