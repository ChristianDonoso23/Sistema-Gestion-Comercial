<?php declare(strict_types=1);

namespace App\Entities;

class PersonaNatural extends Cliente
{
    private string $nombre;
    private string $apellido;
    private string $cedula;

    public function __construct(
        ?int $id = null,
        string $email,
        string $telefono,
        string $direccion,
        string $nombre,
        string $apellido,
        string $cedula
    ) {
        parent::__construct($id, $email, $telefono, $direccion);
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->cedula = $cedula;
    }

    /*Getters*/
    public function getNombre(): string     { return $this->nombre; }
    public function getApellido(): string   { return $this->apellido; }
    public function getCedula(): string     { return $this->cedula; }
    /*Setters*/
    public function setNombre(string $nombre): void       { $this->nombre = $nombre; }
    public function setApellido(string $apellido): void   { $this->apellido = $apellido; }
    public function setCedula(string $cedula): void       { $this->cedula = $cedula; }
}