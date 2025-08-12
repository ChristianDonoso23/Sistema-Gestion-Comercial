DELIMITER $$

CREATE OR REPLACE PROCEDURE sp_create_usuario(
    IN p_username VARCHAR(50),
    IN p_passwordHash VARCHAR(255),
    IN p_estado VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO Usuario(username, passwordHash, estado)
    VALUES (p_username, p_passwordHash, p_estado);

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_update_usuario(
    IN p_id INT,
    IN p_username VARCHAR(50),
    IN p_passwordHash VARCHAR(255),
    IN p_estado VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE Usuario
    SET username = p_username,
        passwordHash = p_passwordHash,
        estado = p_estado
    WHERE id = p_id;

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_delete_usuario(
    IN p_id INT
)
BEGIN
    DELETE FROM Usuario WHERE id = p_id;
END$$

CREATE OR REPLACE PROCEDURE sp_usuario_list()
BEGIN
    SELECT id, username, passwordHash, estado FROM Usuario;
END$$

CREATE OR REPLACE PROCEDURE sp_find_usuario(
    IN p_id INT
)
BEGIN
    SELECT id, username, passwordHash, estado
    FROM Usuario
    WHERE id = p_id;
END$$

DELIMITER ;
