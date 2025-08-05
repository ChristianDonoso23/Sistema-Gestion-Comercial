-- ======================================
-- PROCEDIMIENTOS PARA CLIENTES NATURALES
-- ======================================

USE sistema_gestion_comercial;
DELIMITER $$

CREATE OR REPLACE PROCEDURE sp_persona_natural_list()
BEGIN
    SELECT c.id AS cliente_id, c.email, c.telefono, c.direccion,
           pn.nombre, pn.apellido, pn.cedula
    FROM Cliente c
    JOIN PersonaNatural pn ON pn.cliente_id = c.id
    ORDER BY pn.nombre;
END$$

CREATE OR REPLACE PROCEDURE sp_persona_natural_find(IN p_id INT)
BEGIN
    SELECT c.id AS cliente_id, c.email, c.telefono, c.direccion,
           pn.nombre, pn.apellido, pn.cedula
    FROM Cliente c
    JOIN PersonaNatural pn ON pn.cliente_id = c.id
    WHERE c.id = p_id;
END$$

CREATE OR REPLACE PROCEDURE sp_persona_natural_create(
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
        ROLLBACK;
    END;
    START TRANSACTION;
    
    INSERT INTO Cliente(email, telefono, direccion)
    VALUES (p_email, p_telefono, p_direccion);
    
    SET @new_cliente_id = LAST_INSERT_ID();
    
    INSERT INTO PersonaNatural(cliente_id, nombre, apellido, cedula)
    VALUES (@new_cliente_id, p_nombre, p_apellido, p_cedula);
    
    COMMIT;
    SELECT @new_cliente_id AS cliente_id;
END$$

CREATE OR REPLACE PROCEDURE sp_persona_natural_update(
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
        ROLLBACK;
    END;
    START TRANSACTION;
    
    UPDATE Cliente
    SET email = p_email, telefono = p_telefono, direccion = p_direccion
    WHERE id = p_id;
    
    UPDATE PersonaNatural
    SET nombre = p_nombre, apellido = p_apellido, cedula = p_cedula
    WHERE cliente_id = p_id;
    
    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_persona_natural_delete(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;
    
    DELETE FROM PersonaNatural WHERE cliente_id = p_id;
    DELETE FROM Cliente WHERE id = p_id;
    
    COMMIT;
    
    SELECT 1 AS OK;
END$$

DELIMITER ;
