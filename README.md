# 💼 Sistema de Gestión Comercial para PYMEs – ITIN

> Proyecto Final de Aplicación de Tecnologías Web – ITIN, ESPE.

## 📌 Descripción

Sistema web que permite a pequeñas y medianas empresas gestionar de forma eficiente sus **clientes, productos, ventas, facturación y usuarios**, combinando un **back end en PHP puro** con un **front end en Ext JS**. Se excluye el uso de ORMs para priorizar el acceso a base de datos mediante **consultas SQL parametrizadas** y **procedimientos almacenados**, compatibles con **MySQL** o **PostgreSQL**.

## 🧱 Arquitectura del Proyecto

```
Sistema_Gestion_Comercial/
├── public/                  # Frontend base (HTML inicial)
│   └── index.html
├── src/
│   ├── Config/              # Configuración de base de datos (Database.php)
│   ├── Entities/            # Clases de dominio (Cliente, Producto, etc.)
│   ├── Interfaces/          # Interfaces para repositorios y servicios
│   ├── Repositories/        # Acceso a datos: consultas y lógica de persistencia
│   ├── Sql/                 # Scripts SQL y procedimientos almacenados
│   │   └── SGC.sql
│   └── vendor/              # Dependencias instaladas con Composer
│       ├── composer/
│       ├── autoload.php
│       ├── composer.json
│       ├── composer.lock
│       └── README.md
```

## ⚙️ Tecnologías Usadas

- **PHP 8.x**
- **Ext JS 7+**
- **MySQL / PostgreSQL**
- **Composer**
- **XAMPP / Laragon / Docker (opcional)**
- **Postman** (para pruebas de API REST)
- **GitHub** (control de versiones)

## 🚀 Cómo Clonar y Levantar el Proyecto

### 1. Clonar el repositorio

```bash
git clone https://github.com/ChristianDonoso23/Sistema_Gestion_Comercial.git
cd Sistema_Gestion_Comercial
```

### 2. Configurar el backend

- Ve a `src/Config/Database.php` y ajusta tus credenciales de base de datos:

```php
$host = 'localhost';
$dbname = 'sistema_gestion_comercial';
$username = 'root';
$password = '';
```

### 3. Crear la base de datos

- Abre tu gestor (phpMyAdmin, MySQL Workbench o terminal) y ejecuta:

```bash
mysql -u root -p < src/Sql/SGC.sql
```

### 4. Iniciar servidor local

- Activa Apache y MySQL desde XAMPP o similar.
- Abre en el navegador:  
  `http://localhost/Sistema_Gestion_Comercial/public/index.html`

## 🔁 Flujo de Venta

1. Buscar cliente y añadir productos al carrito.
2. `POST /api/ventas` → crea venta.
3. Valida stock (`sp_validar_stock`), guarda venta, descuenta inventario (`sp_descontar_stock`).
4. Devuelve `idVenta`.
5. Emitir factura vía `POST /api/facturas/{idVenta}`.

## 🔐 Seguridad

- Contraseñas con **Argon2id**.
- Roles y permisos administrables desde UI.
- (Próximamente) Protección de endpoints con tokens.

## 👨‍💻 Autores

- Christian Donoso  
- Mateo Chanataxi  
- Mauri Tandazo  
**Universidad de las Fuerzas Armadas – ESPE**

