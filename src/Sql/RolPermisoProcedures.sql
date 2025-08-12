DELIMITER $$

CREATE OR REPLACE PROCEDURE sp_create_rolpermiso(
    IN p_idRol INT,
    IN p_idPermiso INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO RolPermiso(idRol, idPermiso)
    VALUES (p_idRol, p_idPermiso);

    COMMIT;
END$$

CREATE OR REPLACE PROCEDURE sp_delete_rolpermiso(
    IN p_idRol INT,
    IN p_idPermiso INT
)
BEGIN
    DELETE FROM RolPermiso
    WHERE idRol = p_idRol AND idPermiso = p_idPermiso;
END$$

CREATE OR REPLACE PROCEDURE sp_rolpermiso_list()
BEGIN
    SELECT idRol, idPermiso FROM RolPermiso;
END$$

CREATE OR REPLACE PROCEDURE sp_find_rolpermiso(
    IN p_idRol INT,
    IN p_idPermiso INT
)
BEGIN
    SELECT idRol, idPermiso FROM RolPermiso
    WHERE idRol = p_idRol AND idPermiso = p_idPermiso;
END$$

DELIMITER ;
