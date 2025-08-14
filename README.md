
# 💼 Sistema de Gestión Comercial para PYMEs – ITIN

> Proyecto Final de Aplicación de Tecnologías Web.

## 📌 Descripción

Este sistema permite a pequeñas y medianas empresas administrar de forma eficiente sus **clientes, productos, ventas, facturación y usuarios**, integrando tecnologías PHP puro en el back end con Ext JS en el front end. La persistencia de datos se realiza a través de consultas SQL parametrizadas y procedimientos almacenados (sin ORMs), utilizando MySQL o PostgreSQL como motor de base de datos.

---

## 🧱 Arquitectura del Proyecto

```
Sistema_Gestion_Comercial/
├── public/
│   ├── Api/
│   ├── js/             
│   └── Index.html         # Punto de entrada del cliente
│ 
└── src/
    ├── Config/            # Conexión a la base de datos (Database.php)
    ├── Entities/          # Entidades de negocio
    ├── Interfaces/        # Contratos de repositorios
    ├── Repositories/      # Acceso a datos
    ├── Sql/               # Scripts SQL para base de datos
    │   ├─ SGC.sql
    │   └── SGC(RespaldoBD).sql  #Respaldo para importar en la BD
    │ 
    └── vendor/            # Dependencias Composer
        ├── autoload.php
        ├── composer.json
        └── ...
```

---

## ⚙️ Tecnologías Usadas

- **PHP 8.x**
- **Ext JS 7+**
- **MySQL / PostgreSQL**
- **Visual Studio Code**
- **Postman** (para testing de API REST)
- **GitHub** (control de versiones)

---

## 🚀 Cómo Clonar y Levantar el Proyecto

### 1. Clona el repositorio
```bash
git clone https://github.com/ChristianDonoso23/Sistema_Gestion_Comercial.git
```

### 2. Configura el entorno backend
- Ajusta credenciales en `src/Config/Database.php` según tu motor de base de datos.

### 3. Restaura la base de datos

#### Opción A – phpMyAdmin
1. Crea una base de datos llamada `Sistema_Gestion_Comercial`.
2. Importa el archivo `src/Sql/SGC(RespaldoBD).sql` desde la pestaña **Importar**.

#### Opción B – Línea de comandos
```bash
mysql -u root -p Sistema_Gestion_Comercial < src/Sql/SGC(RespaldoBD).sql
```

### 4. Levanta los servicios
- Si usas XAMPP, activa **Apache** y **MySQL**.
- Accede a: `http://localhost/Sistema_Gestion_Comercial/public/`

---

## 🔁 Flujo de Venta (resumen)

1. Buscar cliente, agregar productos al carrito.
2. `POST /api/ventas` → crea la venta.
3. Backend:
   - Valida stock (`sp_validar_stock`)
   - Inserta cabecera y detalle
   - Descuenta inventario (`sp_descontar_stock`)
4. Devuelve `idVenta`
5. Emitir factura → `POST /api/facturas/{idVenta}` → PDF / firma / SRI

---

## 🔐 Seguridad

- Contraseñas hasheadas con `Argon2id`.
- Permisos y roles gestionables desde la interfaz administrativa.
- Acceso a endpoints protegido vía tokens (futuro).

---

## 📄 Licencia

Este proyecto es de uso académico y fue desarrollado como parte del módulo de integración de la carrera ITIN – ESPE.

---

## 👨‍💻 Autores

- Christian Donoso, Mateo Chanataxi, Mauri Tandazo  
- Universidad de las Fuerzas Armadas ESPE
