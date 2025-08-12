DELIMITER $$

CREATE OR REPLACE PROCEDURE sp_create_rol(
    IN p_nombre VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO Rol(nombre)
    VALUES (p_nombre);

    SET @new_id = LAST_INSERT_ID();

    COMMIT;

    SELECT @new_id AS rol_id;
END$$

CREATE OR REPLACE PROCEDURE sp_update_rol(
    IN p_id INT,
    IN p_nombre VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE Rol
    SET nombre = p_nombre
    WHERE id = p_id;

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_delete_rol(
    IN p_id INT
)
BEGIN
    DELETE FROM Rol WHERE id = p_id;
END$$

CREATE OR REPLACE PROCEDURE sp_rol_list()
BEGIN
    SELECT id, nombre FROM Rol ORDER BY id;
END$$

CREATE OR REPLACE PROCEDURE sp_find_rol(
    IN p_id INT
)
BEGIN
    SELECT id, nombre FROM Rol WHERE id = p_id;
END$$

DELIMITER ;
