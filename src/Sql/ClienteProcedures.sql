-- ======================================
-- PROCEDIMIENTOS PARA CLIENTES
-- ======================================

USE sistema_gestion_comercial;

DELIMITER $$

-- Lista todos los clientes con su información extendida según tipo PersonaNatural o PersonaJuridica
CREATE OR REPLACE PROCEDURE sp_cliente_list()
BEGIN
    SELECT 
        c.id, c.email, c.telefono, c.direccion,
        pn.nombre, pn.apellido, pn.cedula,
        pj.razonSocial, pj.ruc, pj.representanteLegal
    FROM Cliente c
    LEFT JOIN PersonaNatural pn ON c.id = pn.cliente_id
    LEFT JOIN PersonaJuridica pj ON c.id = pj.cliente_id;
END$$

-- Busca un cliente por ID, mostrando datos base y de PersonaNatural o PersonaJuridica
CREATE OR REPLACE PROCEDURE sp_find_cliente(IN p_id INT)
BEGIN
    SELECT 
        c.id, c.email, c.telefono, c.direccion,
        pn.nombre, pn.apellido, pn.cedula,
        pj.razonSocial, pj.ruc, pj.representanteLegal
    FROM Cliente c
    LEFT JOIN PersonaNatural pn ON c.id = pn.cliente_id
    LEFT JOIN PersonaJuridica pj ON c.id = pj.cliente_id
    WHERE c.id = p_id;
END$$

-- Crea un nuevo Cliente y su detalle en PersonaNatural (transacción para integridad)
CREATE OR REPLACE PROCEDURE sp_create_persona_natural(
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(255),
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(100),
    IN p_cedula VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK; -- Revierte en caso de error
    END;

    START TRANSACTION;

    INSERT INTO Cliente(email, telefono, direccion) 
    VALUES (p_email, p_telefono, p_direccion);

    SET @new_id = LAST_INSERT_ID();

    INSERT INTO PersonaNatural(cliente_id, nombre, apellido, cedula)
    VALUES (@new_id, p_nombre, p_apellido, p_cedula);

    COMMIT;

    SELECT @new_id AS cliente_id;
END$$

-- Crea un nuevo Cliente y su detalle en PersonaJuridica (transacción para integridad)
CREATE OR REPLACE PROCEDURE sp_create_persona_juridica(
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
        ROLLBACK; -- Revierte en caso de error
    END;

    START TRANSACTION;

    INSERT INTO Cliente(email, telefono, direccion)
    VALUES (p_email, p_telefono, p_direccion);

    SET @new_id = LAST_INSERT_ID();

    INSERT INTO PersonaJuridica(cliente_id, razonSocial, ruc, representanteLegal)
    VALUES (@new_id, p_razonSocial, p_ruc, p_representanteLegal);

    COMMIT;

    SELECT @new_id AS cliente_id;
END$$

-- Actualiza un Cliente y su detalle en PersonaNatural (transacción para integridad)
CREATE OR REPLACE PROCEDURE sp_update_persona_natural(
    IN p_id INT,
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(255),
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(100),
    IN p_cedula VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK; -- Revierte en caso de error
    END;

    START TRANSACTION;

    UPDATE Cliente
    SET email = p_email,
        telefono = p_telefono,
        direccion = p_direccion
    WHERE id = p_id;

    UPDATE PersonaNatural
    SET nombre = p_nombre,
        apellido = p_apellido,
        cedula = p_cedula
    WHERE cliente_id = p_id;

    COMMIT;
END$$

-- Actualiza un Cliente y su detalle en PersonaJuridica (transacción para integridad)
CREATE OR REPLACE PROCEDURE sp_update_persona_juridica(
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
        ROLLBACK; -- Revierte en caso de error
    END;

    START TRANSACTION;

    UPDATE Cliente
    SET email = p_email,
        telefono = p_telefono,
        direccion = p_direccion
    WHERE id = p_id;

    UPDATE PersonaJuridica
    SET razonSocial = p_razonSocial,
        ruc = p_ruc,
        representanteLegal = p_representanteLegal
    WHERE cliente_id = p_id;

    COMMIT;
END$$

-- Elimina cliente y cascada automática por FK con ON DELETE CASCADE
CREATE OR REPLACE PROCEDURE sp_delete_cliente(IN p_id INT)
BEGIN
    DELETE FROM Cliente WHERE id = p_id;
END$$

DELIMITER ;

