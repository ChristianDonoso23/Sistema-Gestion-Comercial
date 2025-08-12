DELIMITER $$

CREATE OR REPLACE PROCEDURE sp_create_factura(
    IN p_idVenta INT,
    IN p_numero INT,
    IN p_claveAcceso VARCHAR(50),
    IN p_fechaEmision DATE,
    IN p_estado VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO Factura(idVenta, numero, claveAcceso, fechaEmision, estado)
    VALUES (p_idVenta, p_numero, p_claveAcceso, p_fechaEmision, p_estado);

    SET @new_id = LAST_INSERT_ID();

    COMMIT;

    SELECT @new_id AS factura_id;
END$$

CREATE OR REPLACE PROCEDURE sp_update_factura(
    IN p_id INT,
    IN p_idVenta INT,
    IN p_numero INT,
    IN p_claveAcceso VARCHAR(50),
    IN p_fechaEmision DATE,
    IN p_estado VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE Factura
    SET idVenta = p_idVenta,
        numero = p_numero,
        claveAcceso = p_claveAcceso,
        fechaEmision = p_fechaEmision,
        estado = p_estado
    WHERE id = p_id;

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_delete_factura(IN p_id INT)
BEGIN
    DELETE FROM Factura WHERE id = p_id;
END$$

CREATE OR REPLACE PROCEDURE sp_factura_list()
BEGIN
    SELECT id, idVenta, numero, claveAcceso, fechaEmision, estado FROM Factura;
END$$

CREATE OR REPLACE PROCEDURE sp_find_factura(IN p_id INT)
BEGIN
    SELECT id, idVenta, numero, claveAcceso, fechaEmision, estado
    FROM Factura
    WHERE id = p_id;
END$$

DELIMITER ;
