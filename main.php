<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Entities\Categoria;
use App\Entities\PersonaNatural;
use App\Entities\PersonaJuridica;
use App\Entities\ProductoFisico;
use App\Entities\ProductoDigital;

use App\Repositories\CategoriaRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\ProductoRepository;

echo "=== INICIANDO INSERCIONES MASIVAS ===" . PHP_EOL;

$categoriaRepo = new CategoriaRepository();
$clienteRepo = new ClienteRepository();
$productoRepo = new ProductoRepository();

// Categorías a insertar
$categorias = [
    new Categoria(null, 'Electrónica', 'Dispositivos electrónicos y gadgets', 'activo', null),
    new Categoria(null, 'Hogar', 'Productos para el hogar y decoración', 'activo', null),
    new Categoria(null, 'Libros', 'Libros físicos y digitales', 'activo', null),
];

// Insertar categorías
foreach ($categorias as $categoria) {
    $ok = $categoriaRepo->create($categoria);
    echo $ok ? "Categoría '{$categoria->getNombre()}' creada con ID: {$categoria->getId()}" : "Error creando categoría '{$categoria->getNombre()}'";
    echo PHP_EOL;
}

// Clientes Persona Natural
$personasNaturales = [
    new PersonaNatural(null, 'ana.martinez@gmail.com', '0987654321', 'Av. Siempre Viva 742', 'Ana', 'Martínez', '0102030405'),
    new PersonaNatural(null, 'carlos.ramirez@gmail.com', '0998877665', 'Calle Luna 123', 'Carlos', 'Ramírez', '0203040506'),
    new PersonaNatural(null, 'laura.gomez@gmail.com', '0981234567', 'Calle Sol 456', 'Laura', 'Gómez', '0304050607'),
];

// Insertar personas naturales
foreach ($personasNaturales as $pn) {
    $ok = $clienteRepo->create($pn);
    echo $ok ? "Persona Natural '{$pn->getNombre()} {$pn->getApellido()}' creada con ID: {$pn->getId()}" : "Error creando Persona Natural '{$pn->getNombre()} {$pn->getApellido()}'";
    echo PHP_EOL;
}

// Clientes Persona Jurídica
$personasJuridicas = [
    new PersonaJuridica(null, 'ventas.empresa1@gmail.com', '022334455', 'Av. Central 100', 'Empresa Uno S.A.', '1790012345001', 'Juan Pérez'),
    new PersonaJuridica(null, 'contacto.empresa2@gmail.com', '022334466', 'Av. Industrial 200', 'Empresa Dos S.A.', '1790012345002', 'María López'),
];

// Insertar personas jurídicas
foreach ($personasJuridicas as $pj) {
    $ok = $clienteRepo->create($pj);
    echo $ok ? "Persona Jurídica '{$pj->getRazonSocial()}' creada con ID: {$pj->getId()}" : "Error creando Persona Jurídica '{$pj->getRazonSocial()}'";
    echo PHP_EOL;
}

// Productos Físicos
$productosFisicos = [
    new ProductoFisico(null, 'Televisor 55"', 'Televisor Smart 4K UHD', 899.99, 50, $categorias[0]->getId(), 15.0, 75.0, 10.0, 5.0),
    new ProductoFisico(null, 'Refrigerador', 'Refrigerador No Frost 300L', 1200.00, 20, $categorias[1]->getId(), 80.0, 180.0, 70.0, 60.0),
];

// Insertar productos físicos
foreach ($productosFisicos as $pf) {
    $ok = $productoRepo->create($pf);
    echo $ok ? "Producto Físico '{$pf->getNombre()}' creado con ID: {$pf->getId()}" : "Error creando Producto Físico '{$pf->getNombre()}'";
    echo PHP_EOL;
}

// Productos Digitales
$productosDigitales = [
    new ProductoDigital(null, 'Ebook PHP Avanzado', 'Libro digital para programación PHP', 29.99, 1000, $categorias[2]->getId(), 'https://ebooks.com/php-avanzado', 'licencia-personal'),
    new ProductoDigital(null, 'Curso de JavaScript', 'Curso online para aprender JS desde cero', 49.99, 500, $categorias[2]->getId(), 'https://cursos.com/js-basico', 'licencia-curso'),
];

// Insertar productos digitales
foreach ($productosDigitales as $pd) {
    $ok = $productoRepo->create($pd);
    echo $ok ? "Producto Digital '{$pd->getNombre()}' creado con ID: {$pd->getId()}" : "Error creando Producto Digital '{$pd->getNombre()}'";
    echo PHP_EOL;
}

echo "=== INSERCIONES MASIVAS FINALIZADAS ===" . PHP_EOL;
