const createPermissionPanel = () => {
    Ext.define("App.model.Permiso", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "codigo", type: "string" }
        ]
    });

    let permisoStore = Ext.create("Ext.data.Store", {
        storeId: "permisoStore",
        model: "App.model.Permiso",
        proxy: {
            type: "rest",
            url: "/Api/permiso.php",
            reader: { type: "json", rootProperty: "" },
            writer: { type: "json", rootProperty: "", writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Permisos",
        store: permisoStore,
        itemId: "permisoPanel",
        layout: "fit",
        columns: [
            {
                text: "ID",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "id",
                align: "center"
            },
            {
                text: "Código",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "codigo",
                align: "center"
            }
        ],
        tbar: [
            {
                text: 'Eliminar Permiso',
                handler() {
                    const rec = this.up('grid').getSelection()[0];
                    if (!rec) return Ext.Msg.alert('Atención', 'Seleccione un Permiso para eliminar.');

                    Ext.Msg.confirm('Confirmar', '¿Está seguro de que desea eliminar el siguiente Permiso?', btn => {
                        if (btn === 'yes') {
                            permisoStore.remove(rec);
                            permisoStore.sync({
                                success: () => Ext.Msg.alert('Éxito', 'Permiso eliminado correctamente.'),
                                failure: () => Ext.Msg.alert('Error', 'No se pudo eliminar el Permiso.')
                            });
                        }
                    });
                }
            }
        ]
    });

    return grid;
};
