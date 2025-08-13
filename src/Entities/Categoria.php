<?php declare(strict_types=1);

namespace App\Entities;

class Categoria
{
    private int $id;
    private string $nombre;
    private string $descripcion;
    private string $estado;
    private ?int $idPadre;

    public function __construct(
        ?int $id = null,
        string $nombre,
        string $descripcion,
        string $estado,
        ?int $idPadre = null
    ) {
        $this->id = $id ?? 0;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->estado = $estado;
        $this->idPadre = $idPadre;
    }

    /* Getters */
    public function getId(): int                { return $this->id; }
    public function getNombre(): string         { return $this->nombre; }
    public function getDescripcion(): string    { return $this->descripcion; }
    public function getEstado(): string         { return $this->estado; }
    public function getIdPadre(): ?int          { return $this->idPadre; }
    
    /* Setters */
    public function setId(int $id): void                        { $this->id = $id; }
    public function setNombre(string $nombre): void             { $this->nombre = $nombre; }
    public function setDescripcion(string $descripcion): void   { $this->descripcion = $descripcion; }
    public function setEstado(string $estado): void             { $this->estado = $estado; }
    public function setIdPadre(?int $idPadre): void             { $this->idPadre = $idPadre; }
}