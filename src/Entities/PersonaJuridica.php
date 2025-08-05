<?php

declare(strict_types=1);

namespace App\Entities;

class PersonaJuridica extends Cliente
{
    private string $razonSocial;
    private string $ruc;
    private string $representanteLegal;

    public function __construct(
        ?int $id = null,
        string $email,
        string $telefono,
        string $direccion,
        string $razonSocial,
        string $ruc,
        string $representanteLegal
    ) {
        parent::__construct($id, $email, $telefono, $direccion);
        $this->razonSocial = $razonSocial;
        $this->ruc = $ruc;
        $this->representanteLegal = $representanteLegal;
    }

    /*Getters*/
    public function getRazonSocial(): string { return $this->razonSocial; }
    public function getRuc(): string { return $this->ruc; }
    public function getRepresentanteLegal(): string { return $this->representanteLegal; }

    /*Setters*/
    public function setRazonSocial(string $razonSocial): void { $this->razonSocial = $razonSocial; }
    public function setRuc(string $ruc): void { $this->ruc = $ruc; }
    public function setRepresentanteLegal(string $representanteLegal): void { $this->representanteLegal = $representanteLegal; }
}