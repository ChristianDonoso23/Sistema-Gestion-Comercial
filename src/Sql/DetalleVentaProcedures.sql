DELIMITER $$

CREATE OR REPLACE PROCEDURE sp_create_detalle_venta(
    IN p_idVenta INT,
    IN p_lineNumber INT,
    IN p_idProducto INT,
    IN p_cantidad INT,
    IN p_precioUnitario DECIMAL(10,2),
    IN p_subtotal DECIMAL(10,2)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO DetalleVenta(idVenta, lineNumber, idProducto, cantidad, precioUnitario, subtotal)
    VALUES (p_idVenta, p_lineNumber, p_idProducto, p_cantidad, p_precioUnitario, p_subtotal);

    -- Actualizar total en Venta sumando subtotales de DetalleVenta
    UPDATE Venta
    SET total = (
        SELECT IFNULL(SUM(subtotal), 0)
        FROM DetalleVenta
        WHERE idVenta = p_idVenta
    )
    WHERE id = p_idVenta;

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_update_detalle_venta(
    IN p_idVenta INT,
    IN p_lineNumber INT,
    IN p_idProducto INT,
    IN p_cantidad INT,
    IN p_precioUnitario DECIMAL(10,2),
    IN p_subtotal DECIMAL(10,2)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE DetalleVenta
    SET idProducto = p_idProducto,
        cantidad = p_cantidad,
        precioUnitario = p_precioUnitario,
        subtotal = p_subtotal
    WHERE idVenta = p_idVenta AND lineNumber = p_lineNumber;

    -- Actualizar total en Venta sumando subtotales de DetalleVenta
    UPDATE Venta
    SET total = (
        SELECT IFNULL(SUM(subtotal), 0)
        FROM DetalleVenta
        WHERE idVenta = p_idVenta
    )
    WHERE id = p_idVenta;

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_delete_detalle_venta(
    IN p_idVenta INT,
    IN p_lineNumber INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    DELETE FROM DetalleVenta WHERE idVenta = p_idVenta AND lineNumber = p_lineNumber;

    -- Actualizar total en Venta sumando subtotales de DetalleVenta
    UPDATE Venta
    SET total = (
        SELECT IFNULL(SUM(subtotal), 0)
        FROM DetalleVenta
        WHERE idVenta = p_idVenta
    )
    WHERE id = p_idVenta;

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_detalle_venta_list()
BEGIN
    SELECT idVenta, lineNumber, idProducto, cantidad, precioUnitario, subtotal FROM DetalleVenta;
END$$

CREATE OR REPLACE PROCEDURE sp_find_detalle_venta(
    IN p_idVenta INT,
    IN p_lineNumber INT
)
BEGIN
    SELECT idVenta, lineNumber, idProducto, cantidad, precioUnitario, subtotal
    FROM DetalleVenta
    WHERE idVenta = p_idVenta AND lineNumber = p_lineNumber;
END$$

DELIMITER ;
