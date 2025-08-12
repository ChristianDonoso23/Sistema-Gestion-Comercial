DELIMITER $$

-- CREAR VENTA
CREATE OR REPLACE PROCEDURE sp_create_venta(
    IN p_fecha DATE,
    IN p_idCliente INT,
    IN p_total DECIMAL(10,2),
    IN p_estado VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO Venta(fecha, idCliente, total, estado)
    VALUES (p_fecha, p_idCliente, p_total, p_estado);

    SET @new_id = LAST_INSERT_ID();

    COMMIT;

    -- Devolver el registro completo recién creado
    SELECT 
        @new_id AS venta_id,
        p_fecha AS fecha,
        p_idCliente AS idCliente,
        p_total AS total,
        p_estado AS estado;
END$$

-- ACTUALIZAR VENTA
CREATE OR REPLACE PROCEDURE sp_update_venta(
    IN p_id INT,
    IN p_fecha DATE,
    IN p_total DECIMAL(10,2),
    IN p_idCliente INT,
    IN p_estado VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE Venta
    SET fecha = p_fecha,
        idCliente = p_idCliente,
        total = p_total,
        estado = p_estado
    WHERE id = p_id;

    COMMIT;
    
    -- Verificar que se actualizó correctamente
    SELECT ROW_COUNT() AS affected_rows;
END$$

-- ELIMINAR VENTA
CREATE OR REPLACE PROCEDURE sp_delete_venta(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        RESIGNAL;
    END;
    
    DELETE FROM Venta WHERE id = p_id;
    SELECT ROW_COUNT() AS affected_rows;
END$$

-- LISTAR TODAS LAS VENTAS
CREATE OR REPLACE PROCEDURE sp_venta_list()
BEGIN
    SELECT 
        id, 
        fecha, 
        idCliente, 
        total, 
        estado
    FROM Venta
    ORDER BY id DESC;
END$$

-- BUSCAR VENTA POR ID
CREATE OR REPLACE PROCEDURE sp_find_venta(IN p_id INT)
BEGIN
    SELECT 
        id, 
        fecha, 
        idCliente, 
        total, 
        estado
    FROM Venta
    WHERE id = p_id;
END$$

DELIMITER ;

ALTER TABLE Venta AUTO_INCREMENT = 1;