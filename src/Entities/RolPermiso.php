<?php

declare(strict_types=1);

namespace App\Entities;

class RolPermiso
{
    private int $idRol;
    private int $idPermiso;

    public function __construct(
        int $idRol, 
        int $idPermiso
    )
    {
        $this->idRol = $idRol;
        $this->idPermiso = $idPermiso;
    }
    /*Getters*/
    public function getIdRol(): int { return $this->idRol; }
    public function getIdPermiso(): int { return $this->idPermiso; }
    /*Setters*/
    public function setIdRol(int $idRol): void { $this->idRol = $idRol; }
    public function setIdPermiso(int $idPermiso): void { $this->idPermiso = $idPermiso; }
}
