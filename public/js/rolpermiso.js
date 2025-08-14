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
            reader: { type: "json", rootProperty: "" },
            writer: { type: "json", rootProperty: "", writeAllFields: true },
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
            {
                text: "ID Rol",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "idRol",
                align: "center"
            },
            {
                text: "ID Permiso",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "idPermiso",
                align: "center"
            }
        ],
        tbar: [
            {
                text: 'Eliminar Rol-Permiso',
                handler() {
                    const rec = this.up('grid').getSelection()[0];
                    if (!rec) return Ext.Msg.alert('Atención', 'Seleccione un Rol-Permiso para eliminar.');

                    Ext.Msg.confirm('Confirmar', '¿Está seguro de que desea eliminar el siguiente Rol-Permiso?', btn => {
                        if (btn === 'yes') {
                            permisoStore.remove(rec);
                            permisoStore.sync({
                                success: () => Ext.Msg.alert('Éxito', 'Rol-Permiso eliminado correctamente.'),
                                failure: () => Ext.Msg.alert('Error', 'No se pudo eliminar el Rol-Permiso.')
                            });
                        }
                    });
                }
            }
        ]
    });

    return grid;
};
