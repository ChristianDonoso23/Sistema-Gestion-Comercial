const createRolPanel = () => {
    Ext.define("App.model.Rol", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "nombre", type: "string" }
        ]
    });

    let rolStore = Ext.create("Ext.data.Store", {
        storeId: "rolStore",
        model: "App.model.Rol",
        proxy: {
            type: "rest",
            url: "/Api/rol.php",
            reader: { type: "json", rootProperty: "" },
            writer: { type: "json", rootProperty: "", writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Roles",
        store: rolStore,
        itemId: "rolPanel",
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
                text: "Nombre",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "nombre",
                align: "center"
            }
        ],
        tbar: [
            {
                text: 'Eliminar Rol',
                handler() {
                    const rec = this.up('grid').getSelection()[0];
                    if (!rec) return Ext.Msg.alert('Atención', 'Seleccione un Rol para eliminar.');

                    Ext.Msg.confirm('Confirmar', '¿Está seguro de que desea eliminar el siguiente Rol?', btn => {
                        if (btn === 'yes') {
                            rolStore.remove(rec);
                            rolStore.sync({
                                success: () => Ext.Msg.alert('Éxito', 'Rol eliminado correctamente.'),
                                failure: () => Ext.Msg.alert('Error', 'No se pudo eliminar el Rol.')
                            });
                        }
                    });
                }
            }
        ]
    });

    return grid;
};
