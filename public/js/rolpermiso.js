const createRolPermissionPanel = () => {
    Ext.define("App.model.RolPermiso", {
        extend: "Ext.data.Model",
        fields: [
            { name: "idRol", type: "int" },
            { name: "idPermiso", type: "int" }
        ]
    });

    let rolPermisoStore = Ext.create("Ext.data.Store", {
        storeId: "rolPermisoStore",
        model: "App.model.RolPermiso",
        proxy: {
            type: "rest",
            url: "/Api/rolpermiso.php",
            reader: { type: 'json', rootProperty: '' },
            writer: { type: 'json', rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Roles y Permisos",
        store: rolPermisoStore,
        itemId: "rolPermisoPanel",
        layout: "fit",
        columns: [
            { text: "ID Rol", flex: 1, dataIndex: "idRol", align: "center" },
            { text: "ID Permiso", flex: 1, dataIndex: "idPermiso", align: "center" }
        ]
    });

    return grid;
};
