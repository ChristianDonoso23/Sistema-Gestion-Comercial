DELIMITER $$

-- Procedimiento para calcular el total de una venta sumando subtotales de DetalleVenta
CREATE OR REPLACE PROCEDURE sp_calcular_total_venta(
    IN p_idVenta INT,
    OUT p_total DECIMAL(10,2)
)
BEGIN
    SELECT IFNULL(SUM(subtotal), 0) INTO p_total
    FROM DetalleVenta
    WHERE idVenta = p_idVenta;
END$$

-- Crear Venta sin total (se calcula internamente)
CREATE OR REPLACE PROCEDURE sp_create_venta(
    IN p_fecha DATE,
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

    INSERT INTO Venta(fecha, idCliente, total, estado)
    VALUES (p_fecha, p_idCliente, 0, p_estado);

    SET @new_id = LAST_INSERT_ID();

    CALL sp_calcular_total_venta(@new_id, @total_calculado);

    UPDATE Venta SET total = @total_calculado WHERE id = @new_id;

    COMMIT;

    SELECT 
        @new_id AS venta_id,
        p_fecha AS fecha,
        p_idCliente AS idCliente,
        @total_calculado AS total,
        p_estado AS estado;
END$$

-- Actualizar Venta sin total (se recalcula)
CREATE OR REPLACE PROCEDURE sp_update_venta(
    IN p_id INT,
    IN p_fecha DATE,
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
        estado = p_estado
    WHERE id = p_id;

    CALL sp_calcular_total_venta(p_id, @total_calculado);

    UPDATE Venta SET total = @total_calculado WHERE id = p_id;

    COMMIT;

    SELECT ROW_COUNT() AS affected_rows;
END$$

-- Eliminar Venta
CREATE OR REPLACE PROCEDURE sp_delete_venta(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        RESIGNAL;
    END;

    DELETE FROM Venta WHERE id = p_id;
    SELECT ROW_COUNT() AS affected_rows;
END$$

-- Listar todas las Ventas
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

-- Buscar Venta por ID
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

-- Reiniciar AUTO_INCREMENT si lo necesitas
ALTER TABLE Venta AUTO_INCREMENT = 1;
