-- Active: 1752793183249@@127.0.0.1@3306@sistema_gestion_comercial
CREATE DATABASE Sistema_Gestion_Comercial
    DEFAULT CHARACTER SET = 'utf8mb4';

USE Sistema_Gestion_Comercial;

CREATE TABLE Cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(15) NOT NULL,
    direccion VARCHAR(255) NOT NULL
);

CREATE TABLE PersonaNatural (
    cliente_id INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    FOREIGN KEY (cliente_id) REFERENCES Cliente(id) ON DELETE CASCADE
);

CREATE TABLE PersonaJuridica (
    cliente_id INT PRIMARY KEY,
    razonSocial VARCHAR(100) NOT NULL,
    ruc VARCHAR(20) NOT NULL UNIQUE,
    representanteLegal VARCHAR(100) NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES Cliente(id) ON DELETE CASCADE
);

CREATE TABLE Categoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL,
    id_padre INT,
    FOREIGN KEY (id_padre) REFERENCES Categoria(id)
);

CREATE TABLE Producto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precioUnitario DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    idCategoria INT NOT NULL,
    FOREIGN KEY (idCategoria) REFERENCES Categoria(id) ON DELETE CASCADE
);

CREATE TABLE ProductoFisico (
    producto_id INT PRIMARY KEY,
    peso DECIMAL(10,2) NOT NULL,
    alto DECIMAL(10,2) NOT NULL,
    ancho DECIMAL(10,2) NOT NULL,
    profundidad DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES Producto(id) ON DELETE CASCADE
);

CREATE TABLE ProductoDigital (
    producto_id INT PRIMARY KEY,
    urlDescarga VARCHAR(255) NOT NULL,
    licencia VARCHAR(100) NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES Producto(id) ON DELETE CASCADE
);

CREATE TABLE Venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    idCliente INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('borrador', 'emitida', 'anulada') NOT NULL,
    FOREIGN KEY (idCliente) REFERENCES Cliente(id) ON DELETE CASCADE
);

CREATE TABLE DetalleVenta (
    idVenta INT NOT NULL,
    lineNumber INT NOT NULL,
    idProducto INT NOT NULL,
    cantidad INT NOT NULL,
    precioUnitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (idVenta, lineNumber),
    FOREIGN KEY (idVenta) REFERENCES Venta(id) ON DELETE CASCADE,
    FOREIGN KEY (idProducto) REFERENCES Producto(id) ON DELETE CASCADE
);

CREATE TABLE Factura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idVenta INT NOT NULL,
    numero INT NOT NULL,
    claveAcceso VARCHAR(50) NOT NULL UNIQUE,
    fechaEmision DATE NOT NULL,
    estado ENUM('borrador', 'emitida', 'anulada') NOT NULL,
    FOREIGN KEY (idVenta) REFERENCES Venta(id) ON DELETE CASCADE
);

CREATE TABLE Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL
);

CREATE TABLE Rol (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Permiso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE RolPermiso (
    idRol INT NOT NULL,
    idPermiso INT NOT NULL,
    PRIMARY KEY (idRol, idPermiso),
    FOREIGN KEY (idRol) REFERENCES Rol(id) ON DELETE CASCADE,
    FOREIGN KEY (idPermiso) REFERENCES Permiso(id) ON DELETE CASCADE
);
