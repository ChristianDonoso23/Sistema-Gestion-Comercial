<?php

declare(strict_types=1);

namespace App\Entities;

class ProductoDigital extends Producto
{
    private string $urlDescarga;
    private string $licencia;

    public function __construct(
        ?int $id = null,
        string $nombre,
        string $descripcion,
        string $precioUnitario,
        int $stock,
        int $idCategoria,
        string $urlDescarga,
        string $licencia
    ) {
        parent::__construct($id, $nombre, $descripcion, $precioUnitario, $stock, $idCategoria);
        $this->urlDescarga = $urlDescarga;
        $this->licencia = $licencia;
    }
    /* Getters */
    public function getUrlDescarga(): string { return $this->urlDescarga; }
    public function getLicencia(): string { return $this->licencia; }
    /* Setters */
    public function setUrlDescarga(string $urlDescarga): void { $this->urlDescarga = $urlDescarga; }
    public function setLicencia(string $licencia): void { $this->licencia = $licencia; }
}