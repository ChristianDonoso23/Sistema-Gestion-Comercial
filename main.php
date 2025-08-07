<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';  // Ruta ajustada para tu autoload

use App\Entities\Categoria;
use App\Repositories\CategoriaRepository;

echo "=== INICIANDO PRUEBAS DE CATEGORÍA ===" . PHP_EOL;

$categoriaRepo = new CategoriaRepository();

// Variables para crear datos únicos
$timestamp = time();

// === Crear Categoría ===
echo PHP_EOL . "--- CREANDO CATEGORÍA ---" . PHP_EOL;

$nuevaCategoria = new Categoria(
    null,                           // id = null para creación
    "Categoría Test {$timestamp}",
    "Descripción de prueba para categoría",
    'activo',
    null                            // Sin categoría padre
);

echo "ID antes de crear: " . $nuevaCategoria->getId() . PHP_EOL;

$result = $categoriaRepo->create($nuevaCategoria);

echo "Resultado de creación: " . ($result ? 'true' : 'false') . PHP_EOL;
echo "ID después de crear: " . $nuevaCategoria->getId() . PHP_EOL;

// === Listar todas las categorías ===
echo PHP_EOL . "--- LISTANDO TODAS LAS CATEGORÍAS ---" . PHP_EOL;

$categorias = $categoriaRepo->findAll();

echo "Total categorías encontradas: " . count($categorias) . PHP_EOL;

foreach ($categorias as $cat) {
    echo "ID: {$cat->getId()}, Nombre: {$cat->getNombre()}, Estado: {$cat->getEstado()}, Id Padre: " . ($cat->getIdPadre() ?? 'NULL') . PHP_EOL;
}

echo PHP_EOL . "=== PRUEBAS DE CATEGORÍA COMPLETADAS ===" . PHP_EOL;
