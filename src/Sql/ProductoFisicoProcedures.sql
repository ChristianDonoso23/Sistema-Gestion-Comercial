USE sistema_gestion_comercial;

DELIMITER $$

-- Listar todos los productos físicos
CREATE OR REPLACE PROCEDURE sp_producto_fisico_list()
BEGIN
    SELECT p.id AS producto_id, p.nombre, p.descripcion, p.precioUnitario, p.stock, p.idCategoria,
           pf.peso, pf.alto, pf.ancho, pf.profundidad
    FROM Producto p
    JOIN ProductoFisico pf ON pf.producto_id = p.id
    ORDER BY p.nombre;
END$$

-- Buscar producto físico por id
CREATE OR REPLACE PROCEDURE sp_producto_fisico_find(IN p_id INT)
BEGIN
    SELECT p.id AS producto_id, p.nombre, p.descripcion, p.precioUnitario, p.stock, p.idCategoria,
           pf.peso, pf.alto, pf.ancho, pf.profundidad
    FROM Producto p
    JOIN ProductoFisico pf ON pf.producto_id = p.id
    WHERE p.id = p_id;
END$$

-- Crear nuevo producto físico
CREATE OR REPLACE PROCEDURE sp_producto_fisico_create(
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

    SET @new_producto_id = LAST_INSERT_ID();

    INSERT INTO ProductoFisico(producto_id, peso, alto, ancho, profundidad)
    VALUES (@new_producto_id, p_peso, p_alto, p_ancho, p_profundidad);

    COMMIT;

    SELECT @new_producto_id AS producto_id;
END$$

-- Actualizar producto físico
CREATE OR REPLACE PROCEDURE sp_producto_fisico_update(
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

-- Eliminar producto físico
CREATE OR REPLACE PROCEDURE sp_producto_fisico_delete(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM ProductoFisico WHERE producto_id = p_id;
    DELETE FROM Producto WHERE id = p_id;

    COMMIT;

    SELECT 1 AS OK;
END$$

DELIMITER ;
