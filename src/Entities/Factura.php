<?php

declare(strict_types=1);

namespace App\Entities;

class Factura
{
    private int $id;
    private int $idVenta;
    private int $numero;
    private string $claveAcceso;
    private \DateTime $fechaEmision;
    private string $estado;

    public function __construct(
        ?int $id = null,
        int $idVenta,
        int $numero,
        string $claveAcceso,
        \DateTime $fechaEmision,
        string $estado
    )
    {
        $this->id = $id ?? 0;
        $this->idVenta = $idVenta;
        $this->numero = $numero;
        $this->claveAcceso = $claveAcceso;
        $this->fechaEmision = $fechaEmision;
        $this->estado = $estado;
    }
    /* Getters */
    public function getId(): int { return $this->id; }
    public function getIdVenta(): int { return $this->idVenta; }
    public function getNumero(): int { return $this->numero; }
    public function getClaveAcceso(): string { return $this->claveAcceso; }
    public function getFechaEmision(): \DateTime { return $this->fechaEmision; }
    public function getEstado(): string { return $this->estado; }
    /* Setters */
    public function setId(int $id): void { $this->id = $id; }
    public function setIdVenta(int $idVenta): void { $this->idVenta = $idVenta; }
    public function setNumero(int $numero): void { $this->numero = $numero; }
    public function setClaveAcceso(string $claveAcceso): void { $this->claveAcceso = $claveAcceso; }
    public function setFechaEmision(\DateTime $fechaEmision): void { $this->fechaEmision = $fechaEmision; }
    public function setEstado(string $estado): void { $this->estado = $estado; }
}