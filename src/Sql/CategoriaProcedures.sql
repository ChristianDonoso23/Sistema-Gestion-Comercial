USE sistema_gestion_comercial;
DELIMITER $$

-- CREAR CATEGORÍA
CREATE OR REPLACE PROCEDURE sp_create_categoria(
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_estado VARCHAR(20),
    IN p_idPadre INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO Categoria(nombre, descripcion, estado, id_padre)
    VALUES (p_nombre, p_descripcion, p_estado, p_idPadre);

    SET @new_id = LAST_INSERT_ID();

    COMMIT;

    SELECT @new_id AS categoria_id;
END$$

-- ACTUALIZAR CATEGORÍA
CREATE OR REPLACE PROCEDURE sp_update_categoria(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_estado VARCHAR(20),
    IN p_idPadre INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE Categoria
    SET nombre = p_nombre,
        descripcion = p_descripcion,
        estado = p_estado,
        id_padre = p_idPadre
    WHERE id = p_id;

    COMMIT;
END$$

-- ELIMINAR CATEGORÍA
CREATE OR REPLACE PROCEDURE sp_delete_categoria(IN p_id INT)
BEGIN
    DELETE FROM Categoria WHERE id = p_id;
END$$

-- LISTAR TODAS LAS CATEGORÍAS
CREATE OR REPLACE PROCEDURE sp_categoria_list()
BEGIN
    SELECT id, nombre, descripcion, estado, id_padre AS idPadre FROM Categoria;
END$$

-- BUSCAR CATEGORÍA POR ID
CREATE OR REPLACE PROCEDURE sp_find_categoria(IN p_id INT)
BEGIN
    SELECT id, nombre, descripcion, estado, id_padre AS idPadre
    FROM Categoria
    WHERE id = p_id;
END$$

DELIMITER ;
ALTER TABLE Categoria AUTO_INCREMENT = 1;
