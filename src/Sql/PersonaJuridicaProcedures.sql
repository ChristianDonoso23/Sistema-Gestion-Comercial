-- ======================================
-- PROCEDIMIENTOS PARA CLIENTES JURIDICOS
-- ======================================

USE sistema_gestion_comercial;

DELIMITER $$

CREATE OR REPLACE PROCEDURE sp_persona_juridica_list()
BEGIN
    SELECT c.id AS cliente_id, c.email, c.telefono, c.direccion,
           pj.razonSocial, pj.ruc, pj.representanteLegal
    FROM Cliente c
    JOIN PersonaJuridica pj ON pj.cliente_id = c.id
    ORDER BY pj.razonSocial;
END$$

CREATE OR REPLACE PROCEDURE sp_persona_juridica_find(IN p_id INT)
BEGIN
    SELECT c.id AS cliente_id, c.email, c.telefono, c.direccion,
           pj.razonSocial, pj.ruc, pj.representanteLegal
    FROM Cliente c
    JOIN PersonaJuridica pj ON pj.cliente_id = c.id
    WHERE c.id = p_id;
END$$

CREATE OR REPLACE PROCEDURE sp_persona_juridica_create(
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(255),
    IN p_razonSocial VARCHAR(100),
    IN p_ruc VARCHAR(20),
    IN p_representanteLegal VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;

    INSERT INTO Cliente(email, telefono, direccion)
    VALUES (p_email, p_telefono, p_direccion);

    SET @new_cliente_id = LAST_INSERT_ID();

    INSERT INTO PersonaJuridica(cliente_id, razonSocial, ruc, representanteLegal)
    VALUES (@new_cliente_id, p_razonSocial, p_ruc, p_representanteLegal);

    COMMIT;
    SELECT @new_cliente_id AS cliente_id;
END$$

CREATE OR REPLACE PROCEDURE sp_persona_juridica_update(
    IN p_id INT,
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(255),
    IN p_razonSocial VARCHAR(100),
    IN p_ruc VARCHAR(20),
    IN p_representanteLegal VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;

    UPDATE Cliente
    SET email = p_email, telefono = p_telefono, direccion = p_direccion
    WHERE id = p_id;

    UPDATE PersonaJuridica
    SET razonSocial = p_razonSocial, ruc = p_ruc, representanteLegal = p_representanteLegal
    WHERE cliente_id = p_id;

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_persona_juridica_delete(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;

    DELETE FROM PersonaJuridica WHERE cliente_id = p_id;
    DELETE FROM Cliente WHERE id = p_id;

    COMMIT;
    SELECT 1 AS OK;
END$$

DELIMITER ;
