<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Entities\PersonaNatural;
use App\Entities\PersonaJuridica;
use App\Repositories\ClienteRepository;

echo "=== INICIANDO PRUEBAS DE CLIENTES ===" . PHP_EOL;

// Generar datos únicos usando timestamp
$timestamp = time();
$random = rand(1000, 9999);

try {
    $clienteRepo = new ClienteRepository();
    echo "✅ Conexión a base de datos establecida" . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// === Cliente Persona Natural ===
echo PHP_EOL . "--- CREANDO PERSONA NATURAL ---" . PHP_EOL;

$clienteNatural = new PersonaNatural(
    null,
    "juan.perez_{$timestamp}@example.com",  // Email único
    '0999999999',
    'Av. Siempre Viva 123',
    'Juan',
    'Perez',
    "172345{$random}"  // Cédula única
);

echo "ID antes de crear: " . $clienteNatural->getId() . PHP_EOL;
echo "Email: " . $clienteNatural->getEmail() . PHP_EOL;
echo "Cédula: " . $clienteNatural->getCedula() . PHP_EOL;

$result1 = $clienteRepo->create($clienteNatural);

echo "Resultado de creación: " . ($result1 ? 'true' : 'false') . PHP_EOL;
echo "ID después de crear: " . $clienteNatural->getId() . PHP_EOL;

if ($result1) {
    echo "✅ Cliente Natural creado con ID: " . $clienteNatural->getId() . PHP_EOL;
} else {
    echo "❌ Error al crear cliente natural." . PHP_EOL;
}

// === Cliente Persona Jurídica ===
echo PHP_EOL . "--- CREANDO PERSONA JURÍDICA ---" . PHP_EOL;

$clienteJuridico = new PersonaJuridica(
    null,
    "empresa_{$timestamp}@ejemplo.com",  // Email único
    '022345678',
    'Av. Empresarial 456',
    "ACME Corp S.A. {$timestamp}",  // Razón social única
    "179999999{$random}",  // RUC único
    'Carlos López'
);

echo "ID antes de crear: " . $clienteJuridico->getId() . PHP_EOL;
echo "Email: " . $clienteJuridico->getEmail() . PHP_EOL;
echo "RUC: " . $clienteJuridico->getRuc() . PHP_EOL;

$result2 = $clienteRepo->create($clienteJuridico);

echo "Resultado de creación: " . ($result2 ? 'true' : 'false') . PHP_EOL;
echo "ID después de crear: " . $clienteJuridico->getId() . PHP_EOL;

if ($result2) {
    echo "✅ Cliente jurídico creado exitosamente con ID: " . $clienteJuridico->getId() . PHP_EOL;
} else {
    echo "❌ Error al crear cliente jurídico." . PHP_EOL;
}

// === VERIFICAR EN BASE DE DATOS ===
echo PHP_EOL . "--- VERIFICANDO EN BASE DE DATOS ---" . PHP_EOL;

$clientes = $clienteRepo->findAll();
echo "Total de clientes encontrados: " . count($clientes) . PHP_EOL;

foreach ($clientes as $cliente) {
    if ($cliente instanceof PersonaNatural) {
        echo "Persona Natural - ID: " . $cliente->getId() . 
             ", Nombre: " . $cliente->getNombre() . " " . $cliente->getApellido() . 
             ", Email: " . $cliente->getEmail() . PHP_EOL;
    } elseif ($cliente instanceof PersonaJuridica) {
        echo "Persona Jurídica - ID: " . $cliente->getId() . 
             ", Razón Social: " . $cliente->getRazonSocial() . 
             ", Email: " . $cliente->getEmail() . PHP_EOL;
    }
}

echo PHP_EOL . "=== PRUEBAS COMPLETADAS ===" . PHP_EOL;