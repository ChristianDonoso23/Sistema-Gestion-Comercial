DELIMITER $$

CREATE OR REPLACE PROCEDURE sp_create_permiso(
    IN p_codigo VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO Permiso(codigo)
    VALUES (p_codigo);

    SET @new_id = LAST_INSERT_ID();

    COMMIT;

    SELECT @new_id AS permiso_id;
END$$

CREATE OR REPLACE PROCEDURE sp_update_permiso(
    IN p_id INT,
    IN p_codigo VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE Permiso
    SET codigo = p_codigo
    WHERE id = p_id;

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_delete_permiso(
    IN p_id INT
)
BEGIN
    DELETE FROM Permiso WHERE id = p_id;
END$$

CREATE OR REPLACE PROCEDURE sp_permiso_list()
BEGIN
    SELECT id, codigo FROM Permiso ORDER BY id;
END$$

CREATE OR REPLACE PROCEDURE sp_find_permiso(
    IN p_id INT
)
BEGIN
    SELECT id, codigo FROM Permiso WHERE id = p_id;
END$$

DELIMITER ;
