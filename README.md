
# 💼 Sistema de Gestión Comercial para PYMEs – ITIN

> Proyecto Final de Aplicación de Tecnologías Web.

## 📌 Descripción

Este sistema permite a pequeñas y medianas empresas administrar de forma eficiente sus **clientes, productos, ventas, facturación y usuarios**, integrando tecnologías PHP puro en el back end con Ext JS en el front end. La persistencia de datos se realiza a través de consultas SQL parametrizadas y procedimientos almacenados (sin ORMs), utilizando MySQL o PostgreSQL como motor de base de datos.

---

## 🧱 Arquitectura del Proyecto

```
Sistema_Gestion_Comercial/
├── backend/
│   ├── config/           # Conexión a la base de datos
│   ├── controllers/      # Controladores de API REST
│   ├── models/           # Lógica de negocio y acceso a datos
│   ├── services/         # Validaciones, lógica de aplicación
│   ├── database/         # Scripts SQL y procedimientos almacenados
│   ├── public/           # Punto de entrada (index.php)
├── frontend/
│   ├── app/              # Componentes Ext JS
│   ├── resources/        # Estilos, íconos, assets
│   ├── index.html        # Archivo raíz
└── README.md
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
- Copia `backend/config/db.php` y ajusta tus credenciales de base de datos.
- Importa el archivo SQL desde `backend/database/init.sql` en tu gestor (MySQL/PostgreSQL).

### 3. Inicia servicios locales
Si usas XAMPP:
- Activa **Apache** y **MySQL**.
- Accede a: `http://localhost/gestion-comercial/backend/public/`

### 4. Levanta el frontend Ext JS
Si ya tienes Sencha Cmd:
```bash
cd frontend
sencha app watch
```

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
