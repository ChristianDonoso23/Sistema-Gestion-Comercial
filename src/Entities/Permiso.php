<?php declare(strict_types=1);

namespace App\Entities;

class Permiso
{
    private int $id;
    private string $codigo;

    public function __construct(
        ?int $id = null,
        string $codigo
    )
    {
        $this->id = $id ?? 0;
        $this->codigo = $codigo;
    }

    /*Getters*/
    public function getId(): int          { return $this->id; }
    public function getCodigo(): string   { return $this->codigo; }

    /*Setters*/
    public function setId(int $id): void              { $this->id = $id; }
    public function setCodigo(string $codigo): void   { $this->codigo = $codigo; }
}
