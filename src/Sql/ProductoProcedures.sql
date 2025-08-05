USE sistema_gestion_comercial;
DELIMITER $$

-- LISTAR TODOS LOS PRODUCTOS CON SUS DETALLES (físico o digital)
CREATE OR REPLACE PROCEDURE sp_producto_list()
BEGIN
    SELECT
        p.id, p.nombre, p.descripcion, p.precioUnitario, p.stock, p.idCategoria,
        pf.peso, pf.alto, pf.ancho, pf.profundidad,
        pd.urlDescarga, pd.licencia
    FROM Producto p
    LEFT JOIN ProductoFisico pf ON pf.producto_id = p.id
    LEFT JOIN ProductoDigital pd ON pd.producto_id = p.id;
END$$

-- BUSCAR PRODUCTO POR ID
CREATE OR REPLACE PROCEDURE sp_find_producto(IN p_id INT)
BEGIN
    SELECT
        p.id, p.nombre, p.descripcion, p.precioUnitario, p.stock, p.idCategoria,
        pf.peso, pf.alto, pf.ancho, pf.profundidad,
        pd.urlDescarga, pd.licencia
    FROM Producto p
    LEFT JOIN ProductoFisico pf ON pf.producto_id = p.id
    LEFT JOIN ProductoDigital pd ON pd.producto_id = p.id
    WHERE p.id = p_id;
END$$

-- CREAR PRODUCTO FISICO
CREATE OR REPLACE PROCEDURE sp_create_producto_fisico(
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_precioUnitario DECIMAL(10,2),
    IN p_stock INT,
    IN p_idCategoria INT,
    IN p_peso DECIMAL(10,2),
    IN p_alto DECIMAL(10,2),
    IN p_ancho DECIMAL(10,2),
    IN p_profundidad DECIMAL(10,2)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO Producto(nombre, descripcion, precioUnitario, stock, idCategoria)
    VALUES (p_nombre, p_descripcion, p_precioUnitario, p_stock, p_idCategoria);

    SET @new_id = LAST_INSERT_ID();

    INSERT INTO ProductoFisico(producto_id, peso, alto, ancho, profundidad)
    VALUES (@new_id, p_peso, p_alto, p_ancho, p_profundidad);

    COMMIT;

    SELECT @new_id AS producto_id;
END$$

-- CREAR PRODUCTO DIGITAL
CREATE OR REPLACE PROCEDURE sp_create_producto_digital(
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_precioUnitario DECIMAL(10,2),
    IN p_stock INT,
    IN p_idCategoria INT,
    IN p_urlDescarga VARCHAR(255),
    IN p_licencia VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO Producto(nombre, descripcion, precioUnitario, stock, idCategoria)
    VALUES (p_nombre, p_descripcion, p_precioUnitario, p_stock, p_idCategoria);

    SET @new_id = LAST_INSERT_ID();

    INSERT INTO ProductoDigital(producto_id, urlDescarga, licencia)
    VALUES (@new_id, p_urlDescarga, p_licencia);

    COMMIT;

    SELECT @new_id AS producto_id;
END$$

-- ACTUALIZAR PRODUCTO FISICO
CREATE OR REPLACE PROCEDURE sp_update_producto_fisico(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_precioUnitario DECIMAL(10,2),
    IN p_stock INT,
    IN p_idCategoria INT,
    IN p_peso DECIMAL(10,2),
    IN p_alto DECIMAL(10,2),
    IN p_ancho DECIMAL(10,2),
    IN p_profundidad DECIMAL(10,2)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE Producto
    SET nombre = p_nombre,
        descripcion = p_descripcion,
        precioUnitario = p_precioUnitario,
        stock = p_stock,
        idCategoria = p_idCategoria
    WHERE id = p_id;

    UPDATE ProductoFisico
    SET peso = p_peso,
        alto = p_alto,
        ancho = p_ancho,
        profundidad = p_profundidad
    WHERE producto_id = p_id;

    COMMIT;
END$$

-- ACTUALIZAR PRODUCTO DIGITAL
CREATE OR REPLACE PROCEDURE sp_update_producto_digital(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_precioUnitario DECIMAL(10,2),
    IN p_stock INT,
    IN p_idCategoria INT,
    IN p_urlDescarga VARCHAR(255),
    IN p_licencia VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE Producto
    SET nombre = p_nombre,
        descripcion = p_descripcion,
        precioUnitario = p_precioUnitario,
        stock = p_stock,
        idCategoria = p_idCategoria
    WHERE id = p_id;

    UPDATE ProductoDigital
    SET urlDescarga = p_urlDescarga,
        licencia = p_licencia
    WHERE producto_id = p_id;

    COMMIT;
END$$

-- ELIMINAR PRODUCTO (ON DELETE CASCADE elimina detalles)
CREATE OR REPLACE PROCEDURE sp_delete_producto(IN p_id INT)
BEGIN
    DELETE FROM Producto WHERE id = p_id;
END$$

DELIMITER ;
