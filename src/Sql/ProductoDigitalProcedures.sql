USE sistema_gestion_comercial;

DELIMITER $$

-- Listar todos los productos digitales
CREATE OR REPLACE PROCEDURE sp_producto_digital_list()
BEGIN
    SELECT p.id AS producto_id, p.nombre, p.descripcion, p.precioUnitario, p.stock, p.idCategoria,
           pd.urlDescarga, pd.licencia
    FROM Producto p
    JOIN ProductoDigital pd ON pd.producto_id = p.id
    ORDER BY p.nombre;
END$$

-- Buscar producto digital por id
CREATE OR REPLACE PROCEDURE sp_producto_digital_find(IN p_id INT)
BEGIN
    SELECT p.id AS producto_id, p.nombre, p.descripcion, p.precioUnitario, p.stock, p.idCategoria,
           pd.urlDescarga, pd.licencia
    FROM Producto p
    JOIN ProductoDigital pd ON pd.producto_id = p.id
    WHERE p.id = p_id;
END$$

-- Crear nuevo producto digital
CREATE OR REPLACE PROCEDURE sp_producto_digital_create(
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_precioUnitario DECIMAL(10,2),
    IN p_stock INT,
    IN p_idCategoria INT,
    IN p_urlDescarga VARCHAR(255),
    IN p_licencia VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO Producto(nombre, descripcion, precioUnitario, stock, idCategoria)
    VALUES (p_nombre, p_descripcion, p_precioUnitario, p_stock, p_idCategoria);

    SET @new_producto_id = LAST_INSERT_ID();

    INSERT INTO ProductoDigital(producto_id, urlDescarga, licencia)
    VALUES (@new_producto_id, p_urlDescarga, p_licencia);

    COMMIT;

    SELECT @new_producto_id AS producto_id;
END$$

-- Actualizar producto digital
CREATE OR REPLACE PROCEDURE sp_producto_digital_update(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_precioUnitario DECIMAL(10,2),
    IN p_stock INT,
    IN p_idCategoria INT,
    IN p_urlDescarga VARCHAR(255),
    IN p_licencia VARCHAR(255)
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

-- Eliminar producto digital
CREATE OR REPLACE PROCEDURE sp_producto_digital_delete(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM ProductoDigital WHERE producto_id = p_id;
    DELETE FROM Producto WHERE id = p_id;

    COMMIT;

    SELECT 1 AS OK;
END$$

DELIMITER ;
